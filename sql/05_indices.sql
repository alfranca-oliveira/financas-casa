CREATE INDEX idx_lancamentos_data ON lancamentos (data);
CREATE INDEX idx_lancamentos_conta ON lancamentos (conta_id);
CREATE INDEX idx_lancamentos_categoria ON lancamentos (categoria_id);
CREATE INDEX idx_lancamentos_usuario ON lancamentos (usuario_id);
ANALYZE lancamentos;