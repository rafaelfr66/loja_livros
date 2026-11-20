<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $conexao = null;
    private static array $config = [];

    public static function configure(array $config): void
    {
        self::$config = $config;
    }

    public static function getConnection(): PDO
    {
        if (self::$conexao === null) {
            $c = self::$config;
            $dsn = "mysql:host={$c['host']};port={$c['port']};dbname={$c['name']};charset={$c['charset']}";
            try {
                self::$conexao = new PDO($dsn, $c['user'], $c['pass'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                http_response_code(500);
                exit('<h1>Erro ao conectar ao banco de dados</h1><p>Verifique se o container MySQL está rodando '
                    . '(<code>docker compose up -d db</code>).</p><pre>' . htmlspecialchars($e->getMessage()) . '</pre>');
            }
        }
        return self::$conexao;
    }

    private function __construct() {}
    private function __clone() {}
}
