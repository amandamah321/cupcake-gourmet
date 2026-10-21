<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Pagamento
{
    /** Registra o resultado do pagamento. Nenhum dado de cartão é armazenado. */
    public static function registrar(int $idPedido, string $metodo, string $status, string $referencia): void
    {
        $st = Database::conn()->prepare(
            'INSERT INTO pagamento (id_pedido, metodo, status, referencia_transacao) VALUES (?, ?, ?, ?)'
        );
        $st->execute([$idPedido, $metodo, $status, $referencia]);
    }

    public static function porPedido(int $idPedido): ?array
    {
        $st = Database::conn()->prepare('SELECT * FROM pagamento WHERE id_pedido = ?');
        $st->execute([$idPedido]);
        return $st->fetch() ?: null;
    }
}
