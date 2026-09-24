USE globo_viagens;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    foto VARCHAR(255) NOT NULL DEFAULT 'images/Logo.png',
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS planos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    categoria VARCHAR(40) NOT NULL,
    classe VARCHAR(40) NOT NULL,
    descricao VARCHAR(255) NOT NULL,
    icone VARCHAR(80) NOT NULL DEFAULT 'fa-layer-group',
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO planos (nome, preco, categoria, classe, descricao, icone)
SELECT 'Bronze', 29.90, 'Bronze', 'bronze', 'O essencial para viajar com conforto.', 'fa-compass'
WHERE NOT EXISTS (SELECT 1 FROM planos);
INSERT INTO planos (nome, preco, categoria, classe, descricao, icone)
SELECT 'Prata', 39.90, 'Prata', 'prata', 'Mais estrutura para aproveitar sem pressa.', 'fa-sun'
WHERE (SELECT COUNT(*) FROM planos) = 1;
INSERT INTO planos (nome, preco, categoria, classe, descricao, icone)
SELECT 'Ouro', 49.90, 'Ouro', 'ouro', 'A experiencia completa da Globo Viagens.', 'fa-crown'
WHERE (SELECT COUNT(*) FROM planos) = 2;

CREATE TABLE IF NOT EXISTS destinos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    estado CHAR(2) NOT NULL,
    tipo VARCHAR(120) NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    imagem VARCHAR(255) NOT NULL,
    resumo VARCHAR(255) NOT NULL,
    descricao TEXT NOT NULL,
    localizacao VARCHAR(160) NOT NULL,
    destaque TEXT NOT NULL,
    galeria TEXT NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    status ENUM('ativo', 'em_uso', 'inativo') NOT NULL DEFAULT 'ativo',
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS videos_relatos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(180) NOT NULL,
    autor VARCHAR(120) NOT NULL,
    destino VARCHAR(160) NOT NULL DEFAULT '',
    descricao VARCHAR(220) NOT NULL DEFAULT '',
    url VARCHAR(255) NOT NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO usuarios (nome, email, senha_hash, foto, ativo)
VALUES ('Sistemas S', 'contato@sistemass.app.br','$2a$12$xMGMC.ZjBU.yV5a7o9KEyeJ/VRdnCKu4bG1yLfeQ0QyAam0n2cs.W','images/Logo.png', 1);