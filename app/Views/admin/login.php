<div class="login-admin">
    <div class="caixa-form">
        <a href="<?= url('') ?>" class="logo">
            <span class="logo-icone">E</span>
            <span>Entrelinhas<small>administração</small></span>
        </a>
        <h1>Acesso restrito</h1>
        <p class="subtitulo">Entre com seu usuário de funcionário.</p>
        <form action="<?= url('admin/login') ?>" method="post" data-validar novalidate>
            <?= csrf_field() ?>
            <label>E-mail
                <input type="email" name="email" value="<?= e(old('email')) ?>" required autofocus>
            </label>
            <label>Senha
                <input type="password" name="senha" required>
            </label>
            <button class="btn btn-primario btn-bloco">Entrar</button>
        </form>
        <p class="dica">Teste: <code>admin@entrelinhas.com</code> / <code>admin123</code></p>
        <a href="<?= url('') ?>" class="link-discreto">← Voltar para a loja</a>
    </div>
</div>
