<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Entrar · Finanças da Casa</title>
</head>
<body>
    <h1>Entrar</h1>

    <?php if ($erro): ?>
        <p><?= e($erro) ?></p>
    <?php endif; ?>

    <form method="post" action="/login">
        <input type="hidden" name="csrf" value="<?= e(\App\Auth::tokenCsrf()) ?>">

        <label>E-mail
            <input type="email" name="email" value="<?= e($email) ?>" required>
        </label>

        <label>Senha
            <input type="password" name="senha" required>
        </label>

        <button type="submit">Entrar</button>
    </form>
</body>
</html>