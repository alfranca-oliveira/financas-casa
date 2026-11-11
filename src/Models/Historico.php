<?php

namespace App\Models;

use App\Database;

class Historico
{
    public static function registrar(
        ?int $usuarioId,
        string $acao,
        ?string $ip,
        ?string $tabela = null,
        ?int $registroId = null,
        ?array $antes = null,
        ?array $depois = null
    ): void {
        $consulta = Database::conectar()->prepare(
            'INSERT INTO historico (usuario_id, acao, tabela, registro_id, dados_antes, dados_depois, ip)
             VALUES (:usuario_id, :acao, :tabela, :registro_id, :antes, :depois, :ip)'
        );
        $consulta->execute([
            'usuario_id' => $usuarioId,
            'acao' => $acao,
            'tabela' => $tabela,
            'registro_id' => $registroId,
            'antes' => $antes === null ? null : json_encode($antes, JSON_UNESCAPED_UNICODE),
            'depois' => $depois === null ? null : json_encode($depois, JSON_UNESCAPED_UNICODE),
            'ip' => $ip,
        ]);
    }
}