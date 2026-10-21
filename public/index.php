<?php
declare(strict_types=1);

// Servidor embutido do PHP: deixa arquivos estáticos (css, js, img) passarem direto.
if (PHP_SAPI === 'cli-server') {
    $arquivo = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file($arquivo)) {
        return false;
    }
}

date_default_timezone_set('America/Sao_Paulo');
session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
]);

require __DIR__ . '/../app/autoload.php';

use App\Core\Router;
use App\Core\View;

// Tratamento global de erros: o visitante vê uma mensagem amigável, o detalhe vai para o log.
set_exception_handler(function (\Throwable $e): void {
    error_log($e::class . ': ' . $e->getMessage());
    $mensagem = $e instanceof \PDOException
        ? 'Não foi possível acessar o banco de dados. Tente novamente em instantes.'
        : 'Ocorreu um erro inesperado. Tente novamente.';
    try {
        View::render('erro', ['titulo' => 'Erro', 'mensagem' => $mensagem], 500);
    } catch (\Throwable) {
        http_response_code(500);
        echo $mensagem;
    }
});

$router = new Router();
require __DIR__ . '/../app/routes.php';
$router->dispatch(
    $_SERVER['REQUEST_METHOD'] ?? 'GET',
    parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/'
);
