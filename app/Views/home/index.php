<section class="hero">
    <div class="hero-texto">
        <span class="sobretitulo">Livraria independente desde 2012</span>
        <h1>Toda boa história começa <em>entre as linhas</em>.</h1>
        <p>Clássicos brasileiros, fantasia, ficção científica, romances e livros técnicos —
            escolhidos a dedo por quem ama ler. Compre online e receba em casa.</p>
        <div class="hero-acoes">
            <a href="<?= url('livros') ?>" class="btn btn-primario">Explorar o catálogo</a>
            <?php if (!App\Core\Auth::cliente()): ?>
                <a href="<?= url('cadastro') ?>" class="btn btn-contorno">Criar minha conta</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="hero-pilha" aria-hidden="true">
        <?php foreach (array_slice($destaques, 0, 3) as $l): ?>
            <div class="hero-livro"><?php require APP_PATH . '/Views/partials/capa.php'; ?></div>
        <?php endforeach; ?>
    </div>
</section>

<section class="vantagens">
    <div>🚚 <strong>Frete grátis</strong><span>acima de R$ 150</span></div>
    <div>🔁 <strong>Troca fácil</strong><span>em até 7 dias</span></div>
    <div>📖 <strong>Curadoria</strong><span>indicações dos livreiros</span></div>
</section>

<section class="secao">
    <div class="secao-cabecalho">
        <h2>Navegue por gênero</h2>
    </div>
    <div class="categorias-lista">
        <?php foreach ($categorias as $c): ?>
            <a href="<?= url('livros?categoria=' . $c['id']) ?>" class="categoria-chip">
                <strong><?= e($c['nome']) ?></strong>
                <span><?= (int) $c['qtd_livros'] ?> título(s)</span>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<section class="secao">
    <div class="secao-cabecalho">
        <h2>Destaques da estante</h2>
        <a href="<?= url('livros') ?>">Ver todos →</a>
    </div>
    <div class="grade-livros">
        <?php foreach ($destaques as $l): ?>
            <?php require APP_PATH . '/Views/partials/livro_card.php'; ?>
        <?php endforeach; ?>
    </div>
</section>

<section class="secao">
    <div class="secao-cabecalho">
        <h2>Chegaram agora</h2>
        <a href="<?= url('livros?ordem=recentes') ?>">Mais novidades →</a>
    </div>
    <div class="grade-livros">
        <?php foreach ($novidades as $l): ?>
            <?php require APP_PATH . '/Views/partials/livro_card.php'; ?>
        <?php endforeach; ?>
    </div>
</section>
