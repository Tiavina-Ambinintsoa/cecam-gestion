<?php
namespace App\Controllers;

use App\Core\Controller;

class DashboardController extends Controller
{
    public function index(): void
    {
        requireAuth();
        $this->render('dashboard/index', ['pageTitle' => 'Tableau de bord']);
    }
}