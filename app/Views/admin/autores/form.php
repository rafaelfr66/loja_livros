<?php $acao = $autor ? "admin/autores/{$autor['id']}/atualizar" : 'admin/autores/salvar'; ?>
<form action="<?= url($acao) ?>" method="post" class="painel form-admin" data-validar novalidate>
    <?= csrf_field() ?>
    <div class="linha-campos">
        <label>Nome *
            <input type="text" name="nome" value="<?= e(old('nome', $autor['nome'] ?? '')) ?>" required minlength="3" maxlength="120">
        </label>
        <label>Nacionalidade
            <input type="text" name="nacionalidade" value="<?= e(old('nacionalidade', $autor['nacionalidade'] ?? '')) ?>" maxlength="60">
        </label>
    </div>
    <label>Biografia
        <textarea name="biografia" rows="5"><?= e(old('biografia', $autor['biografia'] ?? '')) ?></textarea>
    </label>
    <div class="form-botoes">
        <a href="<?= url('admin/autores') ?>" class="btn btn-contorno">Cancelar</a>
        <button class="btn btn-primario">Salvar</button>
    </div>
</form>
