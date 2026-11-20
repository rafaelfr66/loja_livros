<?php

namespace App\Core;

class Auth
{
    public static function loginUsuario(array $usuario): void
    {
        session_regenerate_id(true);
        $_SESSION['usuario'] = [
            'id' => (int) $usuario['id'],
            'nome' => $usuario['nome'],
            'email' => $usuario['email'],
            'perfil' => $usuario['perfil'],
        ];
    }

    public static function loginCliente(array $cliente): void
    {
        session_regenerate_id(true);
        $_SESSION['cliente'] = [
            'id' => (int) $cliente['id'],
            'nome' => $cliente['nome'],
            'email' => $cliente['email'],
        ];
    }

    public static function usuario(): ?array
    {
        return $_SESSION['usuario'] ?? null;
    }

    public static function cliente(): ?array
    {
        return $_SESSION['cliente'] ?? null;
    }

    public static function isAdmin(): bool
    {
        return (self::usuario()['perfil'] ?? '') === 'admin';
    }

    public static function logoutUsuario(): void
    {
        unset($_SESSION['usuario']);
        session_regenerate_id(true);
    }

    public static function logoutCliente(): void
    {
        unset($_SESSION['cliente'], $_SESSION['carrinho']);
        session_regenerate_id(true);
    }

    public static function exigirUsuario(): void
    {
        if (!self::usuario()) {
            flash('erro', 'Faça login para acessar a área administrativa.');
            redirect('admin/login');
        }
    }

    public static function exigirAdmin(): void
    {
        self::exigirUsuario();
        if (!self::isAdmin()) {
            flash('erro', 'Apenas administradores podem acessar essa área.');
            redirect('admin');
        }
    }

    public static function exigirCliente(): void
    {
        if (!self::cliente()) {
            $_SESSION['voltar_para'] = $_SERVER['REQUEST_URI'] ?? null;
            flash('erro', 'Entre na sua conta para continuar.');
            redirect('entrar');
        }
    }
}
