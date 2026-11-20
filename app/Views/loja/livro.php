<nav class="migalhas">
    <a href="<?= url('') ?>">Início</a> /
    <a href="<?= url('livros?categoria=' . $livro['categoria_id']) ?>"><?= e($livro['categoria']) ?></a> /
    <span><?= e($livro['titulo']) ?></span>
</nav>

<article class="livro-detalhe">
    <div class="livro-detalhe-capa">
        <?php $l = $livro; require APP_PATH . '/Views/partials/capa.php'; ?>
    </div>

    <div class="livro-detalhe-info">
        <span class="livro-card-categoria"><?= e($livro['categoria']) ?></span>
        <h1><?= e($livro['titulo']) ?></h1>
        <p class="autor">por <strong><?= e($livro['autor']) ?></strong></p>

        <p class="preco preco-grande"><?= moeda($livro['preco']) ?></p>
        <p class="parcelas">ou 3x de <?= moeda($livro['preco'] / 3) ?> sem juros</p>

        <?php if ($livro['estoque'] > 0): ?>
            <form action="<?= url('carrinho/adicionar') ?>" method="post" class="form-comprar" data-validar>
                <?= csrf_field() ?>
                <input type="hidden" name="livro_id" value="<?= $livro['id'] ?>">
                <label>Quantidade
                    <input type="number" name="quantidade" value="1" min="1" max="<?= (int) $livro['estoque'] ?>" required>
                </label>
                <button class="btn btn-primario btn-grande">Adicionar ao carrinho</button>
            </form>
            <p class="estoque-info <?= $livro['estoque'] <= 3 ? 'baixo' : '' ?>">
                <?= $livro['estoque'] <= 3 ? '⚠ Restam apenas ' . (int) $livro['estoque'] . ' unidade(s)!' : '✔ Em estoque' ?>
            </p>
        <?php else: ?>
            <p class="alerta alerta-erro">Este livro está esgotado no momento.</p>
        <?php endif; ?>

        <?php if ($livro['sinopse']): ?>
            <h2>Sinopse</h2>
            <p class="sinopse"><?= nl2br(e($livro['sinopse'])) ?></p>
        <?php endif; ?>

        <dl class="ficha">
            <dt>ISBN</dt><dd><?= e($livro['isbn']) ?></dd>
            <?php if ($livro['editora']): ?><dt>Editora</dt><dd><?= e($livro['editora']) ?></dd><?php endif; ?>
            <?php if ($livro['ano_publicacao']): ?><dt>Ano</dt><dd><?= e($livro['ano_publicacao']) ?></dd><?php endif; ?>
            <?php if ($livro['paginas']): ?><dt>Páginas</dt><dd><?= e($livro['paginas']) ?></dd><?php endif; ?>
        </dl>
    </div>
</article>

<?php if ($relacionados): ?>
    <section class="secao">
        <div class="secao-cabecalho"><h2>Você também pode gostar</h2></div>
        <div class="grade-livros">
            <?php foreach ($relacionados as $l): ?>
                <?php require APP_PATH . '/Views/partials/livro_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>
