<?php
declare(strict_types=1);

namespace App\Services;

/** Totais do carrinho calculados em centavos para evitar erros de ponto flutuante. */
final class CarrinhoCalculator
{
    /** @param array<int,array{quantidade:int|string,preco_unitario:float|string}> $itens */
    public static function subtotal(array $itens): float
    {
        $centavos = 0;
        foreach ($itens as $item) {
            $centavos += (int) round(((float) $item['preco_unitario']) * 100) * (int) $item['quantidade'];
        }
        return $centavos / 100;
    }

    public static function total(float $subtotal, float $frete): float
    {
        return round($subtotal + $frete, 2);
    }
}
