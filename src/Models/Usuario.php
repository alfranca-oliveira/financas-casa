<?php

namespace App\Models;

use App\Database;

class Usuario
{
    public static function buscarPorEmail(string $email): ?array
    {
        $stmt = Database::conectar()->prepare(
            'SELECT id, nome, email, senha_hash, perfil FROM usuarios WHERE email = :email'
        );
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch();

        return $usuario ?: null;
    }
}