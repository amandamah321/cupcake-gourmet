<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Categoria
{
    public static function todas(): array
    {
        return Database::conn()
            ->query('SELECT id_categoria, nome FROM categoria ORDER BY id_categoria')
            ->fetchAll();
    }
}
