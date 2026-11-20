<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Categoria;

class CategoriaController extends Controller
{
    private Categoria $model;

    public function __construct()
    {
        Auth::exigirUsuario();
        $this->model = new Categoria();
    }

    public function index(): void
    {
        $this->renderAdmin('categorias/index', [
            'titulo' => 'Categorias',
            'categorias' => $this->model->comContagem(),
        ]);
    }

    public function create(): void
    {
        $this->renderAdmin('categorias/form', ['titulo' => 'Nova categoria', 'categoria' => null]);
    }

    public function store(): void
    {
        $this->verificarCsrf();
        $dados = $this->dados();
        if ($erros = $this->validar($dados)) {
            $this->voltarComErros('admin/categorias/novo', $erros);
        }
        $this->model->create($dados);
        flash('sucesso', 'Categoria cadastrada.');
        redirect('admin/categorias');
    }

    public function edit(int $id): void
    {
        $categoria = $this->model->find($id) ?? $this->naoEncontrado();
        $this->renderAdmin('categorias/form', ['titulo' => 'Editar categoria', 'categoria' => $categoria]);
    }

    public function update(int $id): void
    {
        $this->verificarCsrf();
        $this->model->find($id) ?? $this->naoEncontrado();
        $dados = $this->dados();
        if ($erros = $this->validar($dados, $id)) {
            $this->voltarComErros("admin/categorias/{$id}/editar", $erros);
        }
        $this->model->update($id, $dados);
        flash('sucesso', 'Categoria atualizada.');
        redirect('admin/categorias');
    }

    public function destroy(int $id): void
    {
        $this->verificarCsrf();

        if ($this->model->temLivros($id)) {
            flash('erro', 'Não é possível excluir: existem livros nesta categoria.');
        } else {
            $this->model->delete($id);
            flash('sucesso', 'Categoria excluída.');
        }
        redirect('admin/categorias');
    }

    private function dados(): array
    {
        return ['nome' => $this->input('nome'), 'descricao' => $this->input('descricao')];
    }

    private function validar(array $d, ?int $id = null): array
    {
        $erros = [];
        if (tamanho($d['nome']) < 3) {
            $erros[] = 'O nome deve ter pelo menos 3 caracteres.';
        } elseif ($this->model->exists('nome', $d['nome'], $id)) {
            $erros[] = 'Já existe uma categoria com esse nome.';
        }
        return $erros;
    }

    private function naoEncontrado(): never
    {
        flash('erro', 'Categoria não encontrada.');
        redirect('admin/categorias');
    }
}
