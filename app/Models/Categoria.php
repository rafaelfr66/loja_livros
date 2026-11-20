<?php

namespace App\Models;

use App\Core\Model;

class Categoria extends Model
{
    protected string $tabela = 'categorias';

    public function comContagem(): array
    {
        return $this->db->query(
            'SELECT c.*, COUNT(l.id) AS qtd_livros
             FROM categorias c
             LEFT JOIN livros l ON l.categoria_id = c.id AND l.ativo = 1
             GROUP BY c.id
             ORDER BY c.nome'
        )->fetchAll();
    }

    public function create(array $d): int
    {
        $stmt = $this->db->prepare('INSERT INTO categorias (nome, descricao) VALUES (:nome, :descricao)');
        $stmt->execute(['nome' => $d['nome'], 'descricao' => $d['descricao'] ?: null]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $d): bool
    {
        $stmt = $this->db->prepare('UPDATE categorias SET nome = :nome, descricao = :descricao WHERE id = :id');
        return $stmt->execute(['id' => $id, 'nome' => $d['nome'], 'descricao' => $d['descricao'] ?: null]);
    }

    public function temLivros(int $id): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM livros WHERE categoria_id = :id');
        $stmt->execute(['id' => $id]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
