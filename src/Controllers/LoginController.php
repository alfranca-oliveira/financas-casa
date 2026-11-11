<?php

namespace App\Controllers;

use App\Auth;
use App\Models\Usuario;
use App\View;
use App\Models\Historico;
use App\Models\TentativaLogin;

class LoginController
{
    public function formulario(): void
    {
        if (Auth::usuario() !== null) {
            header('Location: /');
            exit;
        }

        View::render('login/index', ['titulo' => 'Entrar', 'erro' => null, 'email' => ''], null);
    }

    public function entrar(): void
    {
        Auth::validarCsrf();

        $email = mb_strtolower(trim($_POST['email'] ?? ''));
        $senha = $_POST['senha'] ?? '';
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'desconhecido';

        if (TentativaLogin::bloqueado($email, $ip)) {
            http_response_code(429);
            $this->mostrarErro(
                'Muitas tentativas de login. Aguarde ' . TentativaLogin::JANELA_MINUTOS . ' minutos e tente novamente.',
                $email
            );
            return;
        }

        $usuario = Usuario::buscarPorEmail($email);

        $senhaCorreta = $usuario !== null
            && $usuario['senha_hash'] !== null
            && password_verify($senha, $usuario['senha_hash']);

        TentativaLogin::registrar($email, $ip, $senhaCorreta);

        if (!$senhaCorreta) {
            $this->mostrarErro('E-mail ou senha incorretos.', $email);
            return;
        }

        Auth::entrar($usuario);
        Historico::registrar($usuario['id'], 'login', $ip);

        header('Location: /');
        exit;
    }

    private function mostrarErro(string $erro, string $email): void
    {
        View::render('login/index', [
            'titulo' => 'Entrar',
            'erro' => $erro,
            'email' => $email,
        ], null);
    }

    public function sair(): void
    {
        Auth::validarCsrf();
        Auth::sair();
        header('Location: /login');
        exit;
    }
}