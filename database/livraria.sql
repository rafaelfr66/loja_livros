SET NAMES utf8mb4;

DROP DATABASE IF EXISTS livraria;
CREATE DATABASE livraria CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE livraria;

CREATE TABLE usuarios (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome        VARCHAR(100) NOT NULL,
    email       VARCHAR(150) NOT NULL UNIQUE,
    senha       VARCHAR(255) NOT NULL,
    perfil      ENUM('admin','funcionario') NOT NULL DEFAULT 'funcionario',
    ativo       TINYINT(1) NOT NULL DEFAULT 1,
    criado_em   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE clientes (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome        VARCHAR(100) NOT NULL,
    email       VARCHAR(150) NOT NULL UNIQUE,
    telefone    VARCHAR(20)  NOT NULL,
    senha       VARCHAR(255) NOT NULL,
    endereco    VARCHAR(255) NULL,
    criado_em   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categorias (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome        VARCHAR(60) NOT NULL UNIQUE,
    descricao   VARCHAR(255) NULL
) ENGINE=InnoDB;

CREATE TABLE autores (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome           VARCHAR(120) NOT NULL,
    nacionalidade  VARCHAR(60)  NULL,
    biografia      TEXT NULL
) ENGINE=InnoDB;

CREATE TABLE livros (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo          VARCHAR(150) NOT NULL,
    isbn            VARCHAR(20)  NOT NULL UNIQUE,
    categoria_id    INT UNSIGNED NOT NULL,
    autor_id        INT UNSIGNED NOT NULL,
    editora         VARCHAR(100) NULL,
    ano_publicacao  SMALLINT NULL,
    paginas         SMALLINT UNSIGNED NULL,
    preco           DECIMAL(10,2) NOT NULL,
    estoque         INT UNSIGNED NOT NULL DEFAULT 0,
    sinopse         TEXT NULL,
    capa            VARCHAR(255) NULL,
    destaque        TINYINT(1) NOT NULL DEFAULT 0,
    ativo           TINYINT(1) NOT NULL DEFAULT 1,
    criado_em       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_livro_categoria FOREIGN KEY (categoria_id) REFERENCES categorias(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_livro_autor FOREIGN KEY (autor_id) REFERENCES autores(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT ck_livro_preco CHECK (preco > 0)
) ENGINE=InnoDB;

CREATE TABLE pedidos (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cliente_id   INT UNSIGNED NOT NULL,
    data_pedido  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status       ENUM('pendente','pago','enviado','entregue','cancelado') NOT NULL DEFAULT 'pendente',
    total        DECIMAL(10,2) NOT NULL DEFAULT 0,
    observacao   VARCHAR(255) NULL,
    CONSTRAINT fk_pedido_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE itens_pedido (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pedido_id       INT UNSIGNED NOT NULL,
    livro_id        INT UNSIGNED NOT NULL,
    quantidade      INT UNSIGNED NOT NULL,
    preco_unitario  DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_item_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_item_livro FOREIGN KEY (livro_id) REFERENCES livros(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT ck_item_qtd CHECK (quantidade > 0)
) ENGINE=InnoDB;

INSERT INTO usuarios (nome, email, senha, perfil) VALUES
('Administrador',   'admin@entrelinhas.com', '$2y$12$nIxqDfrW0zYk7qO6r54hMOLCktAfA.MmuooHQbdqG7RGD04Rku1Oi', 'admin'),
('Fernanda Lima',   'func@entrelinhas.com',  '$2y$12$5EozMtfYfE3/yiwHi2kKyubDaO26NVUw2vbHcmdRUkLGqseARF4zK', 'funcionario');

INSERT INTO clientes (nome, email, telefone, senha, endereco) VALUES
('Ana Souza',      'ana@email.com',    '(11) 98888-1111', '$2y$12$ckokhnzARnfWQsMUsBrUwutddsuG3zD9hELvk8OIgYhcNcloWvOhi', 'Rua das Flores, 120 - São Paulo/SP'),
('Bruno Martins',  'bruno@email.com',  '(21) 97777-2222', '$2y$12$ckokhnzARnfWQsMUsBrUwutddsuG3zD9hELvk8OIgYhcNcloWvOhi', 'Av. Atlântica, 500 - Rio de Janeiro/RJ'),
('Carla Mendes',   'carla@email.com',  '(31) 96666-3333', '$2y$12$ckokhnzARnfWQsMUsBrUwutddsuG3zD9hELvk8OIgYhcNcloWvOhi', 'Rua da Bahia, 45 - Belo Horizonte/MG');

INSERT INTO categorias (nome, descricao) VALUES
('Literatura Brasileira', 'Clássicos e contemporâneos da nossa literatura.'),
('Fantasia',              'Mundos imaginários, magia e grandes jornadas.'),
('Ficção Científica',     'Futuros possíveis, distopias e tecnologia.'),
('Romance',               'Histórias de amor e relações humanas.'),
('Tecnologia',            'Programação, engenharia de software e computação.'),
('Infantojuvenil',        'Leituras para crianças e jovens leitores.');

INSERT INTO autores (nome, nacionalidade, biografia) VALUES
('Machado de Assis',  'Brasileira', 'Fundador da Academia Brasileira de Letras e um dos maiores nomes da literatura em língua portuguesa.'),
('Clarice Lispector', 'Brasileira', 'Escritora e jornalista, conhecida por sua prosa introspectiva e inovadora.'),
('J. R. R. Tolkien',  'Britânica',  'Professor de Oxford e criador da Terra-média.'),
('George Orwell',     'Britânica',  'Jornalista e escritor, autor de obras marcantes sobre totalitarismo.'),
('Jane Austen',       'Britânica',  'Romancista conhecida por suas críticas sociais à aristocracia rural inglesa.'),
('Robert C. Martin',  'Americana',  'Engenheiro de software, um dos autores do Manifesto Ágil.'),
('Antoine de Saint-Exupéry', 'Francesa', 'Aviador e escritor, autor de O Pequeno Príncipe.'),
('Isaac Asimov',      'Americana',  'Bioquímico e um dos mais prolíficos autores de ficção científica.');

INSERT INTO livros (titulo, isbn, categoria_id, autor_id, editora, ano_publicacao, paginas, preco, estoque, sinopse, destaque) VALUES
('Dom Casmurro',                 '978-8535910663', 1, 1, 'Penguin-Companhia', 1899, 256, 34.90, 25, 'Bentinho relembra sua vida e o amor por Capitu, levantando a eterna dúvida: houve ou não traição?', 1),
('Memórias Póstumas de Brás Cubas','978-8535911664', 1, 1, 'Penguin-Companhia', 1881, 368, 39.90, 18, 'Um defunto autor narra sua própria vida com ironia e pessimismo, num marco do Realismo brasileiro.', 0),
('A Hora da Estrela',            '978-8532508126', 1, 2, 'Rocco',             1977,  88, 29.90, 30, 'A história de Macabéa, uma nordestina no Rio de Janeiro, narrada por Rodrigo S.M.', 1),
('O Hobbit',                     '978-8595084742', 2, 3, 'HarperCollins',     1937, 336, 54.90, 12, 'Bilbo Bolseiro é arrastado para uma aventura com treze anões e um mago em busca de um tesouro guardado por um dragão.', 1),
('O Senhor dos Anéis: A Sociedade do Anel', '978-8595084759', 2, 3, 'HarperCollins', 1954, 576, 79.90, 8, 'Frodo recebe a missão de destruir o Um Anel e parte com uma sociedade de companheiros.', 1),
('1984',                         '978-8535914849', 3, 4, 'Companhia das Letras', 1949, 416, 44.90, 20, 'Em um Estado totalitário, Winston Smith ousa pensar por conta própria sob a vigilância do Grande Irmão.', 1),
('A Revolução dos Bichos',       '978-8535909555', 3, 4, 'Companhia das Letras', 1945, 152, 27.90, 3, 'Os animais de uma granja se rebelam contra os humanos, numa fábula sobre poder e corrupção.', 0),
('Eu, Robô',                     '978-8576572008', 3, 8, 'Aleph',             1950, 320, 49.90, 15, 'Contos que apresentam as Três Leis da Robótica e os dilemas entre humanos e máquinas.', 0),
('Orgulho e Preconceito',        '978-8544001820', 4, 5, 'Martin Claret',     1813, 424, 36.90, 22, 'Elizabeth Bennet e o Sr. Darcy superam orgulho e preconceito em uma das maiores histórias de amor.', 1),
('Código Limpo',                 '978-8576082675', 5, 6, 'Alta Books',        2008, 440, 89.90, 10, 'Boas práticas para escrever código legível, fácil de manter e de evoluir.', 1),
('Arquitetura Limpa',            '978-8550804606', 5, 6, 'Alta Books',        2017, 432, 84.90, 0, 'Princípios de arquitetura de software para sistemas sustentáveis.', 0),
('O Pequeno Príncipe',           '978-8595081512', 6, 7, 'HarperCollins',     1943,  96, 24.90, 40, 'Um piloto perdido no deserto conhece um pequeno príncipe vindo de outro planeta.', 1);

INSERT INTO pedidos (cliente_id, data_pedido, status, total, observacao) VALUES
(1, '2026-10-02 14:30:00', 'entregue', 89.80, NULL),
(2, '2026-10-15 09:12:00', 'enviado', 149.70, 'Embrulhar para presente'),
(3, '2026-11-03 18:45:00', 'pago',    89.90, NULL),
(1, '2026-11-10 20:05:00', 'pendente', 54.80, NULL);

INSERT INTO itens_pedido (pedido_id, livro_id, quantidade, preco_unitario) VALUES
(1, 1, 1, 34.90),
(1, 4, 1, 54.90),
(2, 5, 1, 79.90),
(2, 6, 1, 44.90),
(2, 12, 1, 24.90),
(3, 10, 1, 89.90),
(4, 3, 1, 29.90),
(4, 12, 1, 24.90);
