<div class="caixa-form">
    <h1>Criar conta</h1>
    <p class="subtitulo">Cadastre-se para comprar e acompanhar seus pedidos.</p>
    <form action="<?= url('cadastro') ?>" method="post" data-validar novalidate>
        <?= csrf_field() ?>
        <label>Nome completo
            <input type="text" name="nome" value="<?= e(old('nome')) ?>" required minlength="3" maxlength="100">
        </label>
        <label>E-mail
            <input type="email" name="email" value="<?= e(old('email')) ?>" required maxlength="150">
        </label>
        <label>Telefone
            <input type="tel" name="telefone" value="<?= e(old('telefone')) ?>" required data-mascara="telefone"
                   placeholder="(11) 99999-9999" pattern="\(\d{2}\) \d{4,5}-\d{4}">
        </label>
        <label>Endereço (opcional)
            <input type="text" name="endereco" value="<?= e(old('endereco')) ?>" maxlength="255">
        </label>
        <div class="linha-campos">
            <label>Senha
                <input type="password" name="senha" required minlength="6">
            </label>
            <label>Confirmar senha
                <input type="password" name="senha_confirmacao" required data-igual="senha">
            </label>
        </div>
        <button class="btn btn-primario btn-bloco">Criar conta</button>
    </form>
    <p class="rodape-form">Já tem conta? <a href="<?= url('entrar') ?>">Entrar</a></p>
</div>
