<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Societaire extends Model
{
    public function paginate(string $search, int $limit, int $offset): array
    {
        [$where, $params] = $this->buildSearch($search);

        $stmt = $this->db->prepare("SELECT * FROM societaires $where ORDER BY created_at DESC LIMIT ? OFFSET ?");

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

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM societaires $where");
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM societaires WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO societaires (code_societaire, nom, prenom, cin, telephone, adresse, date_naissance, photo, piece_identite, date_adhesion, created_by)
             VALUES (:code_societaire, :nom, :prenom, :cin, :telephone, :adresse, :date_naissance, :photo, :piece_identite, CURRENT_DATE, :created_by)'
        );

        $stmt->execute([
            'code_societaire' => 'TMP-' . uniqid(),
            'nom'             => $data['nom'],
            'prenom'          => $data['prenom'] ?: null,
            'cin'             => $data['cin'] ?: null,
            'telephone'       => $data['telephone'] ?: null,
            'adresse'         => $data['adresse'] ?: null,
            'date_naissance'  => $data['date_naissance'] ?: null,
            'photo'           => $data['photo'] ?? null,
            'piece_identite'  => $data['piece_identite'] ?? null,
            'created_by'      => $data['created_by'],
        ]);

        $id = (int) $this->db->lastInsertId();
        $code = 'SOC-' . str_pad((string) $id, 5, '0', STR_PAD_LEFT);

        $update = $this->db->prepare('UPDATE societaires SET code_societaire = ? WHERE id = ?');
        $update->execute([$code, $id]);

        return $id;
    }

    public function update(int $id, array $data): void
    {
        $sql = 'UPDATE societaires SET nom = :nom, prenom = :prenom, cin = :cin, telephone = :telephone,
                adresse = :adresse, date_naissance = :date_naissance';

        $params = [
            'id'             => $id,
            'nom'            => $data['nom'],
            'prenom'         => $data['prenom'] ?: null,
            'cin'            => $data['cin'] ?: null,
            'telephone'      => $data['telephone'] ?: null,
            'adresse'        => $data['adresse'] ?: null,
            'date_naissance' => $data['date_naissance'] ?: null,
        ];

        if (!empty($data['photo'])) {
            $sql .= ', photo = :photo';
            $params['photo'] = $data['photo'];
        }
        if (!empty($data['piece_identite'])) {
            $sql .= ', piece_identite = :piece_identite';
            $params['piece_identite'] = $data['piece_identite'];
        }

        $sql .= ' WHERE id = :id';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM societaires WHERE id = ?');
        $stmt->execute([$id]);
    }

    private function buildSearch(string $search): array
    {
        if ($search === '') {
            return ['', []];
        }

        $where = 'WHERE nom LIKE ? OR prenom LIKE ? OR cin LIKE ? OR telephone LIKE ? OR code_societaire LIKE ?';
        $like = "%$search%";

        return [$where, [$like, $like, $like, $like, $like]];
    }
}