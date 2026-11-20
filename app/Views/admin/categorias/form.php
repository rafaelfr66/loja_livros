<?php $acao = $categoria ? "admin/categorias/{$categoria['id']}/atualizar" : 'admin/categorias/salvar'; ?>
<form action="<?= url($acao) ?>" method="post" class="painel form-admin" data-validar novalidate>
    <?= csrf_field() ?>
    <label>Nome *
        <input type="text" name="nome" value="<?= e(old('nome', $categoria['nome'] ?? '')) ?>" required minlength="3" maxlength="60">
    </label>
    <label>Descrição
        <textarea name="descricao" rows="3" maxlength="255"><?= e(old('descricao', $categoria['descricao'] ?? '')) ?></textarea>
    </label>
    <div class="form-botoes">
        <a href="<?= url('admin/categorias') ?>" class="btn btn-contorno">Cancelar</a>
        <button class="btn btn-primario">Salvar</button>
    </div>
</form>
