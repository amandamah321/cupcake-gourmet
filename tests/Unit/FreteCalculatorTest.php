<?php
declare(strict_types=1);

namespace Tests\Unit;

use App\Services\FreteCalculator;
use PHPUnit\Framework\TestCase;

final class FreteCalculatorTest extends TestCase
{
    public function testFretePorFaixaDeCep(): void
    {
        $this->assertSame(8.0, FreteCalculator::calcular('01310-100'));
        $this->assertSame(12.0, FreteCalculator::calcular('13000000'));
        $this->assertSame(25.0, FreteCalculator::calcular('80000-000'));
    }

    public function testCepInvalidoLancaExcecao(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        FreteCalculator::calcular('123');
    }
}
