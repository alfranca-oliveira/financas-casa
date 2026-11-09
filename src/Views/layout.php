<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo) ?> · Finanças da Casa</title>
</head>
<body>
    <nav>
        <a href="/">Painel</a> |
        <a href="/lancamentos">Lançamentos</a>
    </nav>
    <main>
        <?= $conteudo ?>
    </main>
</body>
</html>