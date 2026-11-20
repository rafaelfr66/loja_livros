<?php use App\Core\Auth; $cliente = Auth::cliente(); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo ?? '') ?> · Livraria Entrelinhas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,700&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>
<header class="topo">
    <div class="container topo-inner">
        <a href="<?= url('') ?>" class="logo">
            <span class="logo-icone">E</span>
            <span>Entrelinhas<small>livraria</small></span>
        </a>

        <form action="<?= url('livros') ?>" method="get" class="busca-topo">
            <input type="search" name="busca" placeholder="Buscar por título, autor ou ISBN" value="<?= e($_GET['busca'] ?? '') ?>">
            <button type="submit" aria-label="Buscar">🔍</button>
        </form>

        <nav class="menu">
            <a href="<?= url('livros') ?>">Catálogo</a>
            <?php if ($cliente): ?>
                <a href="<?= url('minha-conta') ?>">Olá, <?= e(explode(' ', $cliente['nome'])[0]) ?></a>
                <a href="<?= url('sair') ?>">Sair</a>
            <?php else: ?>
                <a href="<?= url('entrar') ?>">Entrar</a>
                <a href="<?= url('cadastro') ?>">Criar conta</a>
            <?php endif; ?>
            <a href="<?= url('carrinho') ?>" class="btn-carrinho">
                🛒 Carrinho <?php if (carrinho_qtd()): ?><span class="contador"><?= carrinho_qtd() ?></span><?php endif; ?>
            </a>
        </nav>
    </div>
</header>

<main class="container conteudo">
    <?php foreach (flashes() as $f): ?>
        <div class="alerta alerta-<?= e($f['tipo']) ?>"><?= e($f['mensagem']) ?></div>
    <?php endforeach; ?>
    <?= $conteudo ?>
</main>

<footer class="rodape">
    <div class="container rodape-inner">
        <div>
            <strong>Livraria Entrelinhas</strong>
            <p>Histórias que ficam entre as linhas e na memória.</p>
        </div>
        <div>
            <p>📍 Rua dos Leitores, 42 – Centro</p>
            <p>📞 (11) 4002-8922 · ✉ contato@entrelinhas.com</p>
            <p>🕘 Seg. a sáb., 9h às 20h</p>
        </div>
        <div>
            <a href="<?= url('admin/login') ?>">Área administrativa</a>
        </div>
    </div>
</footer>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
