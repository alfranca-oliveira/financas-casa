<?php

namespace App;

class Router
{
    private array $rotas = [];

    public function get(string $caminho, array $acao): void
    {
        $this->rotas['GET'][$caminho] = $acao;
    }

    public function post(string $caminho, array $acao): void
    {
        $this->rotas['POST'][$caminho] = $acao;
    }

    public function executar(string $metodo, string $uri): void
    {
        $caminho = parse_url($uri, PHP_URL_PATH);
        $acao = $this->rotas[$metodo][$caminho] ?? null;

        if ($acao === null) {
            http_response_code(404);
            echo 'Página não encontrada';
            return;
        }

        [$classe, $metodoDaClasse] = $acao;
        (new $classe())->$metodoDaClasse();
    }
}