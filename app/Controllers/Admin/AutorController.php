<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Autor;

class AutorController extends Controller
{
    private Autor $model;

    public function __construct()
    {
        Auth::exigirUsuario();
        $this->model = new Autor();
    }

    public function index(): void
    {
        $this->renderAdmin('autores/index', [
            'titulo' => 'Autores',
            'autores' => $this->model->comContagem(),
        ]);
    }

    public function create(): void
    {
        $this->renderAdmin('autores/form', ['titulo' => 'Novo autor', 'autor' => null]);
    }

    public function store(): void
    {
        $this->verificarCsrf();
        $dados = $this->dados();
        if ($erros = $this->validar($dados)) {
            $this->voltarComErros('admin/autores/novo', $erros);
        }
        $this->model->create($dados);
        flash('sucesso', 'Autor cadastrado.');
        redirect('admin/autores');
    }

    public function edit(int $id): void
    {
        $autor = $this->model->find($id) ?? $this->naoEncontrado();
        $this->renderAdmin('autores/form', ['titulo' => 'Editar autor', 'autor' => $autor]);
    }

    public function update(int $id): void
    {
        $this->verificarCsrf();
        $this->model->find($id) ?? $this->naoEncontrado();
        $dados = $this->dados();
        if ($erros = $this->validar($dados)) {
            $this->voltarComErros("admin/autores/{$id}/editar", $erros);
        }
        $this->model->update($id, $dados);
        flash('sucesso', 'Autor atualizado.');
        redirect('admin/autores');
    }

    public function destroy(int $id): void
    {
        $this->verificarCsrf();
        if ($this->model->temLivros($id)) {
            flash('erro', 'Não é possível excluir: este autor possui livros cadastrados.');
        } else {
            $this->model->delete($id);
            flash('sucesso', 'Autor excluído.');
        }
        redirect('admin/autores');
    }

    private function dados(): array
    {
        return [
            'nome' => $this->input('nome'),
            'nacionalidade' => $this->input('nacionalidade'),
            'biografia' => $this->input('biografia'),
        ];
    }

    private function validar(array $d): array
    {
        return tamanho($d['nome']) < 3 ? ['O nome deve ter pelo menos 3 caracteres.'] : [];
    }

    private function naoEncontrado(): never
    {
        flash('erro', 'Autor não encontrado.');
        redirect('admin/autores');
    }
}
