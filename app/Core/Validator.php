<?php
declare(strict_types=1);

namespace App\Core;

/** Regras de validação reutilizáveis (sem dependência de banco ou sessão). */
final class Validator
{
    public static function email(string $valor): bool
    {
        return filter_var(trim($valor), FILTER_VALIDATE_EMAIL) !== false;
    }

    /** Retorna o CEP só com dígitos (8) ou null se inválido. */
    public static function cepNormalizado(string $valor): ?string
    {
        $d = preg_replace('/\D/', '', $valor) ?? '';
        return strlen($d) === 8 ? $d : null;
    }

    /** Algoritmo de Luhn para número de cartão. */
    public static function luhn(string $numero): bool
    {
        $d = preg_replace('/\D/', '', $numero) ?? '';
        $tam = strlen($d);
        if ($tam < 13 || $tam > 19) {
            return false;
        }
        $soma = 0;
        $dobrar = false;
        for ($i = $tam - 1; $i >= 0; $i--) {
            $n = (int) $d[$i];
            if ($dobrar) {
                $n *= 2;
                if ($n > 9) {
                    $n -= 9;
                }
            }
            $soma += $n;
            $dobrar = !$dobrar;
        }
        return $soma % 10 === 0;
    }

    /** Validade no formato MM/AA, válida até o último dia do mês informado. */
    public static function validadeCartao(string $valor, ?\DateTimeImmutable $hoje = null): bool
    {
        if (preg_match('#^(0[1-9]|1[0-2])/(\d{2})$#', trim($valor), $m) !== 1) {
            return false;
        }
        $hoje ??= new \DateTimeImmutable('now');
        $ano = 2000 + (int) $m[2];
        $mes = (int) $m[1];
        $fim = (new \DateTimeImmutable(sprintf('%04d-%02d-01', $ano, $mes)))
            ->modify('last day of this month')
            ->setTime(23, 59, 59);
        return $fim >= $hoje;
    }

    public static function cvv(string $valor): bool
    {
        return preg_match('/^\d{3,4}$/', trim($valor)) === 1;
    }

    public static function quantidade(int $q): bool
    {
        return $q >= 1 && $q <= 99;
    }

    /** @return array<string,string> mapa campo => mensagem (vazio se tudo válido) */
    public static function validarCheckout(array $d, string $metodo): array
    {
        $erros = [];

        if (trim((string) ($d['nome'] ?? '')) === '') {
            $erros['nome'] = 'Informe seu nome.';
        }

        $email = trim((string) ($d['email'] ?? ''));
        if ($email === '') {
            $erros['email'] = 'Informe seu e-mail.';
        } elseif (!self::email($email)) {
            $erros['email'] = 'Informe um endereço de e-mail válido.';
        }

        if (!in_array($metodo, ['CARTAO', 'PIX'], true)) {
            $erros['metodo'] = 'Selecione uma forma de pagamento.';
            return $erros;
        }

        if ($metodo === 'CARTAO') {
            if (trim((string) ($d['nome_cartao'] ?? '')) === '') {
                $erros['nome_cartao'] = 'Informe o nome impresso no cartão.';
            }

            $numero = trim((string) ($d['numero_cartao'] ?? ''));
            if ($numero === '') {
                $erros['numero_cartao'] = 'Informe o número do cartão.';
            } elseif (!self::luhn($numero)) {
                $erros['numero_cartao'] = 'Número de cartão inválido.';
            }

            $validade = trim((string) ($d['validade'] ?? ''));
            if ($validade === '') {
                $erros['validade'] = 'Informe a validade (MM/AA).';
            } elseif (!self::validadeCartao($validade)) {
                $erros['validade'] = 'Validade inválida ou cartão vencido.';
            }

            $cvv = trim((string) ($d['cvv'] ?? ''));
            if ($cvv === '') {
                $erros['cvv'] = 'Informe o CVV.';
            } elseif (!self::cvv($cvv)) {
                $erros['cvv'] = 'CVV inválido.';
            }
        }

        return $erros;
    }
}
