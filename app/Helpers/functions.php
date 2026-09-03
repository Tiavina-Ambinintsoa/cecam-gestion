<?php

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function currentUserRole(): ?string
{
    return $_SESSION['user_role'] ?? null;
}

function requireAuth(): void
{
    if (!isLoggedIn()) {
        redirect('/login');
    }
}

function flash(string $key, ?string $message = null)
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }
    $msg = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $msg;
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function uploadFile(?array $file, string $subDir, array $allowedExt, int $maxSizeBytes): ?string
{
    if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Erreur lors du téléversement du fichier.');
    }
    if ($file['size'] > $maxSizeBytes) {
        throw new RuntimeException('Fichier trop volumineux (max ' . round($maxSizeBytes / 1024 / 1024, 1) . ' Mo).');
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt, true)) {
        throw new RuntimeException('Type de fichier non autorisé (' . implode(', ', $allowedExt) . ' uniquement).');
    }

    $targetDir = BASE_PATH . '/public/uploads/' . $subDir . '/';
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $filename = uniqid($subDir . '_', true) . '.' . $ext;
    $filename = str_replace('/', '_', $filename);

    if (!move_uploaded_file($file['tmp_name'], $targetDir . $filename)) {
        throw new RuntimeException("Impossible d'enregistrer le fichier.");
    }

    return 'uploads/' . $subDir . '/' . $filename;
}

// Calcule les infos de pagination (page courante, nb de pages, offset SQL).
function paginate(int $totalItems, int $currentPage, int $perPage = 10): array
{
    $totalPages = max(1, (int) ceil($totalItems / $perPage));
    $currentPage = max(1, min($currentPage, $totalPages));

    return [
        'total'        => $totalItems,
        'per_page'     => $perPage,
        'current_page' => $currentPage,
        'total_pages'  => $totalPages,
        'offset'       => ($currentPage - 1) * $perPage,
    ];
}