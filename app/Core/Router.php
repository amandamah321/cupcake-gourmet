<?php
declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $rotas = [];

    public function get(string $padrao, array $acao): void
    {
        $this->adicionar('GET', $padrao, $acao);
    }

    public function post(string $padrao, array $acao): void
    {
        $this->adicionar('POST', $padrao, $acao);
    }

    private function adicionar(string $metodo, string $padrao, array $acao): void
    {
        $regex = '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $padrao) . '$#';
        $this->rotas[] = [$metodo, $regex, $acao];
    }

    public function dispatch(string $metodo, string $caminho): void
    {
        $caminho = rtrim($caminho, '/');
        if ($caminho === '') {
            $caminho = '/';
        }

        foreach ($this->rotas as [$m, $regex, $acao]) {
            if ($m !== $metodo || preg_match($regex, $caminho, $achados) !== 1) {
                continue;
            }

            if ($metodo === 'POST' && !Csrf::valido($_POST['_csrf'] ?? null)) {
                View::render('erro', [
                    'titulo'   => 'Requisição inválida',
                    'mensagem' => 'Sua sessão expirou. Volte à página anterior e tente novamente.',
                ], 419);
                return;
            }

            $parametros = array_filter($achados, 'is_string', ARRAY_FILTER_USE_KEY);
            [$classe, $metodoAcao] = $acao;
            (new $classe())->$metodoAcao(...array_values($parametros));
            return;
        }

        View::render('erro', [
            'titulo'   => 'Página não encontrada',
            'mensagem' => 'A página que você procura não existe.',
        ], 404);
    }
}
