<h1>Lançamentos</h1>
<?php if (\App\Auth::podeEditar()): ?>
    <a href="#">+ Novo lançamento</a>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>Data</th>
            <th>Descrição</th>
            <th>Categoria</th>
            <th>Conta</th>
            <th>Pessoa</th>
            <th>Valor</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($lancamentos as $l): ?>
            <tr>
                <td><?= e(date('d/m/Y', strtotime($l['data']))) ?></td>
                <td><?= e($l['descricao']) ?></td>
                <td><?= e($l['categoria']) ?></td>
                <td><?= e($l['conta']) ?></td>
                <td><?= e($l['pessoa']) ?></td>
                <td><?= $l['tipo'] === 'receita' ? '+' : '-' ?> <?= dinheiro($l['valor']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<p>
    <?php if ($pagina > 1): ?>
        <a href="/lancamentos?pagina=<?= $pagina - 1 ?>">Anterior</a>
    <?php endif; ?>
    Página <?= $pagina ?>
    <a href="/lancamentos?pagina=<?= $pagina + 1 ?>">Próxima</a>
</p>