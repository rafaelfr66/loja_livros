<div class="barra-acoes">
    <form method="get" class="filtro-inline">
        <input type="search" name="busca" value="<?= e($filtros['busca']) ?>" placeholder="Título, autor ou ISBN">
        <select name="categoria">
            <option value="">Todas as categorias</option>
            <?php foreach ($categorias as $c): ?>
                <option value="<?= $c['id'] ?>" <?= $filtros['categoria'] === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['nome']) ?></option>
            <?php endforeach; ?>
        </select>
        <button class="btn btn-contorno">Filtrar</button>
    </form>
    <a href="<?= url('admin/livros/novo') ?>" class="btn btn-primario">+ Novo livro</a>
</div>

<div class="tabela-wrap">
    <table class="tabela">
        <thead><tr><th></th><th>Título</th><th>Autor</th><th>Categoria</th><th>Preço</th><th>Estoque</th><th>Situação</th><th class="acoes">Ações</th></tr></thead>
        <tbody>
        <?php foreach ($livros as $l): ?>
            <tr>
                <td class="mini-capa"><?php require APP_PATH . '/Views/partials/capa.php'; ?></td>
                <td><strong><?= e($l['titulo']) ?></strong><br><small class="texto-suave"><?= e($l['isbn']) ?></small></td>
                <td><?= e($l['autor']) ?></td>
                <td><?= e($l['categoria']) ?></td>
                <td><?= moeda($l['preco']) ?></td>
                <td><span class="badge <?= $l['estoque'] == 0 ? 'badge-cancelado' : ($l['estoque'] <= 5 ? 'badge-pendente' : 'badge-entregue') ?>"><?= (int) $l['estoque'] ?></span></td>
                <td>
                    <?= $l['ativo'] ? 'Ativo' : '<span class="texto-suave">Inativo</span>' ?>
                    <?= $l['destaque'] ? ' ★' : '' ?>
                </td>
                <td class="acoes">
                    <a href="<?= url("admin/livros/{$l['id']}/editar") ?>" class="btn btn-sm btn-contorno">Editar</a>
                    <form action="<?= url("admin/livros/{$l['id']}/excluir") ?>" method="post" data-confirmar="Excluir o livro <?= e($l['titulo']) ?>?">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-perigo">Excluir</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$livros): ?><tr><td colspan="8" class="texto-suave">Nenhum livro encontrado.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
