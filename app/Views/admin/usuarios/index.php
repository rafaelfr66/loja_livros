<div class="barra-acoes">
    <p class="texto-suave">Funcionários com acesso à área administrativa.</p>
    <a href="<?= url('admin/usuarios/novo') ?>" class="btn btn-primario">+ Novo usuário</a>
</div>

<div class="tabela-wrap">
    <table class="tabela">
        <thead><tr><th>Nome</th><th>E-mail</th><th>Perfil</th><th>Situação</th><th>Criado em</th><th class="acoes">Ações</th></tr></thead>
        <tbody>
        <?php foreach ($usuarios as $u): ?>
            <tr>
                <td><strong><?= e($u['nome']) ?></strong></td>
                <td><?= e($u['email']) ?></td>
                <td><span class="badge"><?= e($u['perfil']) ?></span></td>
                <td><?= $u['ativo'] ? 'Ativo' : '<span class="texto-suave">Inativo</span>' ?></td>
                <td><?= data_br($u['criado_em']) ?></td>
                <td class="acoes">
                    <a href="<?= url("admin/usuarios/{$u['id']}/editar") ?>" class="btn btn-sm btn-contorno">Editar</a>
                    <?php if ((int) $u['id'] !== App\Core\Auth::usuario()['id']): ?>
                        <form action="<?= url("admin/usuarios/{$u['id']}/excluir") ?>" method="post" data-confirmar="Excluir o usuário <?= e($u['nome']) ?>?">
                            <?= csrf_field() ?>
                            <button class="btn btn-sm btn-perigo">Excluir</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
