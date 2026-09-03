<?php
declare(strict_types=1);

session_start();

define('BASE_PATH', dirname(__DIR__));

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    if (strpos($class, $prefix) !== 0) return;
    $relative = substr($class, strlen($prefix));
    $path = BASE_PATH . '/app/' . str_replace('\\', '/', $relative) . '.php';
    if (file_exists($path)) require $path;
});

require BASE_PATH . '/app/Config/env.php';
require BASE_PATH . '/app/Helpers/functions.php';

use App\Core\Router;

$router = new Router();

$router->get('/', 'DashboardController@index');
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');

$router->get('/societaires', 'SocietaireController@index');
$router->get('/societaires/create', 'SocietaireController@create');
$router->post('/societaires', 'SocietaireController@store');
$router->get('/societaires/{id}', 'SocietaireController@show');
$router->get('/societaires/{id}/edit', 'SocietaireController@edit');
$router->post('/societaires/{id}/edit', 'SocietaireController@update');
$router->post('/societaires/{id}/supprimer', 'SocietaireController@destroy');

$router->dispatch();