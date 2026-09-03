<?php
namespace App\Core;

class Controller
{
    protected function render(string $view, array $data = [], string $layout = 'app'): void
    {
        extract($data);
        $viewPath = BASE_PATH . '/app/Views/' . $view . '.php';

        if (!file_exists($viewPath)) {
            http_response_code(500);
            die("Vue introuvable : $view");
        }

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        require BASE_PATH . '/app/Views/layouts/' . $layout . '.php';
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}