<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Validator;
use App\Models\Carrinho;
use App\Services\CarrinhoCalculator;
use App\Services\FreteCalculator;

final class CarrinhoController extends Controller
{
    public function index(): void
    {
        $itens = Carrinho::itens();
        $subtotal = CarrinhoCalculator::subtotal($itens);
        $cep = (string) ($_SESSION['checkout']['cep'] ?? '');

        $frete = null;
        if ($cep !== '') {
            try {
                $frete = FreteCalculator::calcular($cep);
            } catch (\InvalidArgumentException) {
                $frete = null;
            }
        }

        $this->view('carrinho', [
            'itens'       => $itens,
            'subtotal'    => $subtotal,
            'cep'         => $cep,
            'frete'       => $frete,
            'total'       => CarrinhoCalculator::total($subtotal, $frete ?? 0.0),
            'comentario'  => (string) ($_SESSION['checkout']['comentario'] ?? ''),
        ]);
    }

    public function adicionar(): void
    {
        $idProduto = (int) ($_POST['id_produto'] ?? 0);
        $quantidade = (int) ($_POST['quantidade'] ?? 1);
        $volta = $this->destinoSeguro((string) ($_POST['voltar'] ?? '/cardapio'));

        if (!Validator::quantidade($quantidade)) {
            $this->flash('erro', 'Quantidade inválida. A quantidade mínima é 1 (máximo 99).');
            $this->redirect($volta);
        }

        try {
            Carrinho::adicionar($idProduto, $quantidade);
            $this->flash('sucesso', 'Produto adicionado ao carrinho.');
        } catch (\DomainException $e) {
            $this->flash('erro', $e->getMessage());
        }
        $this->redirect($volta);
    }

    public function atualizar(): void
    {
        $idProduto = (int) ($_POST['id_produto'] ?? 0);
        $quantidade = (int) ($_POST['quantidade'] ?? 0);

        if (!Validator::quantidade($quantidade)) {
            $this->flash('erro', 'Quantidade inválida. A quantidade mínima é 1; para retirar o item, use "Remover".');
            $this->redirect('/carrinho');
        }

        Carrinho::alterarQuantidade($idProduto, $quantidade);
        $this->redirect('/carrinho');
    }

    public function remover(): void
    {
        Carrinho::remover((int) ($_POST['id_produto'] ?? 0));
        $this->flash('sucesso', 'Item removido do carrinho.');
        $this->redirect('/carrinho');
    }

    /** Recebe CEP e comentário; "calcular" volta ao carrinho, "proximo" segue ao checkout. */
    public function dados(): void
    {
        $acao = (string) ($_POST['acao'] ?? 'calcular');
        $comentario = trim((string) ($_POST['comentario'] ?? ''));
        $_SESSION['checkout']['comentario'] = mb_substr($comentario, 0, 500);

        $cep = Validator::cepNormalizado((string) ($_POST['cep'] ?? ''));
        if ($cep === null) {
            $this->flash('erro', 'Informe um CEP válido com 8 dígitos para calcular a entrega.');
            $this->redirect('/carrinho');
        }
        $_SESSION['checkout']['cep'] = $cep;

        if ($acao === 'proximo') {
            if (Carrinho::itens() === []) {
                $this->flash('erro', 'Seu carrinho está vazio.');
                $this->redirect('/carrinho');
            }
            $this->redirect('/checkout');
        }

        $this->redirect('/carrinho');
    }
}
