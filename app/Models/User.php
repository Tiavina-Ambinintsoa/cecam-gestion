<?php
namespace App\Models;

use App\Core\Model;

class User extends Model
{
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ? AND actif = 1 LIMIT 1');
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }
}