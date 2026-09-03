<?php
namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, string $action): void
    {
        $this->add('GET', $path, $action);
    }

    public function post(string $path, string $action): void
    {
        $this->add('POST', $path, $action);
    }

    private function add(string $method, string $path, string $action): void
    {
        $this->routes[$method][] = [
            'pattern' => $this->toRegex($path),
            'action'  => $action,
        ];
    }

    private function normalize(string $path): string
    {
        $path = parse_url($path, PHP_URL_PATH);
        return '/' . trim($path, '/');
    }

    private function toRegex(string $path): string
    {
        $normalized = $this->normalize($path);
        $regex = preg_replace('#\{[a-zA-Z_][a-zA-Z0-9_]*\}#', '([^/]+)', $normalized);
        return '#^' . $regex . '$#';
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $this->normalize($_SERVER['REQUEST_URI']);

        foreach ($this->routes[$method] ?? [] as $route) {
            if (preg_match($route['pattern'], $uri, $matches)) {
                array_shift($matches); // retire la correspondance complète, ne garde que les paramètres capturés

                [$controllerName, $methodName] = explode('@', $route['action']);
                $controllerClass = "App\\Controllers\\$controllerName";

                if (!class_exists($controllerClass)) {
                    http_response_code(500);
                    die("Contrôleur introuvable : $controllerClass");
                }

                call_user_func_array([new $controllerClass(), $methodName], array_values($matches));
                return;
            }
        }

        http_response_code(404);
        echo "404 - Page non trouvée : $uri";
    }
}