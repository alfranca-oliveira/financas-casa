<?php

namespace App\Controllers\Api;

use App\Json;
use App\Models\Lancamento;
use PDOException;

class LancamentoApiController
{
    public function listar(): void
    {
        $pagina = max(1, (int) ($_GET['pagina'] ?? 1));
        $porPagina = min(100, max(1, (int) ($_GET['por_pagina'] ?? 20)));

        $filtros = [
            'inicio' => $_GET['inicio'] ?? date('Y-m-01'),
            'fim' => $_GET['fim'] ?? date('Y-m-01', strtotime('+1 month')),
            'categoria_id' => $_GET['categoria_id'] ?? null,
        ];

        if (!$this->dataValida($filtros['inicio']) || !$this->dataValida($filtros['fim'])) {
            Json::responder(['erro' => 'Use datas no formato AAAA-MM-DD.'], 422);
        }

        $lancamentos = Lancamento::listar($filtros, $porPagina, ($pagina - 1) * $porPagina);

        Json::responder([
            'dados' => array_map([$this, 'formatar'], $lancamentos),
            'pagina' => $pagina,
            'por_pagina' => $porPagina,
        ]);
    }

    public function mostrar(string $id): void
    {
        $lancamento = Lancamento::buscar((int) $id);

        if ($lancamento === null) {
            Json::responder(['erro' => 'Lançamento não encontrado.'], 404);
        }

        Json::responder(['dados' => $this->formatar($lancamento)]);
    }

    public function criar(): void
    {
        $dados = Json::corpo();
        $erros = $this->validar($dados);

        if ($erros) {
            Json::responder(['erros' => $erros], 422);
        }

        $dados['usuario_id'] = 1; // TEMPORÁRIO: na próxima parte vem do token de quem fez o pedido

        try {
            $id = Lancamento::criar($dados);
        } catch (PDOException $e) {
            if ($e->getCode() === '23503') {
                Json::responder(['erro' => 'Conta ou categoria não existe.'], 422);
            }
            throw $e;
        }

        Json::responder(['dados' => $this->formatar(Lancamento::buscar($id))], 201);
    }

    private function validar(array $dados): array
    {
        $erros = [];

        if (trim($dados['descricao'] ?? '') === '') {
            $erros['descricao'] = 'Informe a descrição.';
        }
        if (!is_numeric($dados['valor'] ?? null) || $dados['valor'] <= 0) {
            $erros['valor'] = 'O valor precisa ser um número maior que zero.';
        }
        if (!$this->dataValida($dados['data'] ?? '')) {
            $erros['data'] = 'Use uma data válida no formato AAAA-MM-DD.';
        }
        foreach (['conta_id', 'categoria_id'] as $campo) {
            if (!filter_var($dados[$campo] ?? null, FILTER_VALIDATE_INT)) {
                $erros[$campo] = 'Informe um id válido.';
            }
        }

        return $erros;
    }

    private function dataValida(string $data): bool
    {
        $partes = explode('-', $data);
        return count($partes) === 3 && checkdate((int) $partes[1], (int) $partes[2], (int) $partes[0]);
    }

    private function formatar(array $l): array
    {
        $l['id'] = (int) $l['id'];
        $l['valor'] = (float) $l['valor'];
        return $l;
    }
}