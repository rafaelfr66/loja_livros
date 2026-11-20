# Etapa 1 – Planejamento, modelagem e banco de dados

## Tema da loja

**Livraria Entrelinhas** — loja virtual de livros (literatura brasileira, fantasia, ficção científica, romance, tecnologia e infantojuvenil).

## Descrição do sistema

Sistema web em PHP 8 (MVC + PDO + MySQL) para uma livraria. Ele tem duas áreas:

- **Área pública (clientes):** página inicial com destaques e gêneros, catálogo com busca e filtro por categoria, página de detalhes do livro, carrinho de compras, cadastro e login de clientes, finalização do pedido e histórico de pedidos ("Minha conta").
- **Área administrativa (funcionários):** login restrito, dashboard com indicadores, CRUD de livros (com upload de capa), categorias, autores, clientes, pedidos e usuários, e relatório de vendas por período.

## Regras de negócio

| # | Regra |
|---|-------|
| RN01 | Apenas usuários (funcionários) autenticados acessam a área administrativa. |
| RN02 | Somente usuários com perfil **admin** podem gerenciar outros usuários. |
| RN03 | Um usuário não pode excluir, desativar ou rebaixar a própria conta. |
| RN04 | Usuários inativos não conseguem fazer login. |
| RN05 | Senhas de usuários e clientes são armazenadas com `password_hash()` e conferidas com `password_verify()`. |
| RN06 | O e-mail é único para clientes e para usuários. O ISBN do livro e o nome da categoria também são únicos. |
| RN07 | O cliente pode montar o carrinho sem login, mas precisa estar logado para finalizar o pedido. |
| RN08 | O cliente só pode ver os próprios pedidos. |
| RN09 | O preço do livro deve ser maior que zero e o estoque não pode ser negativo. |
| RN10 | Não é possível comprar uma quantidade maior que o estoque disponível. Livros esgotados aparecem como "Esgotado" e não podem ser adicionados ao carrinho. |
| RN11 | Ao criar um pedido, o estoque de cada livro é baixado. O preço unitário é gravado no item (o histórico não muda se o preço do livro mudar depois). |
| RN12 | Ao **cancelar** um pedido, os livros voltam ao estoque. Ao reativar um pedido cancelado, o estoque é baixado de novo (se houver saldo). |
| RN13 | Ao excluir um pedido que não estava cancelado, os livros voltam ao estoque. |
| RN14 | Não é possível excluir categoria ou autor que tenha livros vinculados. |
| RN15 | Não é possível excluir livro que já aparece em pedidos (para manter o histórico). Nesse caso o livro deve ser **desativado**. |
| RN16 | Não é possível excluir cliente que possui pedidos. |
| RN17 | Livros inativos não aparecem na loja. |
| RN18 | Status possíveis do pedido: pendente → pago → enviado → entregue, ou cancelado. |
| RN19 | O faturamento e os relatórios desconsideram pedidos cancelados. |

## Diagrama de classes (UML)

```mermaid
classDiagram
    direction LR

    class Model {
        <<abstract>>
        #PDO db
        #string tabela
        +find(id) array
        +all(ordem) array
        +delete(id) bool
        +count() int
        +exists(campo, valor, ignorarId) bool
    }

    class Usuario {
        -int id
        -string nome
        -string email
        -string senha
        -string perfil
        -bool ativo
        +autenticar(email, senha) array
        +findByEmail(email) array
        +create(dados) int
        +update(id, dados) bool
    }

    class Cliente {
        -int id
        -string nome
        -string email
        -string telefone
        -string senha
        -string endereco
        +autenticar(email, senha) array
        +listar(busca) array
        +create(dados) int
        +update(id, dados) bool
        +temPedidos(id) bool
    }

    class Categoria {
        -int id
        -string nome
        -string descricao
        +comContagem() array
        +create(dados) int
        +update(id, dados) bool
        +temLivros(id) bool
    }

    class Autor {
        -int id
        -string nome
        -string nacionalidade
        -string biografia
        +comContagem() array
        +create(dados) int
        +update(id, dados) bool
        +temLivros(id) bool
    }

    class Livro {
        -int id
        -string titulo
        -string isbn
        -string editora
        -int anoPublicacao
        -int paginas
        -float preco
        -int estoque
        -string sinopse
        -string capa
        -bool destaque
        -bool ativo
        +buscar(filtros) array
        +findCompleto(id) array
        +destaques(limite) array
        +relacionados(categoriaId, id) array
        +estoqueBaixo(limite) array
        +create(dados) int
        +update(id, dados) bool
    }

    class Pedido {
        -int id
        -datetime dataPedido
        -string status
        -float total
        -string observacao
        +listar(filtros) array
        +findCompleto(id) array
        +criar(clienteId, itens, obs, status) int
        +atualizar(id, status, obs) void
        +excluir(id) void
        +resumo() array
        +vendasPorMes(meses) array
        +relatorio(inicio, fim) array
    }

    class ItemPedido {
        -int id
        -int quantidade
        -float precoUnitario
    }

    Model <|-- Usuario
    Model <|-- Cliente
    Model <|-- Categoria
    Model <|-- Autor
    Model <|-- Livro
    Model <|-- Pedido

    Categoria "1" --> "0..*" Livro : classifica
    Autor "1" --> "0..*" Livro : escreve
    Cliente "1" --> "0..*" Pedido : faz
    Pedido "1" *-- "1..*" ItemPedido : contém
    Livro "1" --> "0..*" ItemPedido : vendido em
```

## Modelo do banco de dados (DER)

```mermaid
erDiagram
    USUARIOS {
        int id PK
        varchar nome
        varchar email UK
        varchar senha
        enum perfil "admin | funcionario"
        tinyint ativo
        datetime criado_em
    }
    CLIENTES {
        int id PK
        varchar nome
        varchar email UK
        varchar telefone
        varchar senha
        varchar endereco
        datetime criado_em
    }
    CATEGORIAS {
        int id PK
        varchar nome UK
        varchar descricao
    }
    AUTORES {
        int id PK
        varchar nome
        varchar nacionalidade
        text biografia
    }
    LIVROS {
        int id PK
        varchar titulo
        varchar isbn UK
        int categoria_id FK
        int autor_id FK
        varchar editora
        smallint ano_publicacao
        smallint paginas
        decimal preco
        int estoque
        text sinopse
        varchar capa
        tinyint destaque
        tinyint ativo
        datetime criado_em
    }
    PEDIDOS {
        int id PK
        int cliente_id FK
        datetime data_pedido
        enum status
        decimal total
        varchar observacao
    }
    ITENS_PEDIDO {
        int id PK
        int pedido_id FK
        int livro_id FK
        int quantidade
        decimal preco_unitario
    }

    CATEGORIAS ||--o{ LIVROS : "possui"
    AUTORES ||--o{ LIVROS : "escreve"
    CLIENTES ||--o{ PEDIDOS : "realiza"
    PEDIDOS ||--|{ ITENS_PEDIDO : "contém"
    LIVROS ||--o{ ITENS_PEDIDO : "aparece em"
```

`usuarios` não tem relação com as demais tabelas: ela guarda apenas quem acessa o painel.

## Script SQL

O script completo está em [`database/livraria.sql`](../database/livraria.sql). Ele faz:

- criação do banco `livraria` (utf8mb4);
- criação das 7 tabelas com chaves primárias (`AUTO_INCREMENT`);
- chaves estrangeiras com `ON DELETE RESTRICT` (categoria, autor, cliente, livro) e `ON DELETE CASCADE` (itens do pedido);
- restrições `UNIQUE` (e-mails, ISBN, nome da categoria) e `CHECK` (preço > 0, quantidade > 0);
- dados iniciais: 2 usuários, 3 clientes, 6 categorias, 8 autores, 12 livros e 4 pedidos com itens.
