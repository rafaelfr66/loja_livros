<?php

namespace App\Core;

class Controller
{
    public function render(string $view, array $dados = [], string $layout = 'public'): void
    {
        extract($dados);
        ob_start();
        require APP_PATH . "/Views/{$view}.php";
        $conteudo = ob_get_clean();
        require APP_PATH . "/Views/layouts/{$layout}.php";
        unset($_SESSION['old']);
    }

    protected function renderAdmin(string $view, array $dados = []): void
    {
        $this->render('admin/' . $view, $dados, 'admin');
    }

    protected function verificarCsrf(): void
    {
        $token = $_POST['_csrf'] ?? '';
        if (!hash_equals($_SESSION['csrf'] ?? '', $token)) {
            http_response_code(419);
            exit('Sessão expirada. Volte e tente novamente.');
        }
    }

    protected function guardarOld(array $dados): void
    {
        unset($dados['senha'], $dados['senha_confirmacao'], $dados['_csrf']);
        $_SESSION['old'] = $dados;
    }

    protected function input(string $campo, string $padrao = ''): string
    {
        $valor = trim((string) ($_POST[$campo] ?? $padrao));

        if (!preg_match('//u', $valor)) {
            $valor = function_exists('iconv') ? (string) iconv('ISO-8859-1', 'UTF-8', $valor) : '';
        }
        return $valor;
    }

    protected function voltarComErros(string $caminho, array $erros): never
    {
        foreach ($erros as $erro) {
            flash('erro', $erro);
        }
        $this->guardarOld($_POST);
        redirect($caminho);
    }
}
