<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Avaliacao
{
    public static function recentes(int $limite = 6, ?int $idProduto = null): array
    {
        $limite = max(1, $limite);
        $sql = 'SELECT nome_exibicao, comentario, estrelas, criada_em FROM avaliacao';
        $params = [];
        if ($idProduto !== null) {
            $sql .= ' WHERE id_produto = ?';
            $params[] = $idProduto;
        }
        $sql .= ' ORDER BY criada_em DESC, id_avaliacao DESC LIMIT ' . $limite;

        $st = Database::conn()->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    }

    public static function criar(string $nome, ?string $comentario, int $estrelas, ?int $idProduto): void
    {
        $st = Database::conn()->prepare(
            'INSERT INTO avaliacao (id_produto, nome_exibicao, comentario, estrelas) VALUES (?, ?, ?, ?)'
        );
        $st->execute([$idProduto, $nome, $comentario, $estrelas]);
    }
}
