<?php

if (PHP_SAPI === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}

require __DIR__ . '/../vendor/autoload.php';

use App\Router;
use App\Controllers\PainelController;
use App\Controllers\LancamentoController;
use App\Controllers\LoginController;

Dotenv\Dotenv::createImmutable(__DIR__ . '/..')->load();
\App\Auth::iniciarSessao();

$router = new Router();
$router->get('/', [PainelController::class, 'index']);
$router->get('/lancamentos', [LancamentoController::class, 'index']);
$router->get('/login', [LoginController::class, 'formulario']);
$router->post('/login', [LoginController::class, 'entrar']);
$router->post('/sair', [LoginController::class, 'sair']);

$router->executar($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

