<?php
declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function view(string $view, array $dados = [], int $status = 200): void
    {
        View::render($view, $dados, $status);
    }

    protected function redirect(string $caminho): never
    {
        header('Location: ' . $caminho);
        exit;
    }

    protected function flash(string $tipo, string $mensagem): void
    {
        Flash::add($tipo, $mensagem);
    }

    /** Aceita apenas caminhos internos (evita open redirect). */
    protected function destinoSeguro(string $caminho, string $padrao = '/cardapio'): string
    {
        if (str_starts_with($caminho, '/') && !str_starts_with($caminho, '//') && !str_contains($caminho, '\\')) {
            return $caminho;
        }
        return $padrao;
    }
}
