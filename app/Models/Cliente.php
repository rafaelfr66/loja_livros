<?php

namespace App\Models;

use App\Core\Model;

class Cliente extends Model
{
    protected string $tabela = 'clientes';

    public function listar(string $busca = ''): array
    {
        $sql = 'SELECT c.*, COUNT(p.id) AS qtd_pedidos,
                       COALESCE(SUM(CASE WHEN p.status <> "cancelado" THEN p.total END), 0) AS total_gasto
                FROM clientes c
                LEFT JOIN pedidos p ON p.cliente_id = c.id';
        $params = [];
        if ($busca !== '') {
            $sql .= ' WHERE c.nome LIKE :b1 OR c.email LIKE :b2';
            $params = ['b1' => "%{$busca}%", 'b2' => "%{$busca}%"];
        }
        $sql .= ' GROUP BY c.id ORDER BY c.nome';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function autenticar(string $email, string $senha): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM clientes WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $cliente = $stmt->fetch();
        return ($cliente && password_verify($senha, $cliente['senha'])) ? $cliente : null;
    }

    public function create(array $d): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO clientes (nome, email, telefone, senha, endereco)
             VALUES (:nome, :email, :telefone, :senha, :endereco)'
        );
        $stmt->execute([
            'nome' => $d['nome'],
            'email' => $d['email'],
            'telefone' => $d['telefone'],
            'senha' => password_hash($d['senha'], PASSWORD_DEFAULT),
            'endereco' => $d['endereco'] ?: null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $d): bool
    {
        $campos = 'nome = :nome, email = :email, telefone = :telefone, endereco = :endereco';
        $params = [
            'id' => $id,
            'nome' => $d['nome'],
            'email' => $d['email'],
            'telefone' => $d['telefone'],
            'endereco' => $d['endereco'] ?: null,
        ];
        if (!empty($d['senha'])) {
            $campos .= ', senha = :senha';
            $params['senha'] = password_hash($d['senha'], PASSWORD_DEFAULT);
        }
        $stmt = $this->db->prepare("UPDATE clientes SET {$campos} WHERE id = :id");
        return $stmt->execute($params);
    }

    public function temPedidos(int $id): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM pedidos WHERE cliente_id = :id');
        $stmt->execute(['id' => $id]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
