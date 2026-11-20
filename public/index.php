<?php
if (PHP_SAPI === 'cli-server') {
    $arquivo = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file($arquivo)) {
        return false;
    }
}

require dirname(__DIR__) . '/app/bootstrap.php';

$router = new App\Core\Router();
require APP_PATH . '/routes.php';

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
