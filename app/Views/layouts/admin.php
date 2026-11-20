<?php

use App\Core\Auth;
$usuario = Auth::usuario();
$atual = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$ativo = function (string $caminho) use ($atual): string {
    $alvo = url($caminho);
    $igual = $caminho === 'admin' ? rtrim($atual, '/') === rtrim($alvo, '/') : str_starts_with($atual, $alvo);
    return $igual ? 'ativo' : '';
};
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo ?? '') ?> · Admin Entrelinhas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,700&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="admin">
<aside class="sidebar">
    <a href="<?= url('admin') ?>" class="logo logo-claro">
        <span class="logo-icone">E</span>
        <span>Entrelinhas<small>administração</small></span>
    </a>
    <nav>
        <a class="<?= $ativo('admin') ?>" href="<?= url('admin') ?>">📊 Dashboard</a>
        <a class="<?= $ativo('admin/pedidos') ?>" href="<?= url('admin/pedidos') ?>">🧾 Pedidos</a>
        <a class="<?= $ativo('admin/livros') ?>" href="<?= url('admin/livros') ?>">📚 Livros</a>
        <a class="<?= $ativo('admin/categorias') ?>" href="<?= url('admin/categorias') ?>">🏷️ Categorias</a>
        <a class="<?= $ativo('admin/autores') ?>" href="<?= url('admin/autores') ?>">✒️ Autores</a>
        <a class="<?= $ativo('admin/clientes') ?>" href="<?= url('admin/clientes') ?>">👥 Clientes</a>
        <?php if (Auth::isAdmin()): ?>
            <a class="<?= $ativo('admin/usuarios') ?>" href="<?= url('admin/usuarios') ?>">🔐 Usuários</a>
        <?php endif; ?>
        <a class="<?= $ativo('admin/relatorios') ?>" href="<?= url('admin/relatorios/vendas') ?>">📈 Relatório de vendas</a>
    </nav>
    <div class="sidebar-rodape">
        <a href="<?= url('') ?>" target="_blank">↗ Ver loja</a>
    </div>
</aside>

<div class="admin-principal">
    <header class="admin-topo">
        <h1><?= e($titulo ?? '') ?></h1>
        <div class="admin-usuario">
            <span><?= e($usuario['nome']) ?> <small class="badge"><?= e($usuario['perfil']) ?></small></span>
            <a href="<?= url('admin/sair') ?>" class="btn btn-sm btn-contorno">Sair</a>
        </div>
    </header>
    <main class="admin-conteudo">
        <?php foreach (flashes() as $f): ?>
            <div class="alerta alerta-<?= e($f['tipo']) ?>"><?= e($f['mensagem']) ?></div>
        <?php endforeach; ?>
        <?= $conteudo ?>
    </main>
</div>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
