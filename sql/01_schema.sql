CREATE TABLE usuarios (
    id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha_hash VARCHAR(255),
    perfil VARCHAR(20) NOT NULL DEFAULT 'membro'
        CHECK (perfil IN ('admin', 'membro', 'leitura')),
    criado_em TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE contas (
    id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    banco VARCHAR(100),
    titular_id INT REFERENCES usuarios(id),
    tipo VARCHAR(20) NOT NULL
        CHECK (tipo IN ('corrente', 'poupanca', 'cartao', 'dinheiro')),
    saldo_inicial NUMERIC(12,2) NOT NULL DEFAULT 0,
    criado_em TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE categorias (
    id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    tipo VARCHAR(10) NOT NULL CHECK (tipo IN ('receita', 'despesa')),
    natureza VARCHAR(10) CHECK (natureza IN ('fixa', 'variavel')),
    categoria_pai_id INT REFERENCES categorias(id),
    CHECK (tipo = 'receita' OR natureza IS NOT NULL)
);

CREATE TABLE status (
    id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    nome VARCHAR(30) NOT NULL,
    aplica_a VARCHAR(15) NOT NULL CHECK (aplica_a IN ('lancamento', 'lembrete')),
    UNIQUE (nome, aplica_a)
);

CREATE TABLE lancamentos (
    id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    conta_id INT NOT NULL REFERENCES contas(id),
    categoria_id INT NOT NULL REFERENCES categorias(id),
    usuario_id INT NOT NULL REFERENCES usuarios(id),
    status_id INT NOT NULL REFERENCES status(id),
    descricao VARCHAR(200) NOT NULL,
    valor NUMERIC(12,2) NOT NULL CHECK (valor > 0),
    data DATE NOT NULL,
    criado_em TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE orcamentos (
    id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    categoria_id INT NOT NULL REFERENCES categorias(id),
    mes DATE NOT NULL,
    valor_limite NUMERIC(12,2) NOT NULL CHECK (valor_limite > 0),
    UNIQUE (categoria_id, mes)
);

CREATE TABLE investimentos (
    id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    tipo VARCHAR(20) NOT NULL
        CHECK (tipo IN ('poupanca', 'cdb', 'tesouro', 'acoes', 'fundos', 'cripto', 'outro')),
    instituicao VARCHAR(100),
    criado_em TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE movimentacoes_investimento (
    id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    investimento_id INT NOT NULL REFERENCES investimentos(id),
    conta_id INT REFERENCES contas(id),
    usuario_id INT NOT NULL REFERENCES usuarios(id),
    tipo VARCHAR(15) NOT NULL CHECK (tipo IN ('aporte', 'resgate', 'rendimento')),
    valor NUMERIC(12,2) NOT NULL CHECK (valor > 0),
    data DATE NOT NULL,
    criado_em TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE lembretes (
    id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    descricao VARCHAR(200) NOT NULL,
    valor_previsto NUMERIC(12,2) CHECK (valor_previsto > 0),
    vencimento DATE NOT NULL,
    recorrente BOOLEAN NOT NULL DEFAULT false,
    status_id INT NOT NULL REFERENCES status(id),
    categoria_id INT REFERENCES categorias(id),
    conta_id INT REFERENCES contas(id),
    lancamento_id INT REFERENCES lancamentos(id),
    criado_por INT NOT NULL REFERENCES usuarios(id),
    criado_em TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE notificacoes (
    id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    usuario_id INT NOT NULL REFERENCES usuarios(id),
    lembrete_id INT REFERENCES lembretes(id),
    mensagem VARCHAR(255) NOT NULL,
    lida BOOLEAN NOT NULL DEFAULT false,
    criado_em TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE historico (
    id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    usuario_id INT REFERENCES usuarios(id),
    acao VARCHAR(10) NOT NULL
        CHECK (acao IN ('criou', 'editou', 'apagou', 'login')),
    tabela VARCHAR(50),
    registro_id INT,
    dados_antes JSONB,
    dados_depois JSONB,
    ip VARCHAR(45),
    criado_em TIMESTAMPTZ NOT NULL DEFAULT now()
);