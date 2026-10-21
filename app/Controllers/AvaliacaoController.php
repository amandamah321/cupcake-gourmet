<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Avaliacao;
use App\Models\Produto;

final class AvaliacaoController extends Controller
{
    public function criar(): void
    {
        $volta = $this->destinoSeguro((string) ($_POST['voltar'] ?? '/'), '/');
        $nome = trim((string) ($_POST['nome'] ?? ''));
        $comentario = trim((string) ($_POST['comentario'] ?? ''));
        $estrelas = (int) ($_POST['estrelas'] ?? 0);
        $idProduto = (int) ($_POST['id_produto'] ?? 0);

        if ($nome === '') {
            $this->flash('erro', 'Informe seu nome para enviar a avaliação.');
            $this->redirect($volta);
        }
        if ($estrelas < 1 || $estrelas > 5) {
            $this->flash('erro', 'Escolha uma nota de 1 a 5 estrelas.');
            $this->redirect($volta);
        }
        if (mb_strlen($comentario) > 500) {
            $this->flash('erro', 'O comentário pode ter no máximo 500 caracteres.');
            $this->redirect($volta);
        }

        $idProdutoValido = ($idProduto > 0 && Produto::buscar($idProduto) !== null) ? $idProduto : null;
        Avaliacao::criar(mb_substr($nome, 0, 120), $comentario === '' ? null : $comentario, $estrelas, $idProdutoValido);

        $this->flash('sucesso', 'Obrigado pela sua avaliação!');
        $this->redirect($volta);
    }
}
