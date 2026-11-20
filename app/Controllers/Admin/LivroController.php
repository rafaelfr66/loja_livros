<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Upload;
use App\Models\Autor;
use App\Models\Categoria;
use App\Models\Livro;

class LivroController extends Controller
{
    private Livro $model;

    public function __construct()
    {
        Auth::exigirUsuario();
        $this->model = new Livro();
    }

    public function index(): void
    {
        $filtros = [
            'busca' => trim($_GET['busca'] ?? ''),
            'categoria' => (int) ($_GET['categoria'] ?? 0),
        ];
        $this->renderAdmin('livros/index', [
            'titulo' => 'Livros',
            'livros' => $this->model->buscar($filtros),
            'categorias' => (new Categoria())->all('nome'),
            'filtros' => $filtros,
        ]);
    }

    public function create(): void
    {
        $this->renderAdmin('livros/form', $this->dadosFormulario('Novo livro', null));
    }

    public function store(): void
    {
        $this->verificarCsrf();
        $dados = $this->dados();
        if ($erros = $this->validar($dados)) {
            $this->voltarComErros('admin/livros/novo', $erros);
        }
        try {
            $dados['capa'] = Upload::imagem('capa');
        } catch (\RuntimeException $e) {
            $this->voltarComErros('admin/livros/novo', [$e->getMessage()]);
        }
        $this->model->create($dados);
        flash('sucesso', 'Livro cadastrado.');
        redirect('admin/livros');
    }

    public function edit(int $id): void
    {
        $livro = $this->model->find($id) ?? $this->naoEncontrado();
        $this->renderAdmin('livros/form', $this->dadosFormulario('Editar livro', $livro));
    }

    public function update(int $id): void
    {
        $this->verificarCsrf();
        $livro = $this->model->find($id) ?? $this->naoEncontrado();
        $dados = $this->dados();
        if ($erros = $this->validar($dados, $id)) {
            $this->voltarComErros("admin/livros/{$id}/editar", $erros);
        }

        $dados['capa'] = $livro['capa'];
        try {
            $novaCapa = Upload::imagem('capa');
        } catch (\RuntimeException $e) {
            $this->voltarComErros("admin/livros/{$id}/editar", [$e->getMessage()]);
        }
        if ($novaCapa || !empty($_POST['remover_capa'])) {
            Upload::remover($livro['capa']);
            $dados['capa'] = $novaCapa;
        }

        $this->model->update($id, $dados);
        flash('sucesso', 'Livro atualizado.');
        redirect('admin/livros');
    }

    public function destroy(int $id): void
    {
        $this->verificarCsrf();
        $livro = $this->model->find($id) ?? $this->naoEncontrado();

        if ($this->model->temPedidos($id)) {
            flash('erro', 'Este livro aparece em pedidos e não pode ser excluído. Desative-o para tirá-lo da loja.');
        } else {
            $this->model->delete($id);
            Upload::remover($livro['capa']);
            flash('sucesso', 'Livro excluído.');
        }
        redirect('admin/livros');
    }

    private function dadosFormulario(string $titulo, ?array $livro): array
    {
        return [
            'titulo' => $titulo,
            'livro' => $livro,
            'categorias' => (new Categoria())->all('nome'),
            'autores' => (new Autor())->all('nome'),
        ];
    }

    private function dados(): array
    {
        return [
            'titulo' => $this->input('titulo'),
            'isbn' => $this->input('isbn'),
            'categoria_id' => (int) ($_POST['categoria_id'] ?? 0),
            'autor_id' => (int) ($_POST['autor_id'] ?? 0),
            'editora' => $this->input('editora'),
            'ano_publicacao' => $this->input('ano_publicacao'),
            'paginas' => $this->input('paginas'),
            'preco' => str_replace(',', '.', $this->input('preco')),
            'estoque' => $this->input('estoque', '0'),
            'sinopse' => $this->input('sinopse'),
            'destaque' => !empty($_POST['destaque']),
            'ativo' => !empty($_POST['ativo']),
        ];
    }

    private function validar(array $d, ?int $id = null): array
    {
        $erros = [];
        if (tamanho($d['titulo']) < 2) {
            $erros[] = 'Informe o título do livro.';
        }
        if (!preg_match('/^[0-9Xx-]{10,17}$/', $d['isbn'])) {
            $erros[] = 'ISBN inválido (use apenas números e hífens, 10 a 13 dígitos).';
        } elseif ($this->model->exists('isbn', $d['isbn'], $id)) {
            $erros[] = 'Já existe um livro com esse ISBN.';
        }
        if (!(new Categoria())->find($d['categoria_id'])) {
            $erros[] = 'Selecione uma categoria.';
        }
        if (!(new Autor())->find($d['autor_id'])) {
            $erros[] = 'Selecione um autor.';
        }
        if (!is_numeric($d['preco']) || (float) $d['preco'] <= 0) {
            $erros[] = 'O preço deve ser maior que zero.';
        }
        if (!ctype_digit((string) $d['estoque'])) {
            $erros[] = 'O estoque deve ser um número inteiro maior ou igual a zero.';
        }
        if ($d['ano_publicacao'] !== '' && (!ctype_digit($d['ano_publicacao']) || (int) $d['ano_publicacao'] > (int) date('Y'))) {
            $erros[] = 'Ano de publicação inválido.';
        }
        if ($d['paginas'] !== '' && (!ctype_digit($d['paginas']) || (int) $d['paginas'] < 1)) {
            $erros[] = 'Número de páginas inválido.';
        }
        return $erros;
    }

    private function naoEncontrado(): never
    {
        flash('erro', 'Livro não encontrado.');
        redirect('admin/livros');
    }
}
