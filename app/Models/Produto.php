<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Produto
{
    public static function listar(?int $idCategoria = null): array
    {
        $sql = 'SELECT p.*, c.nome AS categoria
                  FROM produto p
                  JOIN categoria c ON c.id_categoria = p.id_categoria
                 WHERE p.ativo = TRUE';
        $params = [];
        if ($idCategoria !== null) {
            $sql .= ' AND p.id_categoria = ?';
            $params[] = $idCategoria;
        }
        $sql .= ' ORDER BY p.id_categoria, p.nome';

        $st = Database::conn()->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    }

    /** Produto ativo ou null. */
    public static function buscar(int $id): ?array
    {
        $st = Database::conn()->prepare(
            'SELECT p.*, c.nome AS categoria
               FROM produto p
               JOIN categoria c ON c.id_categoria = p.id_categoria
              WHERE p.id_produto = ? AND p.ativo = TRUE'
        );
        $st->execute([$id]);
        return $st->fetch() ?: null;
    }

    public static function destaques(int $quantidade = 3): array
    {
        $quantidade = max(1, $quantidade);
        return Database::conn()
            ->query('SELECT p.* FROM produto p WHERE p.ativo = TRUE ORDER BY p.id_produto LIMIT ' . $quantidade)
            ->fetchAll();
    }
}
