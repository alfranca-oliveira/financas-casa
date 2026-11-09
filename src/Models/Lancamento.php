<?php

namespace App\Models;

use App\Database;
use PDO;

class Lancamento
{
    public static function contar(): int
    {
        return (int) Database::conectar()
            ->query('SELECT COUNT(*) FROM lancamentos')
            ->fetchColumn();
    }

    public static function doPeriodo(string $inicio, string $fim, int $limite, int $deslocamento): array
    {
        $stmt = Database::conectar()->prepare(
            'SELECT l.id, l.descricao, l.valor, l.data,
                    c.nome AS categoria, c.tipo, co.nome AS conta, u.nome AS pessoa
             FROM lancamentos l
             JOIN categorias c ON c.id = l.categoria_id
             JOIN contas co ON co.id = l.conta_id
             JOIN usuarios u ON u.id = l.usuario_id
             WHERE l.data >= :inicio AND l.data < :fim
             ORDER BY l.data DESC, l.id DESC
             LIMIT :limite OFFSET :deslocamento'
        );
        $stmt->bindValue(':inicio', $inicio);
        $stmt->bindValue(':fim', $fim);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':deslocamento', $deslocamento, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}