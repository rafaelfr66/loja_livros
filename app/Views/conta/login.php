<div class="caixa-form">
    <h1>Entrar</h1>
    <p class="subtitulo">Acesse sua conta para finalizar compras e acompanhar pedidos.</p>
    <form action="<?= url('entrar') ?>" method="post" data-validar novalidate>
        <?= csrf_field() ?>
        <label>E-mail
            <input type="email" name="email" value="<?= e(old('email')) ?>" required autofocus>
        </label>
        <label>Senha
            <input type="password" name="senha" required minlength="6">
        </label>
        <button class="btn btn-primario btn-bloco">Entrar</button>
    </form>
    <p class="rodape-form">Ainda não tem conta? <a href="<?= url('cadastro') ?>">Cadastre-se</a></p>
    <p class="dica">Teste: <code>ana@email.com</code> / <code>cliente123</code></p>
</div>
