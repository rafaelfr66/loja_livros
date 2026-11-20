<?php $acao = $cliente ? "admin/clientes/{$cliente['id']}/atualizar" : 'admin/clientes/salvar'; ?>
<form action="<?= url($acao) ?>" method="post" class="painel form-admin" data-validar novalidate>
    <?= csrf_field() ?>
    <div class="linha-campos">
        <label>Nome *
            <input type="text" name="nome" value="<?= e(old('nome', $cliente['nome'] ?? '')) ?>" required minlength="3" maxlength="100">
        </label>
        <label>E-mail *
            <input type="email" name="email" value="<?= e(old('email', $cliente['email'] ?? '')) ?>" required maxlength="150">
        </label>
    </div>
    <div class="linha-campos">
        <label>Telefone *
            <input type="tel" name="telefone" value="<?= e(old('telefone', $cliente['telefone'] ?? '')) ?>" required
                   data-mascara="telefone" pattern="\(\d{2}\) \d{4,5}-\d{4}" placeholder="(11) 99999-9999">
        </label>
        <label>Senha <?= $cliente ? '(deixe em branco para manter)' : '*' ?>
            <input type="password" name="senha" minlength="6" <?= $cliente ? '' : 'required' ?>>
        </label>
    </div>
    <label>Endereço
        <input type="text" name="endereco" value="<?= e(old('endereco', $cliente['endereco'] ?? '')) ?>" maxlength="255">
    </label>
    <div class="form-botoes">
        <a href="<?= url('admin/clientes') ?>" class="btn btn-contorno">Cancelar</a>
        <button class="btn btn-primario">Salvar</button>
    </div>
</form>
