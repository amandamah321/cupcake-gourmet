<?php
declare(strict_types=1);

namespace App\Core;

/** Renderiza uma view dentro do layout padrão. */
final class View
{
    public static function render(string $view, array $dados = [], int $status = 200): void
    {
        http_response_code($status);
        $__base = dirname(__DIR__) . '/Views/';
        $__arquivo = $__base . $view . '.php';
        if (!is_file($__arquivo)) {
            throw new \RuntimeException('View não encontrada: ' . $view);
        }

        extract($dados, EXTR_SKIP);
        ob_start();
        require $__arquivo;
        $conteudo = ob_get_clean();
        $titulo = $titulo ?? 'CupCake Gourmet';
        require $__base . 'layout.php';
    }
}
