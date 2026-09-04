<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\CompteEpargne;
use App\Models\Societaire;
use RuntimeException;

class EpargneController extends Controller
{
    private CompteEpargne $comptes;
    private Societaire $societaires;

    public function __construct()
    {
        $this->comptes = new CompteEpargne();
        $this->societaires = new Societaire();
    }

    public function index(): void
    {
        requireAuth();

        $search  = trim($_GET['q'] ?? '');
        $page    = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;

        $total      = $this->comptes->count($search);
        $pagination = paginate($total, $page, $perPage);
        $rows       = $this->comptes->paginate($search, $perPage, $pagination['offset']);

        $viewData = [
            'comptes'    => $rows,
            'search'     => $search,
            'pagination' => $pagination,
        ];

        if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest') {
            $this->renderPartial('epargne/_table', $viewData);
            return;
        }

        $this->render('epargne/index', array_merge($viewData, [
            'pageTitle' => 'Épargne',
        ]));
    }

    public function panel(string $societaireId): void
    {
        requireAuth();

        $societaire = $this->societaires->find((int) $societaireId);
        if (!$societaire) {
            http_response_code(404);
            die('Sociétaire introuvable.');
        }

        $this->renderPartial('societaires/_epargne_panel', [
            'societaire' => $societaire,
            'comptes'    => $this->comptes->findBySocietaire((int) $societaireId),
        ]);
    }

    public function create(string $societaireId): void
    {
        requireAuth();

        $societaire = $this->societaires->find((int) $societaireId);
        if (!$societaire) {
            http_response_code(404);
            die('Sociétaire introuvable.');
        }

        $this->render('epargne/create', [
            'pageTitle'  => 'Nouveau compte — ' . $societaire['nom'],
            'societaire' => $societaire,
            'errors'     => [],
            'old'        => [],
        ]);
    }

    public function store(string $societaireId): void
    {
        requireAuth();

        $societaire = $this->societaires->find((int) $societaireId);
        if (!$societaire) {
            http_response_code(404);
            die('Sociétaire introuvable.');
        }

        [$data, $errors] = $this->validate($_POST);

        if ($errors) {
            $this->render('epargne/create', [
                'pageTitle'  => 'Nouveau compte — ' . $societaire['nom'],
                'societaire' => $societaire,
                'errors'     => $errors,
                'old'        => $_POST,
            ]);
            return;
        }

        $data['societaire_id'] = (int) $societaireId;
        $id = $this->comptes->create($data);

        flash('success', "Compte d'épargne créé avec succès.");
        $this->redirect('/comptes/' . $id);
    }

    public function show(string $id): void
    {
        requireAuth();

        $compte = $this->comptes->find((int) $id);
        if (!$compte) {
            http_response_code(404);
            die('Compte introuvable.');
        }

        $page       = max(1, (int) ($_GET['page'] ?? 1));
        $perPage    = 10;
        $total      = $this->comptes->countHistorique((int) $id);
        $pagination = paginate($total, $page, $perPage);
        $mouvements = $this->comptes->historique((int) $id, $perPage, $pagination['offset']);

        $this->render('epargne/show', [
            'pageTitle'  => 'Compte ' . $compte['numero_compte'],
            'compte'     => $compte,
            'mouvements' => $mouvements,
            'pagination' => $pagination,
        ]);
    }

    public function depot(string $id): void
    {
        requireAuth();

        $montant = (float) str_replace(',', '.', $_POST['montant'] ?? '0');

        try {
            $this->comptes->deposer((int) $id, $montant, (int) $_SESSION['user_id']);
            flash('success', 'Dépôt enregistré avec succès.');
        } catch (RuntimeException $e) {
            flash('error', $e->getMessage());
        }

        $this->redirect('/comptes/' . $id);
    }

    public function retrait(string $id): void
    {
        requireAuth();

        $montant = (float) str_replace(',', '.', $_POST['montant'] ?? '0');

        try {
            $this->comptes->retirer((int) $id, $montant, (int) $_SESSION['user_id']);
            flash('success', 'Retrait enregistré avec succès.');
        } catch (RuntimeException $e) {
            flash('error', $e->getMessage());
        }

        $this->redirect('/comptes/' . $id);
    }

    private function validate(array $input): array
    {
        $errors = [];
        $data = [
            'type_compte'   => trim($input['type_compte'] ?? ''),
            'taux_interet'  => trim($input['taux_interet'] ?? '0'),
            'date_echeance' => trim($input['date_echeance'] ?? ''),
        ];

        if (!in_array($data['type_compte'], CompteEpargne::TYPES, true)) {
            $errors['type_compte'] = 'Type de compte invalide (DAV, DAT ou PLE).';
        }

        if (!is_numeric($data['taux_interet']) || (float) $data['taux_interet'] < 0) {
            $errors['taux_interet'] = "Le taux d'intérêt doit être un nombre positif.";
        }

        if ($data['type_compte'] === 'DAT' && $data['date_echeance'] === '') {
            $errors['date_echeance'] = "Une date d'échéance est obligatoire pour un dépôt à terme (DAT).";
        }
        if ($data['date_echeance'] !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['date_echeance'])) {
            $errors['date_echeance'] = 'Date invalide.';
        }

        return [$data, $errors];
    }
}