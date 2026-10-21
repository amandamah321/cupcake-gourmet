<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Cliente
{
    /** Retorna o id do cliente com esse e-mail, criando-o se não existir. */
    public static function buscarOuCriar(string $nome, string $email): int
    {
        $pdo = Database::conn();
        $st = $pdo->prepare('SELECT id_cliente FROM cliente WHERE email = ?');
        $st->execute([$email]);
        $id = $st->fetchColumn();
        if ($id !== false) {
            return (int) $id;
        }

        $st = $pdo->prepare('INSERT INTO cliente (nome, email) VALUES (?, ?)');
        $st->execute([$nome, $email]);
        return (int) $pdo->lastInsertId();
    }
}
