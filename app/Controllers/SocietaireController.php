<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Societaire;
use RuntimeException;

class SocietaireController extends Controller
{
    private Societaire $societaires;

    public function __construct()
    {
        $this->societaires = new Societaire();
    }

    public function index(): void
    {
        requireAuth();

        $search  = trim($_GET['q'] ?? '');
        $page    = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;

        $total      = $this->societaires->count($search);
        $pagination = paginate($total, $page, $perPage);
        $rows       = $this->societaires->paginate($search, $perPage, $pagination['offset']);

        $viewData = [
            'societaires' => $rows,
            'search'      => $search,
            'pagination'  => $pagination,
        ];

        if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest') {
            $this->renderPartial('societaires/_table', $viewData);
            return;
        }

        $this->render('societaires/index', array_merge($viewData, [
            'pageTitle' => 'Sociétaires',
        ]));
    }

    public function create(): void
    {
        requireAuth();
        $this->render('societaires/create', [
            'pageTitle' => 'Nouveau sociétaire',
            'errors'    => [],
            'old'       => [],
        ]);
    }

    public function store(): void
    {
        requireAuth();

        [$data, $errors] = $this->validate($_POST);

        if ($errors) {
            $this->render('societaires/create', [
                'pageTitle' => 'Nouveau sociétaire',
                'errors'    => $errors,
                'old'       => $_POST,
            ]);
            return;
        }

        try {
            $data['photo'] = uploadFile($_FILES['photo'] ?? null, 'societaires/photos', ['jpg', 'jpeg', 'png'], 2 * 1024 * 1024);
            $data['piece_identite'] = uploadFile($_FILES['piece_identite'] ?? null, 'societaires/pieces', ['jpg', 'jpeg', 'png', 'pdf'], 5 * 1024 * 1024);
        } catch (RuntimeException $e) {
            $this->render('societaires/create', [
                'pageTitle' => 'Nouveau sociétaire',
                'errors'    => ['fichier' => $e->getMessage()],
                'old'       => $_POST,
            ]);
            return;
        }

        $data['created_by'] = $_SESSION['user_id'];
        $id = $this->societaires->create($data);

        flash('success', 'Sociétaire créé avec succès.');
        $this->redirect('/societaires/' . $id);
    }

    public function show(string $id): void
    {
        requireAuth();

        $societaire = $this->societaires->find((int) $id);
        if (!$societaire) {
            http_response_code(404);
            die('Sociétaire introuvable.');
        }

        $this->render('societaires/show', [
            'pageTitle'  => trim($societaire['nom'] . ' ' . $societaire['prenom']),
            'societaire' => $societaire,
        ]);
    }

    public function edit(string $id): void
    {
        requireAuth();

        $societaire = $this->societaires->find((int) $id);
        if (!$societaire) {
            http_response_code(404);
            die('Sociétaire introuvable.');
        }

        $this->render('societaires/edit', [
            'pageTitle'  => 'Modifier — ' . $societaire['nom'],
            'societaire' => $societaire,
            'errors'     => [],
        ]);
    }

    public function update(string $id): void
    {
        requireAuth();

        $societaire = $this->societaires->find((int) $id);
        if (!$societaire) {
            http_response_code(404);
            die('Sociétaire introuvable.');
        }

        [$data, $errors] = $this->validate($_POST);

        if ($errors) {
            $this->render('societaires/edit', [
                'pageTitle'  => 'Modifier — ' . $societaire['nom'],
                'societaire' => array_merge($societaire, $_POST),
                'errors'     => $errors,
            ]);
            return;
        }

        try {
            $data['photo'] = uploadFile($_FILES['photo'] ?? null, 'societaires/photos', ['jpg', 'jpeg', 'png'], 2 * 1024 * 1024);
            $data['piece_identite'] = uploadFile($_FILES['piece_identite'] ?? null, 'societaires/pieces', ['jpg', 'jpeg', 'png', 'pdf'], 5 * 1024 * 1024);
        } catch (RuntimeException $e) {
            $this->render('societaires/edit', [
                'pageTitle'  => 'Modifier — ' . $societaire['nom'],
                'societaire' => array_merge($societaire, $_POST),
                'errors'     => ['fichier' => $e->getMessage()],
            ]);
            return;
        }

        $this->societaires->update((int) $id, $data);

        flash('success', 'Sociétaire mis à jour.');
        $this->redirect('/societaires/' . $id);
    }

    public function destroy(string $id): void
    {
        requireAuth();

        if (currentUserRole() !== 'admin') {
            http_response_code(403);
            die('Accès refusé.');
        }

        $this->societaires->delete((int) $id);

        flash('success', 'Sociétaire supprimé.');
        $this->redirect('/societaires');
    }

    private function validate(array $input): array
    {
        $errors = [];
        $data = [
            'nom'            => trim($input['nom'] ?? ''),
            'prenom'         => trim($input['prenom'] ?? ''),
            'cin'            => trim($input['cin'] ?? ''),
            'telephone'      => trim($input['telephone'] ?? ''),
            'adresse'        => trim($input['adresse'] ?? ''),
            'date_naissance' => trim($input['date_naissance'] ?? ''),
        ];

        if ($data['nom'] === '') {
            $errors['nom'] = 'Le nom est obligatoire.';
        }
        if ($data['telephone'] !== '' && !preg_match('/^[0-9+ ]{6,20}$/', $data['telephone'])) {
            $errors['telephone'] = 'Numéro de téléphone invalide.';
        }
        if ($data['date_naissance'] !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['date_naissance'])) {
            $errors['date_naissance'] = 'Date invalide.';
        }

        return [$data, $errors];
    }
}