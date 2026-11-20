<div class="barra-acoes">
    <form method="get" class="filtro-inline">
        <input type="search" name="busca" value="<?= e($busca) ?>" placeholder="Nome ou e-mail">
        <button class="btn btn-contorno">Buscar</button>
    </form>
    <a href="<?= url('admin/clientes/novo') ?>" class="btn btn-primario">+ Novo cliente</a>
</div>

<div class="tabela-wrap">
    <table class="tabela">
        <thead><tr><th>Nome</th><th>E-mail</th><th>Telefone</th><th>Pedidos</th><th>Total gasto</th><th>Cliente desde</th><th class="acoes">Ações</th></tr></thead>
        <tbody>
        <?php foreach ($clientes as $c): ?>
            <tr>
                <td><strong><?= e($c['nome']) ?></strong></td>
                <td><?= e($c['email']) ?></td>
                <td><?= e($c['telefone']) ?></td>
                <td><?= (int) $c['qtd_pedidos'] ?></td>
                <td><?= moeda($c['total_gasto']) ?></td>
                <td><?= data_br($c['criado_em']) ?></td>
                <td class="acoes">
                    <a href="<?= url("admin/clientes/{$c['id']}/editar") ?>" class="btn btn-sm btn-contorno">Editar</a>
                    <form action="<?= url("admin/clientes/{$c['id']}/excluir") ?>" method="post" data-confirmar="Excluir o cliente <?= e($c['nome']) ?>?">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-perigo">Excluir</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$clientes): ?><tr><td colspan="7" class="texto-suave">Nenhum cliente encontrado.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
