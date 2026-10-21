<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Simula um gateway de pagamento (nenhum valor é cobrado).
 * Regras para demonstração e testes:
 *  - PIX: sempre aprovado.
 *  - Cartão: recusado se o número terminar em 0002 (ex.: 4000 0000 0000 0002); demais aprovados.
 */
final class GatewaySimulado
{
    /** @return array{status:string,referencia:string} */
    public function processar(string $metodo, array $dados = []): array
    {
        if ($metodo === 'PIX') {
            return ['status' => 'APROVADO', 'referencia' => $this->referencia('PIX')];
        }

        if ($metodo === 'CARTAO') {
            $numero = preg_replace('/\D/', '', (string) ($dados['numero_cartao'] ?? '')) ?? '';
            $status = str_ends_with($numero, '0002') ? 'RECUSADO' : 'APROVADO';
            return ['status' => $status, 'referencia' => $this->referencia('CARTAO')];
        }

        throw new \InvalidArgumentException('Método de pagamento inválido.');
    }

    private function referencia(string $prefixo): string
    {
        return 'SIM-' . $prefixo . '-' . strtoupper(bin2hex(random_bytes(4)));
    }
}
