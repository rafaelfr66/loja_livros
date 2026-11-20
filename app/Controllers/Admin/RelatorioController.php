<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Pedido;

class RelatorioController extends Controller
{
    public function vendas(): void
    {
        Auth::exigirUsuario();

        $inicio = $_GET['inicio'] ?? date('Y-m-01', strtotime('-2 months'));
        $fim = $_GET['fim'] ?? date('Y-m-d');

        $valida = fn($d) => (bool) \DateTime::createFromFormat('Y-m-d', $d);
        if (!$valida($inicio) || !$valida($fim) || $inicio > $fim) {
            flash('erro', 'Período inválido.');
            $inicio = date('Y-m-01');
            $fim = date('Y-m-d');
        }

        $this->renderAdmin('relatorios/vendas', [
            'titulo' => 'Relatório de vendas',
            'inicio' => $inicio,
            'fim' => $fim,
        ] + (new Pedido())->relatorio($inicio, $fim));
    }
}
