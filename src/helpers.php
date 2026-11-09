<?php

function e(?string $valor): string
{
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}

function dinheiro(float|string $valor): string
{
    return 'R$ ' . number_format((float) $valor, 2, ',', '.');
}

function numero(float|int|string $valor): string
{
    return number_format((float) $valor, 0, ',', '.');
}