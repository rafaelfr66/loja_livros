<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Categoria;
use App\Models\Livro;

class HomeController extends Controller
{
    public function index(): void
    {
        $livros = new Livro();
        $this->render('home/index', [
            'titulo' => 'Início',
            'destaques' => $livros->destaques(8),
            'novidades' => array_slice($livros->buscar(['somente_ativos' => true, 'ordem' => 'recentes']), 0, 4),
            'categorias' => (new Categoria())->comContagem(),
        ]);
    }
}
