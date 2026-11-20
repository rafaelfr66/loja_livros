<?php

use App\Controllers\HomeController;
use App\Controllers\LojaController;
use App\Controllers\CarrinhoController;
use App\Controllers\ContaController;
use App\Controllers\Admin\AuthController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\CategoriaController;
use App\Controllers\Admin\AutorController;
use App\Controllers\Admin\LivroController;
use App\Controllers\Admin\ClienteController;
use App\Controllers\Admin\UsuarioController;
use App\Controllers\Admin\PedidoController;
use App\Controllers\Admin\RelatorioController;

$router->get('/', [HomeController::class, 'index']);
$router->get('/livros', [LojaController::class, 'catalogo']);
$router->get('/livro/{id}', [LojaController::class, 'livro']);

$router->get('/carrinho', [CarrinhoController::class, 'index']);
$router->post('/carrinho/adicionar', [CarrinhoController::class, 'adicionar']);
$router->post('/carrinho/atualizar', [CarrinhoController::class, 'atualizar']);
$router->post('/carrinho/remover/{id}', [CarrinhoController::class, 'remover']);
$router->post('/carrinho/finalizar', [CarrinhoController::class, 'finalizar']);

$router->get('/entrar', [ContaController::class, 'loginForm']);
$router->post('/entrar', [ContaController::class, 'login']);
$router->get('/cadastro', [ContaController::class, 'registroForm']);
$router->post('/cadastro', [ContaController::class, 'registrar']);
$router->get('/sair', [ContaController::class, 'logout']);
$router->get('/minha-conta', [ContaController::class, 'pedidos']);
$router->get('/minha-conta/pedido/{id}', [ContaController::class, 'pedido']);

$router->get('/admin/login', [AuthController::class, 'loginForm']);
$router->post('/admin/login', [AuthController::class, 'login']);
$router->get('/admin/sair', [AuthController::class, 'logout']);
$router->get('/admin', [DashboardController::class, 'index']);
$router->get('/admin/relatorios/vendas', [RelatorioController::class, 'vendas']);

$crud = function (string $recurso, string $controller) use ($router): void {
    $router->get("/admin/{$recurso}", [$controller, 'index']);
    $router->get("/admin/{$recurso}/novo", [$controller, 'create']);
    $router->post("/admin/{$recurso}/salvar", [$controller, 'store']);
    $router->get("/admin/{$recurso}/{id}/editar", [$controller, 'edit']);
    $router->post("/admin/{$recurso}/{id}/atualizar", [$controller, 'update']);
    $router->post("/admin/{$recurso}/{id}/excluir", [$controller, 'destroy']);
};

$crud('categorias', CategoriaController::class);
$crud('autores', AutorController::class);
$crud('livros', LivroController::class);
$crud('clientes', ClienteController::class);
$crud('usuarios', UsuarioController::class);
$crud('pedidos', PedidoController::class);
$router->get('/admin/pedidos/{id}', [PedidoController::class, 'show']);
