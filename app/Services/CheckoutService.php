<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Models\Carrinho;
use App\Models\Cliente;
use App\Models\Pagamento;
use App\Models\Pedido;

/** Orquestra a finalização da compra (segue o diagrama de sequência). */
final class CheckoutService
{
    /**
     * @param array<int,array> $itens   itens do carrinho (vindos do banco)
     * @param array{nome:string,email:string} $cliente
     * @return array{id_pedido:int,status:string}
     * @throws \DomainException se o carrinho estiver vazio
     */
    public function finalizar(
        array $itens,
        array $cliente,
        string $cep,
        ?string $comentario,
        string $metodo,
        array $dadosPagamento,
        ?GatewaySimulado $gateway = null
    ): array {
        if ($itens === []) {
            throw new \DomainException('Seu carrinho está vazio.');
        }

        $gateway ??= new GatewaySimulado();
        $subtotal = CarrinhoCalculator::subtotal($itens);
        $total = CarrinhoCalculator::total($subtotal, FreteCalculator::calcular($cep));

        $pdo = Database::conn();
        $pdo->beginTransaction();
        try {
            $idCliente = Cliente::buscarOuCriar($cliente['nome'], $cliente['email']);
            $idPedido = Pedido::criar($idCliente, $cep, $comentario, $total);
            foreach ($itens as $item) {
                Pedido::adicionarItem($idPedido, $item);
            }

            $resultado = $gateway->processar($metodo, $dadosPagamento);
            Pagamento::registrar($idPedido, $metodo, $resultado['status'], $resultado['referencia']);

            if ($resultado['status'] === 'APROVADO') {
                Pedido::atualizarStatus($idPedido, 'CONFIRMADO');
                Carrinho::limpar();
            }

            $pdo->commit();
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }

        return ['id_pedido' => $idPedido, 'status' => $resultado['status']];
    }
}
