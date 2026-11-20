<?php

namespace App\Models;

use App\Core\Model;

class Livro extends Model
{
    protected string $tabela = 'livros';

    private const SELECT_COMPLETO =
        'SELECT l.*, c.nome AS categoria, a.nome AS autor
         FROM livros l
         JOIN categorias c ON c.id = l.categoria_id
         JOIN autores a ON a.id = l.autor_id';

    public function buscar(array $filtros = []): array
    {
        $where = [];
        $params = [];

        if (!empty($filtros['busca'])) {
            $where[] = '(l.titulo LIKE :b1 OR a.nome LIKE :b2 OR l.isbn LIKE :b3)';
            $termo = '%' . $filtros['busca'] . '%';
            $params += ['b1' => $termo, 'b2' => $termo, 'b3' => $termo];
        }
        if (!empty($filtros['categoria'])) {
            $where[] = 'l.categoria_id = :categoria';
            $params['categoria'] = (int) $filtros['categoria'];
        }
        if (!empty($filtros['somente_ativos'])) {
            $where[] = 'l.ativo = 1';
        }

        $ordens = [
            'recentes' => 'l.id DESC',
            'titulo' => 'l.titulo ASC',
            'menor_preco' => 'l.preco ASC',
            'maior_preco' => 'l.preco DESC',
        ];
        $ordem = $ordens[$filtros['ordem'] ?? ''] ?? 'l.titulo ASC';

        $sql = self::SELECT_COMPLETO
            . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
            . " ORDER BY {$ordem}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findCompleto(int $id): ?array
    {
        $stmt = $this->db->prepare(self::SELECT_COMPLETO . ' WHERE l.id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function destaques(int $limite = 8): array
    {
        $stmt = $this->db->prepare(
            self::SELECT_COMPLETO . ' WHERE l.ativo = 1 AND l.destaque = 1 ORDER BY l.id DESC LIMIT :limite'
        );
        $stmt->bindValue('limite', $limite, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function relacionados(int $categoriaId, int $ignorarId, int $limite = 4): array
    {
        $stmt = $this->db->prepare(
            self::SELECT_COMPLETO . ' WHERE l.ativo = 1 AND l.categoria_id = :cat AND l.id <> :id
              ORDER BY RAND() LIMIT :limite'
        );
        $stmt->bindValue('cat', $categoriaId, \PDO::PARAM_INT);
        $stmt->bindValue('id', $ignorarId, \PDO::PARAM_INT);
        $stmt->bindValue('limite', $limite, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function estoqueBaixo(int $limite = 5): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, titulo, estoque FROM livros WHERE ativo = 1 AND estoque <= :limite ORDER BY estoque, titulo'
        );
        $stmt->execute(['limite' => $limite]);
        return $stmt->fetchAll();
    }

    public function findMany(array $ids): array
    {
        if (!$ids) {
            return [];
        }
        $marcadores = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->db->prepare(self::SELECT_COMPLETO . " WHERE l.id IN ({$marcadores})");
        $stmt->execute(array_values(array_map('intval', $ids)));
        $livros = [];
        foreach ($stmt->fetchAll() as $l) {
            $livros[$l['id']] = $l;
        }
        return $livros;
    }

    public function create(array $d): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO livros (titulo, isbn, categoria_id, autor_id, editora, ano_publicacao, paginas,
                                 preco, estoque, sinopse, capa, destaque, ativo)
             VALUES (:titulo, :isbn, :categoria_id, :autor_id, :editora, :ano_publicacao, :paginas,
                     :preco, :estoque, :sinopse, :capa, :destaque, :ativo)'
        );
        $stmt->execute($this->parametros($d));
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $d): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE livros SET titulo = :titulo, isbn = :isbn, categoria_id = :categoria_id, autor_id = :autor_id,
                    editora = :editora, ano_publicacao = :ano_publicacao, paginas = :paginas, preco = :preco,
                    estoque = :estoque, sinopse = :sinopse, capa = :capa, destaque = :destaque, ativo = :ativo
             WHERE id = :id'
        );
        return $stmt->execute($this->parametros($d) + ['id' => $id]);
    }

    private function parametros(array $d): array
    {
        return [
            'titulo' => $d['titulo'],
            'isbn' => $d['isbn'],
            'categoria_id' => (int) $d['categoria_id'],
            'autor_id' => (int) $d['autor_id'],
            'editora' => $d['editora'] ?: null,
            'ano_publicacao' => $d['ano_publicacao'] !== '' ? (int) $d['ano_publicacao'] : null,
            'paginas' => $d['paginas'] !== '' ? (int) $d['paginas'] : null,
            'preco' => $d['preco'],
            'estoque' => (int) $d['estoque'],
            'sinopse' => $d['sinopse'] ?: null,
            'capa' => $d['capa'] ?? null,
            'destaque' => $d['destaque'] ? 1 : 0,
            'ativo' => $d['ativo'] ? 1 : 0,
        ];
    }

    public function temPedidos(int $id): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM itens_pedido WHERE livro_id = :id');
        $stmt->execute(['id' => $id]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
