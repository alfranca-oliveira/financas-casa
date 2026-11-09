<?php

namespace App\Controllers;

use App\Models\Lancamento;
use App\View;

class LancamentoController
{
    public function __construct()
    {
        \App\Auth::exigirLogin();
    }
    public function index(): void
    {
        $pagina = max(1, (int) ($_GET['pagina'] ?? 1));
        $porPagina = 20;

        $lancamentos = Lancamento::doPeriodo(
            '2026-10-01',
            '2026-11-01',
            $porPagina,
            ($pagina - 1) * $porPagina
        );

        View::render('lancamentos/index', [
            'titulo' => 'Lançamentos',
            'lancamentos' => $lancamentos,
            'pagina' => $pagina,
        ]);
    }
}