<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Database;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$db = Database::conectar();

$total = $db->query('SELECT COUNT(*) FROM lancamentos')->fetchColumn();

$stmt = $db->prepare(
    'SELECT descricao, valor, data
     FROM lancamentos
     WHERE data >= :inicio AND data < :fim
     ORDER BY data
     LIMIT 10'
);
$stmt->execute(['inicio' => '2026-10-01', 'fim' => '2026-11-01']);
$lancamentos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Finanças da Casa</title>
</head>
<body>
    <h1>Finanças da Casa</h1>
    <p>Total de lançamentos no banco: <?= number_format($total, 0, ',', '.') ?></p>

    <h2>Primeiros lançamentos de outubro</h2>
    <ul>
        <?php foreach ($lancamentos as $l): ?>
            <li>
                <?= htmlspecialchars($l['data']) ?> ·
                <?= htmlspecialchars($l['descricao']) ?> ·
                R$ <?= number_format($l['valor'], 2, ',', '.') ?>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>