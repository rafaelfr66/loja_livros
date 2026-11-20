<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Cliente;
use App\Models\Livro;
use App\Models\Pedido;

class PedidoController extends Controller
{
    private Pedido $model;

    public function __construct()
    {
        Auth::exigirUsuario();
        $this->model = new Pedido();
    }

    public function index(): void
    {
        $status = in_array($_GET['status'] ?? '', Pedido::STATUS, true) ? $_GET['status'] : '';
        $this->renderAdmin('pedidos/index', [
            'titulo' => 'Pedidos',
            'pedidos' => $this->model->listar(['status' => $status]),
            'status' => $status,
        ]);
    }

    public function show(int $id): void
    {
        $pedido = $this->model->findCompleto($id) ?? $this->naoEncontrado();
        $this->renderAdmin('pedidos/show', ['titulo' => "Pedido #{$id}", 'pedido' => $pedido]);
    }

    public function create(): void
    {
        $this->renderAdmin('pedidos/form', [
            'titulo' => 'Novo pedido',
            'clientes' => (new Cliente())->all('nome'),
            'livros' => (new Livro())->buscar(['somente_ativos' => true]),
        ]);
    }

    public function store(): void
    {
        $this->verificarCsrf();
        $clienteId = (int) ($_POST['cliente_id'] ?? 0);
        $status = in_array($_POST['status'] ?? '', ['pendente', 'pago'], true) ? $_POST['status'] : 'pendente';

        $itens = [];
        foreach ($_POST['livro_id'] ?? [] as $i => $livroId) {
            $livroId = (int) $livroId;
            $qtd = (int) ($_POST['quantidade'][$i] ?? 0);
            if ($livroId > 0 && $qtd > 0) {
                $itens[$livroId] = ($itens[$livroId] ?? 0) + $qtd;
            }
        }

        $erros = [];
        if (!(new Cliente())->find($clienteId)) {
            $erros[] = 'Selecione o cliente.';
        }
        if (!$itens) {
            $erros[] = 'Adicione pelo menos um livro com quantidade válida.';
        }
        if ($erros) {
            $this->voltarComErros('admin/pedidos/novo', $erros);
        }

        try {
            $id = $this->model->criar($clienteId, $itens, $this->input('observacao'), $status);
        } catch (\RuntimeException $e) {
            $this->voltarComErros('admin/pedidos/novo', [$e->getMessage()]);
        }
        flash('sucesso', "Pedido #{$id} criado.");
        redirect("admin/pedidos/{$id}");
    }

    public function edit(int $id): void
    {
        $pedido = $this->model->findCompleto($id) ?? $this->naoEncontrado();
        $this->renderAdmin('pedidos/edit', ['titulo' => "Editar pedido #{$id}", 'pedido' => $pedido]);
    }

    public function update(int $id): void
    {
        $this->verificarCsrf();
        $status = $_POST['status'] ?? '';
        if (!in_array($status, Pedido::STATUS, true)) {
            $this->voltarComErros("admin/pedidos/{$id}/editar", ['Status inválido.']);
        }
        try {
            $this->model->atualizar($id, $status, $this->input('observacao'));
        } catch (\RuntimeException $e) {
            $this->voltarComErros("admin/pedidos/{$id}/editar", [$e->getMessage()]);
        }
        flash('sucesso', 'Pedido atualizado.');
        redirect("admin/pedidos/{$id}");
    }

    public function destroy(int $id): void
    {
        $this->verificarCsrf();
        $this->model->excluir($id);
        flash('sucesso', "Pedido #{$id} excluído e estoque ajustado.");
        redirect('admin/pedidos');
    }

    private function naoEncontrado(): never
    {
        flash('erro', 'Pedido não encontrado.');
        redirect('admin/pedidos');
    }
}
