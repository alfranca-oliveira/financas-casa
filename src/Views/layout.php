<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo) ?> · Finanças da Casa</title>
</head>
<body>
    <?php $usuario = \App\Auth::usuario(); ?>
    <nav>
        <a href="/">Painel</a> |
        <a href="/lancamentos">Lançamentos</a> |
        <?= e($usuario['nome']) ?> (<?= e($usuario['perfil']) ?>)
        <form method="post" action="/sair" style="display:inline">
            <input type="hidden" name="csrf" value="<?= e(\App\Auth::tokenCsrf()) ?>">
            <button type="submit">Sair</button>
        </form>
    </nav>
    <main>
        <?= $conteudo ?>
    </main>
</body>
</html>