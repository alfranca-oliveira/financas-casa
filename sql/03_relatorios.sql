-- 1. Gastos de outubro por grupo de categoria
SELECT COALESCE(pai.nome, c.nome) AS grupo, SUM(l.valor) AS total
FROM lancamentos l
JOIN categorias c ON c.id = l.categoria_id
LEFT JOIN categorias pai ON pai.id = c.categoria_pai_id
JOIN status s ON s.id = l.status_id
WHERE c.tipo = 'despesa'
  AND s.nome = 'Confirmado'
  AND l.data >= DATE '2026-10-01' AND l.data < DATE '2026-11-01'
GROUP BY grupo
ORDER BY total DESC;

-- 2. Receitas e despesas por mês
SELECT date_trunc('month', l.data)::date AS mes,
       SUM(l.valor) FILTER (WHERE c.tipo = 'receita') AS receitas,
       SUM(l.valor) FILTER (WHERE c.tipo = 'despesa') AS despesas
FROM lancamentos l
JOIN categorias c ON c.id = l.categoria_id
GROUP BY mes
ORDER BY mes;

-- 3. Quanto cada pessoa lançou de despesa
SELECT u.nome, SUM(l.valor) AS total_gasto
FROM lancamentos l
JOIN usuarios u ON u.id = l.usuario_id
JOIN categorias c ON c.id = l.categoria_id
WHERE c.tipo = 'despesa'
GROUP BY u.nome
ORDER BY total_gasto DESC;

-- 4. Saldo atual de cada conta
SELECT co.nome,
       co.saldo_inicial + COALESCE(SUM(
           CASE WHEN c.tipo = 'receita' THEN l.valor ELSE -l.valor END
       ), 0) AS saldo_atual
FROM contas co
LEFT JOIN lancamentos l ON l.conta_id = co.id
LEFT JOIN categorias c ON c.id = l.categoria_id
GROUP BY co.id, co.nome, co.saldo_inicial
ORDER BY co.nome;

-- 5. Contas a pagar, com a situação calculada na hora
SELECT le.descricao, le.vencimento, s.nome AS status,
       CASE
           WHEN s.nome IN ('A pagar', 'Agendado', 'Pago parcialmente')
                AND le.vencimento < CURRENT_DATE THEN 'Atrasada'
           ELSE s.nome
       END AS situacao
FROM lembretes le
JOIN status s ON s.id = le.status_id
ORDER BY le.vencimento;