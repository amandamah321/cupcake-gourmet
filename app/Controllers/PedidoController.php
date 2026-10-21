<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Pedido;

final class PedidoController extends Controller
{
    public function confirmacao(string $id): void
    {
        $idPedido = (int) $id;

        // Só o visitante que acabou de comprar pode ver a confirmação.
        $pedido = (($_SESSION['ultimo_pedido'] ?? null) === $idPedido) ? Pedido::detalhe($idPedido) : null;
        if ($pedido === null) {
            $this->view('erro', [
                'titulo'   => 'Pedido não encontrado',
                'mensagem' => 'Não encontramos este pedido.',
            ], 404);
            return;
        }

        $subtotal = 0.0;
        foreach ($pedido['itens'] as $item) {
            $subtotal += (float) $item['preco_unitario'] * (int) $item['quantidade'];
        }

        $this->view('confirmacao', [
            'pedido'   => $pedido,
            'subtotal' => $subtotal,
            'frete'    => max(0.0, (float) $pedido['total'] - $subtotal),
        ]);
    }
}
