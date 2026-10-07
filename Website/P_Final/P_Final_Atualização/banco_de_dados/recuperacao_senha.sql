USE champions_sport;

CREATE TABLE IF NOT EXISTS recuperacao_senha(
    id_recuperacao INT PRIMARY KEY AUTO_INCREMENT,
    id_cadastro_usuario INT NOT NULL,
    token_hash CHAR(64) NOT NULL UNIQUE,
    expira_em DATETIME NOT NULL,
    usado_em DATETIME NULL DEFAULT NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_recuperacao_usuario_criado (id_cadastro_usuario, criado_em),
    FOREIGN KEY (id_cadastro_usuario)
        REFERENCES cadastro_usuario(id_cadastro_usuario)
);
