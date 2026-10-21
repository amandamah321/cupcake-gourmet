<?php
declare(strict_types=1);

namespace App\Core;

final class Config
{
    private static ?array $dados = null;

    public static function get(string $chave): mixed
    {
        if (self::$dados === null) {
            self::$dados = require dirname(__DIR__, 2) . '/config/config.php';
        }
        return self::$dados[$chave] ?? null;
    }
}
