<?php
define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/app/Config/env.php';
require BASE_PATH . '/app/Config/Database.php';

use App\Config\Database;

$pdo = Database::getConnection();

$stmt = $pdo->prepare('INSERT INTO users (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)');
$stmt->execute([
    'Administrateur',
    'admin@cecam.mg',
    password_hash('admin123', PASSWORD_DEFAULT),
    'admin',
]);

echo "Utilisateur admin créé : admin@cecam.mg / admin123\n";