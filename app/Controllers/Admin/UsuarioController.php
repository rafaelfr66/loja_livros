<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Usuario;

class UsuarioController extends Controller
{
    private Usuario $model;

    public function __construct()
    {
        Auth::exigirAdmin();
        $this->model = new Usuario();
    }

    public function index(): void
    {
        $this->renderAdmin('usuarios/index', [
            'titulo' => 'Usuários',
            'usuarios' => $this->model->all('nome'),
        ]);
    }

    public function create(): void
    {
        $this->renderAdmin('usuarios/form', ['titulo' => 'Novo usuário', 'usuario' => null]);
    }

    public function store(): void
    {
        $this->verificarCsrf();
        $dados = $this->dados();
        if ($erros = $this->validar($dados, null)) {
            $this->voltarComErros('admin/usuarios/novo', $erros);
        }
        $this->model->create($dados);
        flash('sucesso', 'Usuário cadastrado.');
        redirect('admin/usuarios');
    }

    public function edit(int $id): void
    {
        $usuario = $this->model->find($id) ?? $this->naoEncontrado();
        $this->renderAdmin('usuarios/form', ['titulo' => 'Editar usuário', 'usuario' => $usuario]);
    }

    public function update(int $id): void
    {
        $this->verificarCsrf();
        $this->model->find($id) ?? $this->naoEncontrado();
        $dados = $this->dados();
        $erros = $this->validar($dados, $id);

        if ($id === Auth::usuario()['id'] && (!$dados['ativo'] || $dados['perfil'] !== 'admin')) {
            $erros[] = 'Você não pode desativar sua própria conta nem remover seu perfil de administrador.';
        }
        if ($erros) {
            $this->voltarComErros("admin/usuarios/{$id}/editar", $erros);
        }
        $this->model->update($id, $dados);
        flash('sucesso', 'Usuário atualizado.');
        redirect('admin/usuarios');
    }

    public function destroy(int $id): void
    {
        $this->verificarCsrf();
        if ($id === Auth::usuario()['id']) {
            flash('erro', 'Você não pode excluir o próprio usuário.');
        } else {
            $this->model->delete($id);
            flash('sucesso', 'Usuário excluído.');
        }
        redirect('admin/usuarios');
    }

    private function dados(): array
    {
        $perfil = $_POST['perfil'] ?? 'funcionario';
        return [
            'nome' => $this->input('nome'),
            'email' => strtolower($this->input('email')),
            'senha' => $_POST['senha'] ?? '',
            'perfil' => in_array($perfil, ['admin', 'funcionario'], true) ? $perfil : 'funcionario',
            'ativo' => !empty($_POST['ativo']) ? 1 : 0,
        ];
    }

    private function validar(array $d, ?int $id): array
    {
        $erros = [];
        if (tamanho($d['nome']) < 3) {
            $erros[] = 'O nome deve ter pelo menos 3 caracteres.';
        }
        if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL)) {
            $erros[] = 'Informe um e-mail válido.';
        } elseif ($this->model->exists('email', $d['email'], $id)) {
            $erros[] = 'Este e-mail já está em uso.';
        }
        if (($id === null || $d['senha'] !== '') && strlen($d['senha']) < 6) {
            $erros[] = 'A senha deve ter pelo menos 6 caracteres.';
        }
        return $erros;
    }

    private function naoEncontrado(): never
    {
        flash('erro', 'Usuário não encontrado.');
        redirect('admin/usuarios');
    }
}
