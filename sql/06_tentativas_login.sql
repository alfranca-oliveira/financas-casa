CREATE TABLE tentativas_login (
    id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    email VARCHAR(150) NOT NULL,
    ip VARCHAR(45) NOT NULL,
    sucesso BOOLEAN NOT NULL,
    criado_em TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE INDEX idx_tentativas_email_data ON tentativas_login (email, criado_em);
CREATE INDEX idx_tentativas_ip_data ON tentativas_login (ip, criado_em);