<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Pedido
{
    public static function criar(?int $idCliente, string $cep, ?string $comentario, float $total): int
    {
        $pdo = Database::conn();
        $st = $pdo->prepare(
            'INSERT INTO pedido (id_cliente, status, total, cep, comentario) VALUES (?, ?, ?, ?, ?)'
        );
        $st->execute([$idCliente, 'PENDENTE', $total, $cep, $comentario]);
        return (int) $pdo->lastInsertId();
    }

    /** Grava o item com o nome do produto naquele momento (snapshot). */
    public static function adicionarItem(int $idPedido, array $item): void
    {
        $st = Database::conn()->prepare(
            'INSERT INTO item_pedido (id_pedido, id_produto, nome_produto_snapshot, quantidade, preco_unitario)
             VALUES (?, ?, ?, ?, ?)'
        );
        $st->execute([
            $idPedido,
            $item['id_produto'],
            $item['nome'],
            $item['quantidade'],
            $item['preco_unitario'],
        ]);
    }

    public static function atualizarStatus(int $idPedido, string $status): void
    {
        $st = Database::conn()->prepare('UPDATE pedido SET status = ? WHERE id_pedido = ?');
        $st->execute([$status, $idPedido]);
    }

    /** Pedido com itens e pagamento, ou null. */
    public static function detalhe(int $idPedido): ?array
    {
        $pdo = Database::conn();
        $st = $pdo->prepare(
            'SELECT p.*, c.nome AS cliente_nome, c.email AS cliente_email
               FROM pedido p
               LEFT JOIN cliente c ON c.id_cliente = p.id_cliente
              WHERE p.id_pedido = ?'
        );
        $st->execute([$idPedido]);
        $pedido = $st->fetch();
        if ($pedido === false) {
            return null;
        }

        $st = $pdo->prepare(
            'SELECT nome_produto_snapshot, quantidade, preco_unitario
               FROM item_pedido WHERE id_pedido = ? ORDER BY id_item_pedido'
        );
        $st->execute([$idPedido]);
        $pedido['itens'] = $st->fetchAll();
        $pedido['pagamento'] = Pagamento::porPedido($idPedido);
        return $pedido;
    }
}
