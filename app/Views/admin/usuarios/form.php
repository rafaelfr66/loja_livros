<?php
$acao = $usuario ? "admin/usuarios/{$usuario['id']}/atualizar" : 'admin/usuarios/salvar';
$perfil = old('perfil', $usuario['perfil'] ?? 'funcionario');
$ativo = isset($_SESSION['old']) ? !empty($_SESSION['old']['ativo']) : (bool) ($usuario['ativo'] ?? true);
?>
<form action="<?= url($acao) ?>" method="post" class="painel form-admin" data-validar novalidate>
    <?= csrf_field() ?>
    <div class="linha-campos">
        <label>Nome *
            <input type="text" name="nome" value="<?= e(old('nome', $usuario['nome'] ?? '')) ?>" required minlength="3" maxlength="100">
        </label>
        <label>E-mail *
            <input type="email" name="email" value="<?= e(old('email', $usuario['email'] ?? '')) ?>" required maxlength="150">
        </label>
    </div>
    <div class="linha-campos">
        <label>Perfil
            <select name="perfil">
                <option value="funcionario" <?= $perfil === 'funcionario' ? 'selected' : '' ?>>Funcionário</option>
                <option value="admin" <?= $perfil === 'admin' ? 'selected' : '' ?>>Administrador</option>
            </select>
        </label>
        <label>Senha <?= $usuario ? '(deixe em branco para manter)' : '*' ?>
            <input type="password" name="senha" minlength="6" <?= $usuario ? '' : 'required' ?>>
        </label>
    </div>
    <label class="check"><input type="checkbox" name="ativo" value="1" <?= $ativo ? 'checked' : '' ?>> Usuário ativo (pode fazer login)</label>
    <div class="form-botoes">
        <a href="<?= url('admin/usuarios') ?>" class="btn btn-contorno">Cancelar</a>
        <button class="btn btn-primario">Salvar</button>
    </div>
</form>
