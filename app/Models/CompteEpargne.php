<?php
namespace App\Models;

use App\Core\Model;
use PDO;
use RuntimeException;
use Throwable;

class CompteEpargne extends Model
{
    // Types de compte réels CECAM : DAV (dépôt à vue), DAT (dépôt à terme), PLE (plan d'épargne)
    public const TYPES = ['DAV', 'DAT', 'PLE'];

    public function findBySocietaire(int $societaireId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM comptes_epargne WHERE societaire_id = ? ORDER BY date_ouverture DESC'
        );
        $stmt->execute([$societaireId]);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT ce.*, s.nom, s.prenom, s.code_societaire
             FROM comptes_epargne ce
             JOIN societaires s ON s.id = ce.societaire_id
             WHERE ce.id = ? LIMIT 1'
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function paginate(string $search, int $limit, int $offset): array
    {
        [$where, $params] = $this->buildSearch($search);

        $stmt = $this->db->prepare(
            "SELECT ce.*, s.nom, s.prenom, s.code_societaire
             FROM comptes_epargne ce
             JOIN societaires s ON s.id = ce.societaire_id
             $where
             ORDER BY ce.date_ouverture DESC
             LIMIT ? OFFSET ?"
        );

        $position = 1;
        foreach ($params as $value) {
            $stmt->bindValue($position++, $value, PDO::PARAM_STR);
        }
        $stmt->bindValue($position++, $limit, PDO::PARAM_INT);
        $stmt->bindValue($position++, $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function count(string $search): int
    {
        [$where, $params] = $this->buildSearch($search);

        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM comptes_epargne ce
             JOIN societaires s ON s.id = ce.societaire_id
             $where"
        );
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO comptes_epargne
                (societaire_id, numero_compte, type_compte, solde, taux_interet, date_ouverture, date_echeance, statut)
             VALUES
                (:societaire_id, :numero_compte, :type_compte, 0, :taux_interet, CURRENT_DATE, :date_echeance, "actif")'
        );

        $stmt->execute([
            'societaire_id' => $data['societaire_id'],
            'numero_compte' => 'TMP-' . uniqid(),
            'type_compte'   => $data['type_compte'],
            'taux_interet'  => $data['taux_interet'],
            'date_echeance' => $data['date_echeance'] ?: null,
        ]);

        $id = (int) $this->db->lastInsertId();
        $numero = $data['type_compte'] . '-' . str_pad((string) $id, 5, '0', STR_PAD_LEFT);

        $update = $this->db->prepare('UPDATE comptes_epargne SET numero_compte = ? WHERE id = ?');
        $update->execute([$numero, $id]);

        return $id;
    }

    public function historique(int $compteId, int $limit, int $offset): array
    {
        $stmt = $this->db->prepare(
            'SELECT m.*, u.nom AS agent_nom
             FROM mouvements_epargne m
             LEFT JOIN users u ON u.id = m.effectue_par
             WHERE m.compte_id = ?
             ORDER BY m.date_mouvement DESC
             LIMIT ? OFFSET ?'
        );
        $stmt->bindValue(1, $compteId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->bindValue(3, $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function countHistorique(int $compteId): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM mouvements_epargne WHERE compte_id = ?');
        $stmt->execute([$compteId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Dépôt sur un compte. Transaction PDO pour garantir la cohérence solde / mouvement.
     */
    public function deposer(int $compteId, float $montant, int $agentId): array
    {
        if ($montant <= 0) {
            throw new RuntimeException("Le montant du dépôt doit être positif.");
        }

        $compte = $this->find($compteId);
        if (!$compte) {
            throw new RuntimeException('Compte introuvable.');
        }

        $this->db->beginTransaction();
        try {
            $nouveauSolde = (float) $compte['solde'] + $montant;

            $update = $this->db->prepare('UPDATE comptes_epargne SET solde = ? WHERE id = ?');
            $update->execute([$nouveauSolde, $compteId]);

            $mouvement = $this->db->prepare(
                'INSERT INTO mouvements_epargne (compte_id, type_mouvement, montant, solde_apres, effectue_par)
                 VALUES (?, "depot", ?, ?, ?)'
            );
            $mouvement->execute([$compteId, $montant, $nouveauSolde, $agentId]);

            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }

        return ['solde' => $nouveauSolde];
    }

    public function retirer(int $compteId, float $montant, int $agentId): array
    {
        if ($montant <= 0) {
            throw new RuntimeException('Le montant du retrait doit être positif.');
        }

        $compte = $this->find($compteId);
        if (!$compte) {
            throw new RuntimeException('Compte introuvable.');
        }

        if ($compte['type_compte'] === 'DAT' && !empty($compte['date_echeance']) && $compte['date_echeance'] > date('Y-m-d')) {
            throw new RuntimeException(
                'Ce compte est un dépôt à terme (DAT) non échu (échéance le ' . $compte['date_echeance'] . '). Retrait impossible avant cette date.'
            );
        }

        if ($montant > (float) $compte['solde']) {
            throw new RuntimeException('Solde insuffisant pour ce retrait.');
        }

        $this->db->beginTransaction();
        try {
            $nouveauSolde = (float) $compte['solde'] - $montant;

            $update = $this->db->prepare('UPDATE comptes_epargne SET solde = ? WHERE id = ?');
            $update->execute([$nouveauSolde, $compteId]);

            $mouvement = $this->db->prepare(
                'INSERT INTO mouvements_epargne (compte_id, type_mouvement, montant, solde_apres, effectue_par)
                 VALUES (?, "retrait", ?, ?, ?)'
            );
            $mouvement->execute([$compteId, $montant, $nouveauSolde, $agentId]);

            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }

        return ['solde' => $nouveauSolde];
    }

    private function buildSearch(string $search): array
    {
        if ($search === '') {
            return ['', []];
        }

        $where = 'WHERE ce.numero_compte LIKE ? OR s.nom LIKE ? OR s.prenom LIKE ? OR s.code_societaire LIKE ?';
        $like = "%$search%";

        return [$where, [$like, $like, $like, $like]];
    }
}