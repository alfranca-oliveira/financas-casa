-- Status
INSERT INTO status (nome, aplica_a) VALUES
    ('A pagar', 'lembrete'),
    ('Agendado', 'lembrete'),
    ('Pago parcialmente', 'lembrete'),
    ('Pago', 'lembrete'),
    ('Cancelado', 'lembrete'),
    ('Confirmado', 'lancamento'),
    ('Previsto', 'lancamento'),
    ('Estornado', 'lancamento'),
    ('Cancelado', 'lancamento');

-- Usuários fictícios (a senha entra no Dia 3)
INSERT INTO usuarios (nome, email, perfil) VALUES
    ('Ana Teste', 'ana@exemplo.com', 'admin'),
    ('Bruno Teste', 'bruno@exemplo.com', 'membro'),
    ('Carla Teste', 'carla@exemplo.com', 'leitura');

-- Contas
INSERT INTO contas (nome, banco, titular_id, tipo, saldo_inicial) VALUES
    ('Conta da casa', 'Banco Exemplo', NULL, 'corrente', 2500.00),
    ('Cartão da Ana', 'Banco Exemplo', (SELECT id FROM usuarios WHERE email = 'ana@exemplo.com'), 'cartao', 0),
    ('Poupança do Bruno', 'Outro Banco', (SELECT id FROM usuarios WHERE email = 'bruno@exemplo.com'), 'poupanca', 1000.00);

-- Categorias principais
INSERT INTO categorias (nome, tipo, natureza) VALUES
    ('Moradia', 'despesa', 'fixa'),
    ('Alimentação', 'despesa', 'variavel'),
    ('Pet', 'despesa', 'variavel'),
    ('Assinaturas', 'despesa', 'fixa'),
    ('Transporte', 'despesa', 'variavel'),
    ('Lazer', 'despesa', 'variavel'),
    ('Salário', 'receita', NULL),
    ('Freelas', 'receita', NULL);

-- Subcategorias, ligadas à categoria principal pelo nome
INSERT INTO categorias (nome, tipo, natureza, categoria_pai_id)
SELECT v.nome, 'despesa', v.natureza, c.id
FROM (VALUES
    ('Aluguel', 'fixa', 'Moradia'),
    ('Luz', 'fixa', 'Moradia'),
    ('Água', 'fixa', 'Moradia'),
    ('Internet', 'fixa', 'Moradia'),
    ('Mercado', 'variavel', 'Alimentação'),
    ('Delivery', 'variavel', 'Alimentação'),
    ('Ração', 'variavel', 'Pet'),
    ('Veterinário', 'variavel', 'Pet'),
    ('Streaming', 'fixa', 'Assinaturas'),
    ('Combustível', 'variavel', 'Transporte')
) AS v(nome, natureza, pai)
JOIN categorias c ON c.nome = v.pai;

-- Lançamentos de outubro
INSERT INTO lancamentos (conta_id, categoria_id, usuario_id, status_id, descricao, valor, data)
SELECT co.id, ca.id, u.id, s.id, v.descricao, v.valor, v.data
FROM (VALUES
    ('Conta da casa', 'Salário', 'ana@exemplo.com', 'Salário de outubro', 5000.00, DATE '2026-10-05'),
    ('Conta da casa', 'Aluguel', 'ana@exemplo.com', 'Aluguel de outubro', 1800.00, DATE '2026-10-10'),
    ('Conta da casa', 'Luz', 'bruno@exemplo.com', 'Conta de luz', 210.50, DATE '2026-10-15'),
    ('Cartão da Ana', 'Mercado', 'ana@exemplo.com', 'Mercado do mês', 780.35, DATE '2026-10-12'),
    ('Cartão da Ana', 'Delivery', 'ana@exemplo.com', 'Pizza', 89.90, DATE '2026-10-18'),
    ('Conta da casa', 'Ração', 'bruno@exemplo.com', 'Ração do pet', 150.00, DATE '2026-10-20'),
    ('Cartão da Ana', 'Streaming', 'ana@exemplo.com', 'Streaming', 55.90, DATE '2026-10-22')
) AS v(conta, categoria, email, descricao, valor, data)
JOIN contas co ON co.nome = v.conta
JOIN categorias ca ON ca.nome = v.categoria
JOIN usuarios u ON u.email = v.email
JOIN status s ON s.nome = 'Confirmado' AND s.aplica_a = 'lancamento';

-- Uma conta a pagar
INSERT INTO lembretes (descricao, valor_previsto, vencimento, recorrente, status_id, categoria_id, conta_id, criado_por)
SELECT 'Internet', 120.00, DATE '2026-11-10', true, s.id, ca.id, co.id, u.id
FROM status s, categorias ca, contas co, usuarios u
WHERE s.nome = 'A pagar' AND s.aplica_a = 'lembrete'
  AND ca.nome = 'Internet'
  AND co.nome = 'Conta da casa'
  AND u.email = 'ana@exemplo.com';

-- Orçamento de mercado para novembro
INSERT INTO orcamentos (categoria_id, mes, valor_limite)
SELECT id, DATE '2026-11-01', 900.00 FROM categorias WHERE nome = 'Mercado';