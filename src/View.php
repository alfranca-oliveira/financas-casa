<?php

namespace App;

class View
{
    public static function render(string $view, array $dados = [], ?string $layout = 'layout'): void
    {
        extract($dados);

        ob_start();
        require __DIR__ . "/Views/{$view}.php";
        $conteudo = ob_get_clean();

        if ($layout === null) {
            echo $conteudo;
            return;
        }

        require __DIR__ . "/Views/{$layout}.php";
    }
}