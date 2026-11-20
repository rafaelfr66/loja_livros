<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Categoria;
use App\Models\Livro;

class LojaController extends Controller
{
    public function catalogo(): void
    {
        $filtros = [
            'busca' => trim($_GET['busca'] ?? ''),
            'categoria' => (int) ($_GET['categoria'] ?? 0),
            'ordem' => $_GET['ordem'] ?? 'titulo',
            'somente_ativos' => true,
        ];

        $this->render('loja/catalogo', [
            'titulo' => 'Catálogo',
            'livros' => (new Livro())->buscar($filtros),
            'categorias' => (new Categoria())->comContagem(),
            'filtros' => $filtros,
        ]);
    }

    public function livro(int $id): void
    {
        $model = new Livro();
        $livro = $model->findCompleto($id);
        if (!$livro || !$livro['ativo']) {
            http_response_code(404);
            $this->render('errors/404', ['titulo' => 'Livro não encontrado']);
            return;
        }

        $this->render('loja/livro', [
            'titulo' => $livro['titulo'],
            'livro' => $livro,
            'relacionados' => $model->relacionados((int) $livro['categoria_id'], $id),
        ]);
    }
}
