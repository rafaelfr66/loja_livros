<div class="barra-acoes">
    <p class="texto-suave"><?= count($categorias) ?> categoria(s)</p>
    <a href="<?= url('admin/categorias/novo') ?>" class="btn btn-primario">+ Nova categoria</a>
</div>

<div class="tabela-wrap">
    <table class="tabela">
        <thead><tr><th>Nome</th><th>Descrição</th><th>Livros ativos</th><th class="acoes">Ações</th></tr></thead>
        <tbody>
        <?php foreach ($categorias as $c): ?>
            <tr>
                <td><strong><?= e($c['nome']) ?></strong></td>
                <td><?= e($c['descricao']) ?></td>
                <td><?= (int) $c['qtd_livros'] ?></td>
                <td class="acoes">
                    <a href="<?= url("admin/categorias/{$c['id']}/editar") ?>" class="btn btn-sm btn-contorno">Editar</a>
                    <form action="<?= url("admin/categorias/{$c['id']}/excluir") ?>" method="post" data-confirmar="Excluir a categoria <?= e($c['nome']) ?>?">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-perigo">Excluir</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
