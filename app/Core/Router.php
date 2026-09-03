<?php
namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, string $action): void
    {
        $this->routes['GET'][$this->normalize($path)] = $action;
    }

    public function post(string $path, string $action): void
    {
        $this->routes['POST'][$this->normalize($path)] = $action;
    }

    private function normalize(string $path): string
    {
        $path = parse_url($path, PHP_URL_PATH);
        return '/' . trim($path, '/');
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $this->normalize($_SERVER['REQUEST_URI']);

        $action = $this->routes[$method][$uri] ?? null;

        if ($action === null) {
            http_response_code(404);
            echo "404 - Page non trouvée : $uri";
            return;
        }

        [$controllerName, $methodName] = explode('@', $action);
        $controllerClass = "App\\Controllers\\$controllerName";

        if (!class_exists($controllerClass)) {
            http_response_code(500);
            die("Contrôleur introuvable : $controllerClass");
        }

        (new $controllerClass())->$methodName();
    }
}