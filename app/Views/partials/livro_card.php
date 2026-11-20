<article class="livro-card">
    <a href="<?= url('livro/' . $l['id']) ?>" class="livro-card-capa">
        <?php require __DIR__ . '/capa.php'; ?>
        <?php if ((int) $l['estoque'] === 0): ?>
            <span class="selo selo-esgotado">Esgotado</span>
        <?php elseif ((int) $l['estoque'] <= 3): ?>
            <span class="selo">Últimas unidades</span>
        <?php endif; ?>
    </a>
    <div class="livro-card-info">
        <span class="livro-card-categoria"><?= e($l['categoria']) ?></span>
        <h3><a href="<?= url('livro/' . $l['id']) ?>"><?= e($l['titulo']) ?></a></h3>
        <p class="livro-card-autor"><?= e($l['autor']) ?></p>
        <p class="preco"><?= moeda($l['preco']) ?></p>
    </div>
</article>
