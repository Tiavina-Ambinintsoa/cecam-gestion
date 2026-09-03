<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (isLoggedIn()) $this->redirect('/');
        $this->render('auth/login', [], 'guest');
    }

    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['mot_de_passe'] ?? '';

        $user = (new User())->findByEmail($email);

        if (!$user || !password_verify($password, $user['mot_de_passe'])) {
            flash('error', 'Identifiants incorrects.');
            $this->redirect('/login');
            return;
        }

        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['nom'];
        $_SESSION['user_role'] = $user['role'];

        $this->redirect('/');
    }

    public function logout(): void
    {
        session_destroy();
        $this->redirect('/login');
    }
}