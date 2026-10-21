<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Avaliacao;

final class SobreController extends Controller
{
    public function index(): void
    {
        $this->view('sobre', ['avaliacoes' => Avaliacao::recentes(6)]);
    }
}
