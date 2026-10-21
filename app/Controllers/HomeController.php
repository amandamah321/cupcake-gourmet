<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Avaliacao;
use App\Models\Produto;

final class HomeController extends Controller
{
    public function index(): void
    {
        $this->view('home', [
            'destaques'  => Produto::destaques(3),
            'avaliacoes' => Avaliacao::recentes(6),
        ]);
    }
}
