<?php

namespace App\Models;

use App\Core\Model;

class Usuario extends Model
{
    protected string $tabela = 'usuarios';

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE email = :email');
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public function autenticar(string $email, string $senha): ?array
    {
        $usuario = $this->findByEmail($email);
        if ($usuario && $usuario['ativo'] && password_verify($senha, $usuario['senha'])) {
            return $usuario;
        }
        return null;
    }

    public function create(array $d): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO usuarios (nome, email, senha, perfil, ativo) VALUES (:nome, :email, :senha, :perfil, :ativo)'
        );
        $stmt->execute([
            'nome' => $d['nome'],
            'email' => $d['email'],
            'senha' => password_hash($d['senha'], PASSWORD_DEFAULT),
            'perfil' => $d['perfil'],
            'ativo' => $d['ativo'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $d): bool
    {
        $campos = 'nome = :nome, email = :email, perfil = :perfil, ativo = :ativo';
        $params = [
            'id' => $id,
            'nome' => $d['nome'],
            'email' => $d['email'],
            'perfil' => $d['perfil'],
            'ativo' => $d['ativo'],
        ];

        if (!empty($d['senha'])) {
            $campos .= ', senha = :senha';
            $params['senha'] = password_hash($d['senha'], PASSWORD_DEFAULT);
        }
        $stmt = $this->db->prepare("UPDATE usuarios SET {$campos} WHERE id = :id");
        return $stmt->execute($params);
    }
}
