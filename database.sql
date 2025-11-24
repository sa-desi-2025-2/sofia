CREATE DATABASE ong_animais;
USE ong_animais;

CREATE TABLE usuarios (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('voluntario','ong','admin') NOT NULL DEFAULT 'voluntario',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO usuarios (id_usuario, email, senha, tipo, criado_em) VALUES
(20, 'souza.sofiaalves@gmail.com', '3c9909afec25354d551dae21590bb26e38d53f2173b8d3dc3eee4c047e7ab1c1eb8b85103e3be7ba613b31bb5c9c36214dc9f14a42fd7a2fdb84856bca5c44c2', 'voluntario', '2025-11-12 19:01:01'),
(22, 'lucas@lucas.lucas', '3c9909afec25354d551dae21590bb26e38d53f2173b8d3dc3eee4c047e7ab1c1eb8b85103e3be7ba613b31bb5c9c36214dc9f14a42fd7a2fdb84856bca5c44c2', 'voluntario', '2025-11-19 17:46:34'),
(23, 'robertinho@gmail.com', 'd92b083d6fe341d2cf99fe2c604265e4ae840a8f785fe9942ae0a1dcfd95f5c386110fdee8831965891ae3c8cc01e6d73b094ffd7906ab781da8419de06ef662', 'voluntario', '2025-11-19 17:54:10'),
(24, 'bat@man.com', 'c11033e2755bc3472afb298cb3e85cd53472e459e49ce9dc0f63948d7ff6bcfd9febc260ad8bfbe6d64a280e0db033244b00b5d0eb820a96e34be861d956bebf', 'voluntario', '2025-11-19 18:10:22'),
(27, 'rob@in.com', 'c11033e2755bc3472afb298cb3e85cd53472e459e49ce9dc0f63948d7ff6bcfd9febc260ad8bfbe6d64a280e0db033244b00b5d0eb820a96e34be861d956bebf', 'voluntario', '2025-11-19 18:14:12'),
(28, 'robi@in.com', '276b40244adbc95c3afec34f5b70844987240d46878b702cec73671ae8cdaeab33cb64f3a495504f9f52b04c0fe94d59685188b1ecb3d115a18e750023255c11', 'voluntario', '2025-11-19 18:29:56'),
(31, 'sim@sim.sim', 'd404559f602eab6fd602ac7680dacbfaadd13630335e951f097af3900e9de176b6db28512f2e000b9d04fba5133e8b1c6e8df59db3a8ab9d60be4b97cc9e81db', 'ong', '2025-11-19 19:23:28');


CREATE TABLE ongs (
    id_ong INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    telefone VARCHAR(30),
    cnpj VARCHAR(30),
    endereco VARCHAR(255),
    cep VARCHAR(12) NOT NULL,
    descricao TEXT,
    imagem VARCHAR(255),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);

INSERT INTO ongs VALUES
(1, 31, 'ong', 'sim@sim.sim', '123', '123', 'Rua Coimbra, 155', '89212-110',
 'A melhor ong do mundo, talvez até do Brasil.', 'uploads/ongs/31.png', '2025-11-19 19:23:28');


CREATE TABLE voluntarios (
    id_voluntario INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    telefone VARCHAR(30),
    cpf VARCHAR(20) UNIQUE,
    endereco VARCHAR(255),
    complemento VARCHAR(255),
    cidade VARCHAR(255) NOT NULL,
    estado VARCHAR(2) NOT NULL,
    cep VARCHAR(12) NOT NULL,
    qualificacoes TEXT,
    imagem VARCHAR(155),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);

INSERT INTO voluntarios VALUES
(1, 20, 'sofia', 'souza.sofiaalves@gmail.com', '(47) 99791-3890', '12738615902',
 'Rua Coimbra, 155', 'Apartamento 201', 'Joinville', 'Sa', '89212-110', 'sim', NULL, '2025-11-12 19:01:01'),
(2, 22, 'lucas', 'lucas@lucas.lucas', '47988489411', '08451011969',
 'sim', 'receba', 'joinville', 'Sa', '89218001', 'todas', NULL, '2025-11-19 17:46:34'),
(3, 23, 'pattinson', 'robertinho@gmail.com', '456', '123',
 'Rua Coimbra, 155', 'Apartamento 202', 'Joinville', 'PE', '89218001', 'lufa lufa', NULL, '2025-11-19 17:54:10'),
(4, 24, 'bruce', 'bat@man.com', '45678', '678',
 'Rua Coimbra, 155', 'Apartamento 203', 'Joinville', 'AP', '12345', 'batman', NULL, '2025-11-19 18:10:22'),
(6, 27, 'bruce', 'rob@in.com', '45678', '6789',
 'Rua Coimbra, 155', 'Apartamento 204', 'Joinville', 'AP', '12345', 'batman', 'uploads/voluntarios/27.png', '2025-11-19 18:14:12');


CREATE TABLE ongs_voluntarios (
    id_ong INT NOT NULL,
    id_voluntario INT NOT NULL,
    data_inscricao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_ong, id_voluntario),
    FOREIGN KEY (id_ong) REFERENCES ongs(id_ong) ON DELETE CASCADE,
    FOREIGN KEY (id_voluntario) REFERENCES voluntarios(id_voluntario) ON DELETE CASCADE
);

CREATE TABLE convites_ongs_voluntarios (
    id_convite INT PRIMARY KEY AUTO_INCREMENT,
    id_ong INT NOT NULL,
    id_voluntario INT NOT NULL,
    status ENUM('pendente','aceito','recusado') NOT NULL DEFAULT 'pendente',
    data_convite TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_resposta TIMESTAMP NULL,
    FOREIGN KEY (id_ong) REFERENCES ongs(id_ong) ON DELETE CASCADE,
    FOREIGN KEY (id_voluntario) REFERENCES voluntarios(id_voluntario) ON DELETE CASCADE,
    UNIQUE KEY unique_convite_pendente (id_ong, id_voluntario, status)
);
