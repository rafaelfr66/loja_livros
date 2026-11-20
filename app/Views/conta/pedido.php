<a href="<?= url('minha-conta') ?>" class="link-discreto">← Meus pedidos</a>
<h1 class="titulo-pagina">Pedido #<?= $pedido['id'] ?> <?= status_pedido($pedido['status']) ?></h1>
<p>Realizado em <?= data_br($pedido['data_pedido'], true) ?></p>

<div class="tabela-wrap">
    <table class="tabela">
        <thead><tr><th>Livro</th><th>Qtd.</th><th>Preço unit.</th><th>Subtotal</th></tr></thead>
        <tbody>
        <?php foreach ($pedido['itens'] as $i): ?>
            <tr>
                <td><a href="<?= url('livro/' . $i['livro_id']) ?>"><?= e($i['titulo']) ?></a></td>
                <td><?= (int) $i['quantidade'] ?></td>
                <td><?= moeda($i['preco_unitario']) ?></td>
                <td><?= moeda($i['subtotal']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot><tr><th colspan="3">Total</th><th><?= moeda($pedido['total']) ?></th></tr></tfoot>
    </table>
</div>
<?php if ($pedido['observacao']): ?>
    <p><strong>Observação:</strong> <?= e($pedido['observacao']) ?></p>
<?php endif; ?>
