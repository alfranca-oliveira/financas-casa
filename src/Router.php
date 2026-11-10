<?php

namespace App;

class Router
{
    private array $rotas = [];

    public function get(string $caminho, array $acao): void { $this->rotas['GET'][$caminho] = $acao; }
    public function post(string $caminho, array $acao): void { $this->rotas['POST'][$caminho] = $acao; }
    public function put(string $caminho, array $acao): void { $this->rotas['PUT'][$caminho] = $acao; }
    public function delete(string $caminho, array $acao): void { $this->rotas['DELETE'][$caminho] = $acao; }

    public function executar(string $metodo, string $uri): void
    {
        $caminho = parse_url($uri, PHP_URL_PATH);

        foreach ($this->rotas[$metodo] ?? [] as $padrao => $acao) {
            $regex = '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $padrao) . '$#';

            if (preg_match($regex, $caminho, $partes)) {
                $parametros = array_filter($partes, 'is_string', ARRAY_FILTER_USE_KEY);
                [$classe, $metodoDaClasse] = $acao;
                (new $classe())->$metodoDaClasse(...$parametros);
                return;
            }
        }

        http_response_code(404);
        echo 'Página não encontrada';
    }
}