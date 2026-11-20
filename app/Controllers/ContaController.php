<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Cliente;
use App\Models\Pedido;

class ContaController extends Controller
{
    public function loginForm(): void
    {
        if (Auth::cliente()) {
            redirect('minha-conta');
        }
        $this->render('conta/login', ['titulo' => 'Entrar']);
    }

    public function login(): void
    {
        $this->verificarCsrf();
        $email = $this->input('email');
        $cliente = (new Cliente())->autenticar($email, $_POST['senha'] ?? '');

        if (!$cliente) {
            $this->voltarComErros('entrar', ['E-mail ou senha inválidos.']);
        }

        Auth::loginCliente($cliente);
        flash('sucesso', 'Bem-vindo(a), ' . explode(' ', $cliente['nome'])[0] . '!');

        $destino = $_SESSION['voltar_para'] ?? null;
        unset($_SESSION['voltar_para']);

        redirect(str_contains((string) $destino, 'carrinho') ? 'carrinho' : 'minha-conta');
    }

    public function registroForm(): void
    {
        $this->render('conta/registro', ['titulo' => 'Criar conta']);
    }

    public function registrar(): void
    {
        $this->verificarCsrf();
        $dados = [
            'nome' => $this->input('nome'),
            'email' => strtolower($this->input('email')),
            'telefone' => $this->input('telefone'),
            'endereco' => $this->input('endereco'),
            'senha' => $_POST['senha'] ?? '',
        ];
        $model = new Cliente();
        $erros = self::validarCliente($dados, $model, null, true);
        if (($dados['senha'] ?? '') !== ($_POST['senha_confirmacao'] ?? '')) {
            $erros[] = 'A confirmação de senha não confere.';
        }
        if ($erros) {
            $this->voltarComErros('cadastro', $erros);
        }

        $id = $model->create($dados);
        Auth::loginCliente($model->find($id));
        flash('sucesso', 'Cadastro realizado! Você já está conectado(a).');
        redirect(!empty($_SESSION['carrinho']) ? 'carrinho' : 'livros');
    }

    public function logout(): void
    {
        Auth::logoutCliente();
        flash('sucesso', 'Você saiu da sua conta.');
        redirect('');
    }

    public function pedidos(): void
    {
        Auth::exigirCliente();
        $this->render('conta/pedidos', [
            'titulo' => 'Minha conta',
            'pedidos' => (new Pedido())->listar(['cliente_id' => Auth::cliente()['id']]),
        ]);
    }

    public function pedido(int $id): void
    {
        Auth::exigirCliente();
        $pedido = (new Pedido())->findCompleto($id);

        if (!$pedido || (int) $pedido['cliente_id'] !== Auth::cliente()['id']) {
            http_response_code(404);
            $this->render('errors/404', ['titulo' => 'Pedido não encontrado']);
            return;
        }
        $this->render('conta/pedido', ['titulo' => 'Pedido #' . $id, 'pedido' => $pedido]);
    }

    public static function validarCliente(array $d, Cliente $model, ?int $id, bool $senhaObrigatoria): array
    {
        $erros = [];
        if (tamanho($d['nome']) < 3) {
            $erros[] = 'Informe o nome completo (mínimo 3 caracteres).';
        }
        if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL)) {
            $erros[] = 'Informe um e-mail válido.';
        } elseif ($model->exists('email', $d['email'], $id)) {
            $erros[] = 'Este e-mail já está cadastrado.';
        }
        if (strlen(preg_replace('/\D/', '', $d['telefone'])) < 10) {
            $erros[] = 'Informe um telefone com DDD.';
        }
        if (($senhaObrigatoria || $d['senha'] !== '') && strlen($d['senha']) < 6) {
            $erros[] = 'A senha deve ter pelo menos 6 caracteres.';
        }
        return $erros;
    }
}
