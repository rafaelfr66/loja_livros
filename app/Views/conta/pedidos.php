<h1 class="titulo-pagina">Meus pedidos</h1>

<?php if (!$pedidos): ?>
    <div class="vazio">
        <p>Você ainda não fez nenhum pedido.</p>
        <a href="<?= url('livros') ?>" class="btn btn-primario">Começar a comprar</a>
    </div>
<?php else: ?>
    <div class="tabela-wrap">
        <table class="tabela">
            <thead><tr><th>Pedido</th><th>Data</th><th>Itens</th><th>Total</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($pedidos as $p): ?>
                <tr>
                    <td>#<?= $p['id'] ?></td>
                    <td><?= data_br($p['data_pedido'], true) ?></td>
                    <td><?= (int) $p['qtd_itens'] ?></td>
                    <td><?= moeda($p['total']) ?></td>
                    <td><?= status_pedido($p['status']) ?></td>
                    <td><a href="<?= url('minha-conta/pedido/' . $p['id']) ?>">Detalhes</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
