<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Livro;
use App\Models\Pedido;

class CarrinhoController extends Controller
{
    public function index(): void
    {
        $carrinho = $_SESSION['carrinho'] ?? [];
        $livros = (new Livro())->findMany(array_keys($carrinho));

        $itens = [];
        $total = 0;
        foreach ($carrinho as $id => $qtd) {
            if (!isset($livros[$id])) {
                unset($_SESSION['carrinho'][$id]);
                continue;
            }
            $subtotal = $livros[$id]['preco'] * $qtd;
            $total += $subtotal;
            $itens[] = ['livro' => $livros[$id], 'quantidade' => $qtd, 'subtotal' => $subtotal];
        }

        $this->render('carrinho/index', ['titulo' => 'Carrinho', 'itens' => $itens, 'total' => $total]);
    }

    public function adicionar(): void
    {
        $this->verificarCsrf();
        $id = (int) ($_POST['livro_id'] ?? 0);
        $qtd = max(1, (int) ($_POST['quantidade'] ?? 1));
        $livro = (new Livro())->find($id);

        if (!$livro || !$livro['ativo']) {
            flash('erro', 'Livro não encontrado.');
            redirect('livros');
        }

        $noCarrinho = $_SESSION['carrinho'][$id] ?? 0;
        if ($noCarrinho + $qtd > $livro['estoque']) {
            flash('erro', "Só temos {$livro['estoque']} unidade(s) de \"{$livro['titulo']}\" em estoque.");
            redirect('livro/' . $id);
        }

        $_SESSION['carrinho'][$id] = $noCarrinho + $qtd;
        flash('sucesso', "\"{$livro['titulo']}\" foi adicionado ao carrinho.");
        redirect('carrinho');
    }

    public function atualizar(): void
    {
        $this->verificarCsrf();
        $livros = (new Livro())->findMany(array_keys($_SESSION['carrinho'] ?? []));

        foreach ($_POST['quantidade'] ?? [] as $id => $qtd) {
            $id = (int) $id;
            $qtd = (int) $qtd;
            if (!isset($livros[$id]) || $qtd <= 0) {
                unset($_SESSION['carrinho'][$id]);
                continue;
            }
            if ($qtd > $livros[$id]['estoque']) {
                $qtd = (int) $livros[$id]['estoque'];
                flash('erro', "Quantidade de \"{$livros[$id]['titulo']}\" ajustada ao estoque disponível ({$qtd}).");
            }
            if ($qtd > 0) {
                $_SESSION['carrinho'][$id] = $qtd;
            } else {
                unset($_SESSION['carrinho'][$id]);
            }
        }
        flash('sucesso', 'Carrinho atualizado.');
        redirect('carrinho');
    }

    public function remover(int $id): void
    {
        $this->verificarCsrf();
        unset($_SESSION['carrinho'][$id]);
        flash('sucesso', 'Item removido do carrinho.');
        redirect('carrinho');
    }

    public function finalizar(): void
    {
        $this->verificarCsrf();
        Auth::exigirCliente();

        $carrinho = $_SESSION['carrinho'] ?? [];
        if (!$carrinho) {
            flash('erro', 'Seu carrinho está vazio.');
            redirect('carrinho');
        }

        try {
            $pedidoId = (new Pedido())->criar(Auth::cliente()['id'], $carrinho, $this->input('observacao'));
        } catch (\RuntimeException $e) {
            flash('erro', $e->getMessage());
            redirect('carrinho');
        }

        unset($_SESSION['carrinho']);
        flash('sucesso', "Pedido #{$pedidoId} realizado com sucesso! Obrigado pela compra.");
        redirect('minha-conta/pedido/' . $pedidoId);
    }
}
