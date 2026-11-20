<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Usuario;

class AuthController extends Controller
{
    public function loginForm(): void
    {
        if (Auth::usuario()) {
            redirect('admin');
        }
        $this->render('admin/login', ['titulo' => 'Acesso administrativo'], 'blank');
    }

    public function login(): void
    {
        $this->verificarCsrf();
        $usuario = (new Usuario())->autenticar($this->input('email'), $_POST['senha'] ?? '');
        if (!$usuario) {
            $this->voltarComErros('admin/login', ['E-mail ou senha inválidos, ou usuário inativo.']);
        }
        Auth::loginUsuario($usuario);
        flash('sucesso', 'Olá, ' . $usuario['nome'] . '!');
        redirect('admin');
    }

    public function logout(): void
    {
        Auth::logoutUsuario();
        flash('sucesso', 'Sessão encerrada.');
        redirect('admin/login');
    }
}
