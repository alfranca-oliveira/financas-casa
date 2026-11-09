<?php

if (PHP_SAPI === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}

require __DIR__ . '/../vendor/autoload.php';

use App\Router;
use App\Controllers\PainelController;
use App\Controllers\LancamentoController;

Dotenv\Dotenv::createImmutable(__DIR__ . '/..')->load();

$router = new Router();
$router->get('/', [PainelController::class, 'index']);
$router->get('/lancamentos', [LancamentoController::class, 'index']);

$router->executar($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);