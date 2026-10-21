<?php
declare(strict_types=1);

/** Escapa texto para HTML (proteção contra XSS). */
function e(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Formata valor em reais: R$ 12,00 */
function dinheiro(float|int|string $valor): string
{
    return 'R$ ' . number_format((float) $valor, 2, ',', '.');
}

/** Campo oculto com o token CSRF para formulários POST. */
function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(\App\Core\Csrf::token()) . '">';
}

/** Quantidade total de itens no carrinho (0 se houver qualquer falha). */
function cart_count(): int
{
    try {
        return \App\Models\Carrinho::quantidadeTotal();
    } catch (\Throwable) {
        return 0;
    }
}

/** Caminho público de uma imagem de produto. */
function asset_url(string $caminho): string
{
    if (preg_match('#^https?://#i', $caminho) === 1) {
        return $caminho;
    }
    return '/' . ltrim($caminho, '/');
}

/** Estrelas de avaliação: ★★★★☆ */
function estrelas(int $n): string
{
    $n = max(0, min(5, $n));
    return str_repeat('★', $n) . str_repeat('☆', 5 - $n);
}

/** Formata CEP: 01310100 -> 01310-100 */
function formata_cep(string $cep): string
{
    $d = preg_replace('/\D/', '', $cep) ?? '';
    return strlen($d) === 8 ? substr($d, 0, 5) . '-' . substr($d, 5) : $cep;
}
