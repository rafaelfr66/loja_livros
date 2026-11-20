<?php

namespace App\Core;

class Upload
{
    private static array $config = [];

    private const TIPOS = [
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG => 'png',
        IMAGETYPE_WEBP => 'webp',
        IMAGETYPE_GIF => 'gif',
    ];

    public static function configure(array $config): void
    {
        self::$config = $config;
    }

    public static function imagem(string $campo): ?string
    {
        $arquivo = $_FILES[$campo] ?? null;
        if (!$arquivo || $arquivo['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($arquivo['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Falha no envio da imagem (código ' . $arquivo['error'] . ').');
        }
        if ($arquivo['size'] > self::$config['max_bytes']) {
            throw new \RuntimeException('A imagem deve ter no máximo 2 MB.');
        }

        $info = @getimagesize($arquivo['tmp_name']);
        if (!$info || !isset(self::TIPOS[$info[2]])) {
            throw new \RuntimeException('Formato inválido. Envie JPG, PNG, WEBP ou GIF.');
        }

        $nome = bin2hex(random_bytes(12)) . '.' . self::TIPOS[$info[2]];
        if (!is_dir(self::$config['dir'])) {
            mkdir(self::$config['dir'], 0775, true);
        }
        if (!move_uploaded_file($arquivo['tmp_name'], self::$config['dir'] . $nome)) {
            throw new \RuntimeException('Não foi possível salvar a imagem.');
        }
        return $nome;
    }

    public static function remover(?string $nome): void
    {
        if ($nome && is_file(self::$config['dir'] . basename($nome))) {
            unlink(self::$config['dir'] . basename($nome));
        }
    }
}
