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

    public static function listar(array $filtros, int $limite, int $deslocamento): array
    {
        $sql = 'SELECT l.id, l.descricao, l.valor, l.data,
                    l.conta_id, co.nome AS conta,
                    l.categoria_id, c.nome AS categoria, c.tipo,
                    u.nome AS pessoa
                FROM lancamentos l
                JOIN categorias c ON c.id = l.categoria_id
                JOIN contas co ON co.id = l.conta_id
                JOIN usuarios u ON u.id = l.usuario_id
                WHERE l.data >= :inicio AND l.data < :fim';

        $valores = ['inicio' => $filtros['inicio'], 'fim' => $filtros['fim']];

        if (!empty($filtros['categoria_id'])) {
            $sql .= ' AND l.categoria_id = :categoria_id';
            $valores['categoria_id'] = $filtros['categoria_id'];
        }

        $sql .= ' ORDER BY l.data DESC, l.id DESC LIMIT :limite OFFSET :deslocamento';

        $stmt = Database::conectar()->prepare($sql);
        foreach ($valores as $chave => $valor) {
            $stmt->bindValue(":$chave", $valor);
        }
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':deslocamento', $deslocamento, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function buscar(int $id): ?array
    {
        $stmt = Database::conectar()->prepare(
            'SELECT l.id, l.descricao, l.valor, l.data,
                    l.conta_id, l.categoria_id, l.usuario_id, l.status_id
            FROM lancamentos l
            WHERE l.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $lancamento = $stmt->fetch();

        return $lancamento ?: null;
    }

    public static function criar(array $dados): int
    {
        $stmt = Database::conectar()->prepare(
            "INSERT INTO lancamentos (conta_id, categoria_id, usuario_id, status_id, descricao, valor, data)
            VALUES (:conta_id, :categoria_id, :usuario_id,
                    (SELECT id FROM status WHERE nome = 'Confirmado' AND aplica_a = 'lancamento'),
                    :descricao, :valor, :data)
            RETURNING id"
        );
        $stmt->execute([
            'conta_id' => $dados['conta_id'],
            'categoria_id' => $dados['categoria_id'],
            'usuario_id' => $dados['usuario_id'],
            'descricao' => $dados['descricao'],
            'valor' => $dados['valor'],
            'data' => $dados['data'],
        ]);

        return (int) $stmt->fetchColumn();
    }
}