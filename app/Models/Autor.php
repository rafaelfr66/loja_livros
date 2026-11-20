<?php

namespace App\Models;

use App\Core\Model;

class Autor extends Model
{
    protected string $tabela = 'autores';

    public function comContagem(): array
    {
        return $this->db->query(
            'SELECT a.*, COUNT(l.id) AS qtd_livros
             FROM autores a
             LEFT JOIN livros l ON l.autor_id = a.id
             GROUP BY a.id
             ORDER BY a.nome'
        )->fetchAll();
    }

    public function create(array $d): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO autores (nome, nacionalidade, biografia) VALUES (:nome, :nacionalidade, :biografia)'
        );
        $stmt->execute([
            'nome' => $d['nome'],
            'nacionalidade' => $d['nacionalidade'] ?: null,
            'biografia' => $d['biografia'] ?: null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $d): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE autores SET nome = :nome, nacionalidade = :nacionalidade, biografia = :biografia WHERE id = :id'
        );
        return $stmt->execute([
            'id' => $id,
            'nome' => $d['nome'],
            'nacionalidade' => $d['nacionalidade'] ?: null,
            'biografia' => $d['biografia'] ?: null,
        ]);
    }

    public function temLivros(int $id): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM livros WHERE autor_id = :id');
        $stmt->execute(['id' => $id]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
