<?php
declare(strict_types=1);

use App\Controllers\AvaliacaoController;
use App\Controllers\CardapioController;
use App\Controllers\CarrinhoController;
use App\Controllers\CheckoutController;
use App\Controllers\HomeController;
use App\Controllers\PedidoController;
use App\Controllers\SobreController;

/** @var \App\Core\Router $router */
$router->get('/', [HomeController::class, 'index']);
$router->get('/cardapio', [CardapioController::class, 'index']);
$router->get('/produto/{id}', [CardapioController::class, 'produto']);
$router->get('/sobre', [SobreController::class, 'index']);

$router->get('/carrinho', [CarrinhoController::class, 'index']);
$router->post('/carrinho/adicionar', [CarrinhoController::class, 'adicionar']);
$router->post('/carrinho/atualizar', [CarrinhoController::class, 'atualizar']);
$router->post('/carrinho/remover', [CarrinhoController::class, 'remover']);
$router->post('/carrinho/dados', [CarrinhoController::class, 'dados']);

$router->get('/checkout', [CheckoutController::class, 'index']);
$router->post('/checkout', [CheckoutController::class, 'finalizar']);
$router->get('/pedido/{id}', [PedidoController::class, 'confirmacao']);

$router->post('/avaliacoes', [AvaliacaoController::class, 'criar']);
