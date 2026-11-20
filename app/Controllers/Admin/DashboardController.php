<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Cliente;
use App\Models\Livro;
use App\Models\Pedido;

class DashboardController extends Controller
{
    public function index(): void
    {
        Auth::exigirUsuario();
        $pedidos = new Pedido();

        $this->renderAdmin('dashboard', [
            'titulo' => 'Dashboard',
            'resumo' => $pedidos->resumo(),
            'totalLivros' => (new Livro())->count(),
            'totalClientes' => (new Cliente())->count(),
            'vendasMes' => $pedidos->vendasPorMes(6),
            'ultimosPedidos' => array_slice($pedidos->listar(), 0, 5),
            'estoqueBaixo' => (new Livro())->estoqueBaixo(5),
        ]);
    }
}
