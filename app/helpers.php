<?php
function e(mixed $valor): string
{
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

function base_path(): string
{
    $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
    return rtrim($dir, '/');
}

function url(string $caminho = ''): string
{
    return base_path() . '/' . ltrim($caminho, '/');
}

function asset(string $arquivo): string
{
    return url('assets/' . ltrim($arquivo, '/'));
}

function capa_url(?string $arquivo): ?string
{
    return $arquivo ? url('uploads/capas/' . $arquivo) : null;
}

function redirect(string $caminho): never
{
    header('Location: ' . url($caminho));
    exit;
}

function moeda(float|string|null $valor): string
{
    return 'R$ ' . number_format((float) $valor, 2, ',', '.');
}

function data_br(?string $data, bool $comHora = false): string
{
    if (!$data) {
        return '';
    }
    return date($comHora ? 'd/m/Y H:i' : 'd/m/Y', strtotime($data));
}

function flash(string $tipo, string $mensagem): void
{
    $_SESSION['flash'][] = ['tipo' => $tipo, 'mensagem' => $mensagem];
}

function flashes(): array
{
    $msgs = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $msgs;
}

function old(string $campo, mixed $padrao = ''): mixed
{
    return $_SESSION['old'][$campo] ?? $padrao;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . csrf_token() . '">';
}

function status_pedido(string $status): string
{
    $rotulos = [
        'pendente' => 'Pendente',
        'pago' => 'Pago',
        'enviado' => 'Enviado',
        'entregue' => 'Entregue',
        'cancelado' => 'Cancelado',
    ];
    return '<span class="badge badge-' . e($status) . '">' . e($rotulos[$status] ?? $status) . '</span>';
}

function capa_cor(int $id): int
{
    return ($id * 47) % 360;
}

function carrinho_qtd(): int
{
    return array_sum($_SESSION['carrinho'] ?? []);
}

function tamanho(string $texto): int
{
    if (function_exists('mb_strlen')) {
        return mb_strlen($texto);
    }
    $n = preg_match_all('/./us', $texto);
    return $n === false ? strlen($texto) : $n;
}
