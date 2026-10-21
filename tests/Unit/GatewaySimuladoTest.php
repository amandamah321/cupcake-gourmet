<?php
declare(strict_types=1);

namespace Tests\Unit;

use App\Services\GatewaySimulado;
use PHPUnit\Framework\TestCase;

final class GatewaySimuladoTest extends TestCase
{
    public function testPixEhSempreAprovado(): void
    {
        $r = (new GatewaySimulado())->processar('PIX');

        $this->assertSame('APROVADO', $r['status']);
        $this->assertStringStartsWith('SIM-PIX-', $r['referencia']);
    }

    public function testCartaoComumEhAprovado(): void
    {
        $r = (new GatewaySimulado())->processar('CARTAO', ['numero_cartao' => '4111 1111 1111 1111']);

        $this->assertSame('APROVADO', $r['status']);
        $this->assertStringStartsWith('SIM-CARTAO-', $r['referencia']);
    }

    public function testCartaoDeTesteTerminadoEm0002EhRecusado(): void
    {
        $r = (new GatewaySimulado())->processar('CARTAO', ['numero_cartao' => '4000 0000 0000 0002']);

        $this->assertSame('RECUSADO', $r['status']);
    }

    public function testMetodoInvalidoLancaExcecao(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new GatewaySimulado())->processar('BOLETO');
    }
}
