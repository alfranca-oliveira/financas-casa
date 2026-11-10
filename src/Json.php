<?php

namespace App;

class Json
{
    public static function responder(mixed $dados, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');

        if ($status !== 204) {
            echo json_encode($dados, JSON_UNESCAPED_UNICODE);
        }

        exit;
    }

    public static function corpo(): array
    {
        $dados = json_decode(file_get_contents('php://input'), true);

        if (!is_array($dados)) {
            self::responder(['erro' => 'O corpo da requisição precisa ser um JSON válido.'], 400);
        }

        return $dados;
    }
}