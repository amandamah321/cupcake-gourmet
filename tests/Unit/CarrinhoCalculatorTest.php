<?php
declare(strict_types=1);

namespace Tests\Unit;

use App\Services\CarrinhoCalculator;
use PHPUnit\Framework\TestCase;

final class CarrinhoCalculatorTest extends TestCase
{
    public function testSubtotalSomaQuantidadeVezesPreco(): void
    {
        $itens = [
            ['quantidade' => 2, 'preco_unitario' => '12.00'],
            ['quantidade' => 1, 'preco_unitario' => '13.50'],
        ];
        $this->assertEqualsWithDelta(37.5, CarrinhoCalculator::subtotal($itens), 0.001);
    }

    public function testCarrinhoVazioTemSubtotalZero(): void
    {
        $this->assertEqualsWithDelta(0.0, CarrinhoCalculator::subtotal([]), 0.001);
    }

    public function testNaoHaErroDeArredondamento(): void
    {
        $itens = [['quantidade' => 3, 'preco_unitario' => 0.10]];
        $this->assertEqualsWithDelta(0.30, CarrinhoCalculator::subtotal($itens), 0.0001);
    }

    public function testTotalSomaFrete(): void
    {
        $this->assertEqualsWithDelta(45.5, CarrinhoCalculator::total(37.5, 8.0), 0.001);
    }
}
