<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo ?? '') ?> · Entrelinhas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,700&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="pagina-login">
    <?php foreach (flashes() as $f): ?>
        <div class="alerta alerta-<?= e($f['tipo']) ?> alerta-flutuante"><?= e($f['mensagem']) ?></div>
    <?php endforeach; ?>
    <?= $conteudo ?>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
