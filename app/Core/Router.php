<?php

namespace App\Core;

class Router
{
    private array $rotas = [];

    public function get(string $caminho, array $acao): void
    {
        $this->add('GET', $caminho, $acao);
    }

    public function post(string $caminho, array $acao): void
    {
        $this->add('POST', $caminho, $acao);
    }

    private function add(string $metodo, string $caminho, array $acao): void
    {
        $regex = preg_replace('#\{(\w+)\}#', '(?P<$1>\d+)', rtrim($caminho, '/') ?: '/');
        $this->rotas[$metodo]['#^' . $regex . '$#'] = $acao;
    }

    public function dispatch(string $uri, string $metodo): void
    {
        $caminho = parse_url($uri, PHP_URL_PATH) ?? '/';
        $base = base_path();
        if ($base !== '' && str_starts_with($caminho, $base)) {
            $caminho = substr($caminho, strlen($base));
        }
        $caminho = rtrim($caminho, '/') ?: '/';

        foreach ($this->rotas[$metodo] ?? [] as $regex => [$controller, $acao]) {
            if (preg_match($regex, $caminho, $m)) {
                $params = array_map('intval', array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY));
                (new $controller())->$acao(...array_values($params));
                return;
            }
        }

        http_response_code(404);
        (new Controller())->render('errors/404', ['titulo' => 'Página não encontrada']);
    }
}
