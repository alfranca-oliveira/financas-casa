<?php

namespace App\Controllers;

use App\Auth;
use App\Models\Usuario;
use App\View;

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

        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $usuario = Usuario::buscarPorEmail($email);

        $senhaCorreta = $usuario !== null
            && $usuario['senha_hash'] !== null
            && password_verify($senha, $usuario['senha_hash']);

        if (!$senhaCorreta) {
            View::render('login/index', [
                'titulo' => 'Entrar',
                'erro' => 'E-mail ou senha incorretos.',
                'email' => $email,
            ], null);
            return;
        }

        Auth::entrar($usuario);
        header('Location: /');
        exit;
    }

    public function sair(): void
    {
        Auth::validarCsrf();
        Auth::sair();
        header('Location: /login');
        exit;
    }
}