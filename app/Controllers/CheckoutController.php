<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Validator;
use App\Models\Carrinho;
use App\Services\CarrinhoCalculator;
use App\Services\CheckoutService;
use App\Services\FreteCalculator;

final class CheckoutController extends Controller
{
    public function index(): void
    {
        $this->view('checkout', $this->contexto() + ['erros' => [], 'antigo' => []]);
    }

    public function finalizar(): void
    {
        $contexto = $this->contexto();
        $metodo = (string) ($_POST['metodo'] ?? '');
        $erros = Validator::validarCheckout($_POST, $metodo);

        // Nunca devolve número do cartão nem CVV para a tela.
        $antigo = $_POST;
        unset($antigo['numero_cartao'], $antigo['cvv'], $antigo['_csrf']);

        if ($erros !== []) {
            $this->view('checkout', $contexto + ['erros' => $erros, 'antigo' => $antigo], 422);
            return;
        }

        try {
            $resultado = (new CheckoutService())->finalizar(
                $contexto['itens'],
                ['nome' => trim((string) $_POST['nome']), 'email' => trim((string) $_POST['email'])],
                $contexto['cep'],
                $_SESSION['checkout']['comentario'] ?? null,
                $metodo,
                $_POST
            );
        } catch (\Throwable $e) {
            error_log('Falha no checkout: ' . $e->getMessage());
            $this->flash('erro', 'Não foi possível concluir o pedido. Tente novamente.');
            $this->view('checkout', $contexto + ['erros' => [], 'antigo' => $antigo], 500);
            return;
        }

        if ($resultado['status'] === 'APROVADO') {
            $_SESSION['ultimo_pedido'] = $resultado['id_pedido'];
            unset($_SESSION['checkout']);
            $this->redirect('/pedido/' . $resultado['id_pedido']);
        }

        $this->flash('erro', 'Pagamento recusado. Confira os dados do cartão ou escolha outra forma de pagamento.');
        $this->view('checkout', $contexto + ['erros' => [], 'antigo' => $antigo], 422);
    }

    /** Dados necessários ao checkout; volta ao carrinho se algo estiver faltando. */
    private function contexto(): array
    {
        $itens = Carrinho::itens();
        if ($itens === []) {
            $this->flash('erro', 'Seu carrinho está vazio.');
            $this->redirect('/carrinho');
        }

        $cep = (string) ($_SESSION['checkout']['cep'] ?? '');
        if ($cep === '') {
            $this->flash('erro', 'Informe o CEP para calcular a entrega.');
            $this->redirect('/carrinho');
        }

        $subtotal = CarrinhoCalculator::subtotal($itens);
        $frete = FreteCalculator::calcular($cep);

        return [
            'itens'    => $itens,
            'cep'      => $cep,
            'subtotal' => $subtotal,
            'frete'    => $frete,
            'total'    => CarrinhoCalculator::total($subtotal, $frete),
        ];
    }
}
