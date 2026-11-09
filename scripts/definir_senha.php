<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Database;

Dotenv\Dotenv::createImmutable(__DIR__ . '/..')->load();

if ($argc !== 3) {
    echo "Uso: php scripts/definir_senha.php EMAIL SENHA\n";
    exit(1);
}

[, $email, $senha] = $argv;

$hash = password_hash($senha, PASSWORD_DEFAULT);

$stmt = Database::conectar()->prepare(
    'UPDATE usuarios SET senha_hash = :hash WHERE email = :email'
);
$stmt->execute(['hash' => $hash, 'email' => $email]);

echo $stmt->rowCount() > 0
    ? "Senha definida para $email\n"
    : "Usuário não encontrado\n";