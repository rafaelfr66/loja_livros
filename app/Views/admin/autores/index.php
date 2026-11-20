<div class="barra-acoes">
    <p class="texto-suave"><?= count($autores) ?> autor(es)</p>
    <a href="<?= url('admin/autores/novo') ?>" class="btn btn-primario">+ Novo autor</a>
</div>

<div class="tabela-wrap">
    <table class="tabela">
        <thead><tr><th>Nome</th><th>Nacionalidade</th><th>Livros</th><th class="acoes">Ações</th></tr></thead>
        <tbody>
        <?php foreach ($autores as $a): ?>
            <tr>
                <td><strong><?= e($a['nome']) ?></strong></td>
                <td><?= e($a['nacionalidade']) ?></td>
                <td><?= (int) $a['qtd_livros'] ?></td>
                <td class="acoes">
                    <a href="<?= url("admin/autores/{$a['id']}/editar") ?>" class="btn btn-sm btn-contorno">Editar</a>
                    <form action="<?= url("admin/autores/{$a['id']}/excluir") ?>" method="post" data-confirmar="Excluir o autor <?= e($a['nome']) ?>?">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-perigo">Excluir</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
