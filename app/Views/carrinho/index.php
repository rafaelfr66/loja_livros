<h1 class="titulo-pagina">Seu carrinho</h1>

<?php if (!$itens): ?>
    <div class="vazio">
        <p>🛒 Seu carrinho está vazio.</p>
        <a href="<?= url('livros') ?>" class="btn btn-primario">Ver livros</a>
    </div>
<?php else: ?>
    <div class="carrinho">
        <form action="<?= url('carrinho/atualizar') ?>" method="post" class="carrinho-itens">
            <?= csrf_field() ?>
            <?php foreach ($itens as $item): $l = $item['livro']; ?>
                <div class="carrinho-item">
                    <div class="carrinho-capa"><?php require APP_PATH . '/Views/partials/capa.php'; ?></div>
                    <div class="carrinho-desc">
                        <a href="<?= url('livro/' . $l['id']) ?>"><strong><?= e($l['titulo']) ?></strong></a>
                        <span><?= e($l['autor']) ?></span>
                        <span class="preco"><?= moeda($l['preco']) ?></span>
                    </div>
                    <input type="number" name="quantidade[<?= $l['id'] ?>]" value="<?= $item['quantidade'] ?>"
                           min="0" max="<?= (int) $l['estoque'] ?>" aria-label="Quantidade">
                    <strong class="carrinho-subtotal"><?= moeda($item['subtotal']) ?></strong>
                    <button type="submit" class="btn-icone" title="Remover"
                            formaction="<?= url('carrinho/remover/' . $l['id']) ?>">✕</button>
                </div>
            <?php endforeach; ?>
            <button class="btn btn-contorno">Atualizar quantidades</button>
        </form>

        <aside class="carrinho-resumo">
            <h2>Resumo</h2>
            <p class="linha"><span>Itens</span><span><?= carrinho_qtd() ?></span></p>
            <p class="linha"><span>Frete</span><span><?= $total >= 150 ? 'Grátis' : 'A combinar' ?></span></p>
            <p class="linha total"><span>Total</span><span><?= moeda($total) ?></span></p>

            <form action="<?= url('carrinho/finalizar') ?>" method="post">
                <?= csrf_field() ?>
                <label>Observação (opcional)
                    <textarea name="observacao" rows="2" maxlength="255" placeholder="Ex.: embrulhar para presente"></textarea>
                </label>
                <button class="btn btn-primario btn-grande btn-bloco">
                    <?= App\Core\Auth::cliente() ? 'Finalizar pedido' : 'Entrar e finalizar' ?>
                </button>
            </form>
            <a href="<?= url('livros') ?>" class="link-discreto">← Continuar comprando</a>
        </aside>
    </div>
<?php endif; ?>
