INSERT INTO lancamentos (conta_id, categoria_id, usuario_id, status_id, descricao, valor, data)
SELECT
    co.ids[1 + floor(random() * array_length(co.ids, 1))::int],
    ca.ids[1 + floor(random() * array_length(ca.ids, 1))::int],
    us.ids[1 + floor(random() * array_length(us.ids, 1))::int],
    st.id,
    'Lançamento de teste ' || g,
    round((random() * 500 + 1)::numeric, 2),
    DATE '2023-01-01' + floor(random() * 1400)::int
FROM generate_series(1, 100000) AS g,
    (SELECT array_agg(id) AS ids FROM contas) co,
    (SELECT array_agg(id) AS ids FROM categorias WHERE categoria_pai_id IS NOT NULL) ca,
    (SELECT array_agg(id) AS ids FROM usuarios) us,
    (SELECT id FROM status WHERE nome = 'Confirmado' AND aplica_a = 'lancamento') st;