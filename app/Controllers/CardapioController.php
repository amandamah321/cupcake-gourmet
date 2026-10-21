<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Avaliacao;
use App\Models\Categoria;
use App\Models\Produto;

final class CardapioController extends Controller
{
    public function index(): void
    {
        $categorias = Categoria::todas();
        $filtro = isset($_GET['categoria']) ? (int) $_GET['categoria'] : null;
        if ($filtro !== null && !in_array($filtro, array_column($categorias, 'id_categoria'), true)) {
            $filtro = null;
        }

        $grupos = [];
        foreach (Produto::listar($filtro) as $produto) {
            $grupos[$produto['categoria']][] = $produto;
        }

        $this->view('cardapio', [
            'categorias' => $categorias,
            'filtro'     => $filtro,
            'grupos'     => $grupos,
        ]);
    }

    public function produto(string $id): void
    {
        $produto = Produto::buscar((int) $id);
        if ($produto === null) {
            $this->view('erro', [
                'titulo'   => 'Produto não encontrado',
                'mensagem' => 'Este produto não existe ou não está mais disponível.',
            ], 404);
            return;
        }

        $this->view('produto', [
            'produto'    => $produto,
            'avaliacoes' => Avaliacao::recentes(5, (int) $produto['id_produto']),
        ]);
    }
}
