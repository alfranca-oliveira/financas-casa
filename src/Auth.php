<?php

namespace App;

class Auth
{
    public static function iniciarSessao(): void
    {
        session_name('financas_sessao');
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax',
            'secure' => ($_ENV['APP_ENV'] ?? 'local') === 'producao',
        ]);
        session_start();
    }

    public static function entrar(array $usuario): void
    {
        session_regenerate_id(true);
        $_SESSION['usuario'] = [
            'id' => $usuario['id'],
            'nome' => $usuario['nome'],
            'perfil' => $usuario['perfil'],
        ];
    }

    public static function sair(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    public static function usuario(): ?array
    {
        return $_SESSION['usuario'] ?? null;
    }

    public static function exigirLogin(): void
    {
        if (self::usuario() === null) {
            header('Location: /login');
            exit;
        }
    }

    public static function exigirPerfil(array $perfis): void
    {
        self::exigirLogin();

        if (!in_array(self::usuario()['perfil'], $perfis, true)) {
            http_response_code(403);
            echo 'Acesso negado';
            exit;
        }
    }

    public static function podeEditar(): bool
    {
        return in_array(self::usuario()['perfil'] ?? '', ['admin', 'membro'], true);
    }

    public static function tokenCsrf(): string
    {
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf'];
    }

    public static function validarCsrf(): void
    {
        $token = $_POST['csrf'] ?? '';

        if (!hash_equals(self::tokenCsrf(), $token)) {
            http_response_code(419);
            echo 'Sessão expirada. Volte e tente novamente.';
            exit;
        }
    }
}