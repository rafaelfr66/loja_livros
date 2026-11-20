<?php

declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');

$config = require ROOT_PATH . '/config/config.php';
date_default_timezone_set($config['app']['timezone']);

spl_autoload_register(function (string $classe): void {
    $prefixo = 'App\\';
    if (!str_starts_with($classe, $prefixo)) {
        return;
    }
    $relativo = str_replace('\\', '/', substr($classe, strlen($prefixo)));
    $arquivo = APP_PATH . '/' . $relativo . '.php';
    if (is_file($arquivo)) {
        require $arquivo;
    }
});

session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
]);

App\Core\Database::configure($config['db']);
App\Core\Upload::configure($config['upload']);

require APP_PATH . '/helpers.php';
