<?php
function escapar($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function numero($valor): string
{
    return number_format((float) $valor, 0, '.', ',');
}

function importe($valor): string
{
    return number_format((float) $valor, 2, '.', ',');
}

function fechaCorta(?string $valor): string
{
    if (!$valor) return '—';
    $fecha = DateTimeImmutable::createFromFormat('!Y-m-d', substr($valor, 0, 10));
    return $fecha && $fecha->format('Y-m-d') === substr($valor, 0, 10) ? $fecha->format('d/m/Y') : '—';
}
