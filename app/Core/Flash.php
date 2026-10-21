<?php
declare(strict_types=1);

namespace App\Core;

/** Mensagens de feedback (sucesso/erro) exibidas uma única vez. */
final class Flash
{
    public static function add(string $tipo, string $mensagem): void
    {
        $_SESSION['_flash'][] = ['tipo' => $tipo, 'msg' => $mensagem];
    }

    public static function todas(): array
    {
        $mensagens = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return $mensagens;
    }
}
