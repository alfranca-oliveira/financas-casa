<?php

namespace App\Controllers;

use App\Models\Lancamento;
use App\View;

class PainelController
{
    public function __construct()
    {
        \App\Auth::exigirLogin();
    }

    public function index(): void
    {
        View::render('painel/index', [
            'titulo' => 'Painel',
            'total' => Lancamento::contar(),
        ]);
    }
}