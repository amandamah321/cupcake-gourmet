<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Validator;

/** Cálculo simulado de entrega a partir do primeiro dígito do CEP. */
final class FreteCalculator
{
    /** @throws \InvalidArgumentException se o CEP for inválido */
    public static function calcular(string $cep): float
    {
        $c = Validator::cepNormalizado($cep);
        if ($c === null) {
            throw new \InvalidArgumentException('CEP inválido.');
        }

        return match ($c[0]) {
            '0'     => 8.00,   // Grande São Paulo
            '1'     => 12.00,  // Interior de São Paulo
            default => 25.00,  // Demais regiões
        };
    }
}
