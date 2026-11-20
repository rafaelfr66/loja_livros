<div class="barra-acoes">
    <div class="abas">
        <a href="<?= url('admin/pedidos') ?>" class="<?= !$status ? 'ativo' : '' ?>">Todos</a>
        <?php foreach (App\Models\Pedido::STATUS as $s): ?>
            <a href="<?= url('admin/pedidos?status=' . $s) ?>" class="<?= $status === $s ? 'ativo' : '' ?>"><?= ucfirst($s) ?></a>
        <?php endforeach; ?>
    </div>
    <a href="<?= url('admin/pedidos/novo') ?>" class="btn btn-primario">+ Novo pedido</a>
</div>

<div class="tabela-wrap">
    <table class="tabela">
        <thead><tr><th>#</th><th>Cliente</th><th>Data</th><th>Itens</th><th>Total</th><th>Status</th><th class="acoes">Ações</th></tr></thead>
        <tbody>
        <?php foreach ($pedidos as $p): ?>
            <tr>
                <td><a href="<?= url('admin/pedidos/' . $p['id']) ?>">#<?= $p['id'] ?></a></td>
                <td><?= e($p['cliente']) ?></td>
                <td><?= data_br($p['data_pedido'], true) ?></td>
                <td><?= (int) $p['qtd_itens'] ?></td>
                <td><?= moeda($p['total']) ?></td>
                <td><?= status_pedido($p['status']) ?></td>
                <td class="acoes">
                    <a href="<?= url('admin/pedidos/' . $p['id']) ?>" class="btn btn-sm btn-contorno">Ver</a>
                    <a href="<?= url("admin/pedidos/{$p['id']}/editar") ?>" class="btn btn-sm btn-contorno">Editar</a>
                    <form action="<?= url("admin/pedidos/{$p['id']}/excluir") ?>" method="post" data-confirmar="Excluir o pedido #<?= $p['id'] ?>? Os livros voltarão ao estoque.">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-perigo">Excluir</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$pedidos): ?><tr><td colspan="7" class="texto-suave">Nenhum pedido.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
