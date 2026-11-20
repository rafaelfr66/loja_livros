<div class="barra-acoes">
    <a href="<?= url('admin/pedidos') ?>" class="link-discreto">← Voltar aos pedidos</a>
    <div class="acoes">
        <a href="<?= url("admin/pedidos/{$pedido['id']}/editar") ?>" class="btn btn-contorno">Alterar status</a>
        <form action="<?= url("admin/pedidos/{$pedido['id']}/excluir") ?>" method="post" data-confirmar="Excluir o pedido #<?= $pedido['id'] ?>?">
            <?= csrf_field() ?>
            <button class="btn btn-perigo">Excluir</button>
        </form>
    </div>
</div>

<div class="grade-2">
    <section class="painel">
        <h2>Dados do pedido</h2>
        <dl class="ficha">
            <dt>Número</dt><dd>#<?= $pedido['id'] ?></dd>
            <dt>Data</dt><dd><?= data_br($pedido['data_pedido'], true) ?></dd>
            <dt>Status</dt><dd><?= status_pedido($pedido['status']) ?></dd>
            <dt>Observação</dt><dd><?= e($pedido['observacao'] ?: '—') ?></dd>
        </dl>
    </section>
    <section class="painel">
        <h2>Cliente</h2>
        <dl class="ficha">
            <dt>Nome</dt><dd><?= e($pedido['cliente']) ?></dd>
            <dt>E-mail</dt><dd><?= e($pedido['cliente_email']) ?></dd>
            <dt>Telefone</dt><dd><?= e($pedido['cliente_telefone']) ?></dd>
            <dt>Endereço</dt><dd><?= e($pedido['cliente_endereco'] ?: '—') ?></dd>
        </dl>
    </section>
</div>

<section class="painel">
    <h2>Itens</h2>
    <div class="tabela-wrap">
        <table class="tabela">
            <thead><tr><th>Livro</th><th>Qtd.</th><th>Preço unit.</th><th>Subtotal</th></tr></thead>
            <tbody>
            <?php foreach ($pedido['itens'] as $i): ?>
                <tr>
                    <td><?= e($i['titulo']) ?></td>
                    <td><?= (int) $i['quantidade'] ?></td>
                    <td><?= moeda($i['preco_unitario']) ?></td>
                    <td><?= moeda($i['subtotal']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot><tr><th colspan="3">Total</th><th><?= moeda($pedido['total']) ?></th></tr></tfoot>
        </table>
    </div>
</section>
