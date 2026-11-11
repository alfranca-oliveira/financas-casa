<?php

namespace App\Models;

use App\Database;
use PDO;

class TentativaLogin
{
    public const JANELA_MINUTOS = 15;
    private const LIMITE_POR_EMAIL = 5;
    private const LIMITE_POR_IP = 20;

    public static function registrar(string $email, string $ip, bool $sucesso): void
    {
        $consulta = Database::conectar()->prepare(
            'INSERT INTO tentativas_login (email, ip, sucesso) VALUES (:email, :ip, :sucesso)'
        );
        $consulta->bindValue(':email', $email);
        $consulta->bindValue(':ip', $ip);
        $consulta->bindValue(':sucesso', $sucesso, PDO::PARAM_BOOL);
        $consulta->execute();
    }

    public static function bloqueado(string $email, string $ip): bool
    {
        $consulta = Database::conectar()->prepare(
            'SELECT
                COUNT(*) FILTER (WHERE email = :email_contagem) AS por_email,
                COUNT(*) FILTER (WHERE ip = :ip_contagem) AS por_ip
             FROM tentativas_login
             WHERE sucesso = false
               AND criado_em > now() - make_interval(mins => :janela)
               AND (email = :email_filtro OR ip = :ip_filtro)'
        );
        $consulta->execute([
            'email_contagem' => $email,
            'ip_contagem' => $ip,
            'janela' => self::JANELA_MINUTOS,
            'email_filtro' => $email,
            'ip_filtro' => $ip,
        ]);
        $falhas = $consulta->fetch();

        return $falhas['por_email'] >= self::LIMITE_POR_EMAIL
            || $falhas['por_ip'] >= self::LIMITE_POR_IP;
    }
}