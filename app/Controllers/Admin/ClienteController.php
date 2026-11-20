<?php

namespace App\Controllers\Admin;

use App\Controllers\ContaController;
use App\Core\Auth;
use App\Core\Controller;
use App\Models\Cliente;

class ClienteController extends Controller
{
    private Cliente $model;

    public function __construct()
    {
        Auth::exigirUsuario();
        $this->model = new Cliente();
    }

    public function index(): void
    {
        $busca = trim($_GET['busca'] ?? '');
        $this->renderAdmin('clientes/index', [
            'titulo' => 'Clientes',
            'clientes' => $this->model->listar($busca),
            'busca' => $busca,
        ]);
    }

    public function create(): void
    {
        $this->renderAdmin('clientes/form', ['titulo' => 'Novo cliente', 'cliente' => null]);
    }

    public function store(): void
    {
        $this->verificarCsrf();
        $dados = $this->dados();
        if ($erros = ContaController::validarCliente($dados, $this->model, null, true)) {
            $this->voltarComErros('admin/clientes/novo', $erros);
        }
        $this->model->create($dados);
        flash('sucesso', 'Cliente cadastrado.');
        redirect('admin/clientes');
    }

    public function edit(int $id): void
    {
        $cliente = $this->model->find($id) ?? $this->naoEncontrado();
        $this->renderAdmin('clientes/form', ['titulo' => 'Editar cliente', 'cliente' => $cliente]);
    }

    public function update(int $id): void
    {
        $this->verificarCsrf();
        $this->model->find($id) ?? $this->naoEncontrado();
        $dados = $this->dados();
        if ($erros = ContaController::validarCliente($dados, $this->model, $id, false)) {
            $this->voltarComErros("admin/clientes/{$id}/editar", $erros);
        }
        $this->model->update($id, $dados);
        flash('sucesso', 'Cliente atualizado.');
        redirect('admin/clientes');
    }

    public function destroy(int $id): void
    {
        $this->verificarCsrf();
        if ($this->model->temPedidos($id)) {
            flash('erro', 'Não é possível excluir: o cliente possui pedidos registrados.');
        } else {
            $this->model->delete($id);
            flash('sucesso', 'Cliente excluído.');
        }
        redirect('admin/clientes');
    }

    private function dados(): array
    {
        return [
            'nome' => $this->input('nome'),
            'email' => strtolower($this->input('email')),
            'telefone' => $this->input('telefone'),
            'endereco' => $this->input('endereco'),
            'senha' => $_POST['senha'] ?? '',
        ];
    }

    private function naoEncontrado(): never
    {
        flash('erro', 'Cliente não encontrado.');
        redirect('admin/clientes');
    }
}
