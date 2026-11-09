<?php

namespace App;

class View
{
    public static function render(string $view, array $dados = []): void
    {
        extract($dados);

        ob_start();
        require __DIR__ . "/Views/{$view}.php";
        $conteudo = ob_get_clean();

        require __DIR__ . '/Views/layout.php';
    }
}