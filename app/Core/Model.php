<?php

namespace App\Core;

use PDO;

abstract class Model
{
    protected PDO $db;
    protected string $tabela;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->tabela} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function all(string $ordem = 'id DESC'): array
    {
        return $this->db->query("SELECT * FROM {$this->tabela} ORDER BY {$ordem}")->fetchAll();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->tabela} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function count(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM {$this->tabela}")->fetchColumn();
    }

    public function exists(string $campo, string $valor, ?int $ignorarId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM {$this->tabela} WHERE {$campo} = :valor";
        $params = ['valor' => $valor];
        if ($ignorarId) {
            $sql .= ' AND id <> :id';
            $params['id'] = $ignorarId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }
}
