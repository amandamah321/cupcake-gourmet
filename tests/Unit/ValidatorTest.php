<?php
declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ValidatorTest extends TestCase
{
    #[DataProvider('emails')]
    public function testEmail(string $email, bool $esperado): void
    {
        $this->assertSame($esperado, Validator::email($email));
    }

    public static function emails(): array
    {
        return [
            'valido'        => ['maria@email.com', true],
            'sem arroba'    => ['mariaemail.com', false],
            'sem dominio'   => ['maria@', false],
            'vazio'         => ['', false],
        ];
    }

    public function testCepNormalizado(): void
    {
        $this->assertSame('01310100', Validator::cepNormalizado('01310-100'));
        $this->assertSame('01310100', Validator::cepNormalizado('01310100'));
        $this->assertNull(Validator::cepNormalizado('1234'));
        $this->assertNull(Validator::cepNormalizado('abcdefgh'));
    }

    #[DataProvider('cartoes')]
    public function testLuhn(string $numero, bool $esperado): void
    {
        $this->assertSame($esperado, Validator::luhn($numero));
    }

    public static function cartoes(): array
    {
        return [
            'aprovado de teste'  => ['4111 1111 1111 1111', true],
            'recusado de teste'  => ['4000 0000 0000 0002', true],
            'digito errado'      => ['4111 1111 1111 1112', false],
            'letras'             => ['abcdefghijklmnop', false],
            'curto demais'       => ['4111', false],
        ];
    }

    public function testValidadeCartao(): void
    {
        $hoje = new \DateTimeImmutable('2026-10-12 10:00:00');

        $this->assertTrue(Validator::validadeCartao('12/26', $hoje));
        $this->assertTrue(Validator::validadeCartao('10/26', $hoje), 'vale até o fim do mês');
        $this->assertFalse(Validator::validadeCartao('09/26', $hoje), 'mês passado');
        $this->assertFalse(Validator::validadeCartao('13/26', $hoje), 'mês inexistente');
        $this->assertFalse(Validator::validadeCartao('1226', $hoje), 'sem barra');
    }

    public function testCvvEQuantidade(): void
    {
        $this->assertTrue(Validator::cvv('123'));
        $this->assertTrue(Validator::cvv('1234'));
        $this->assertFalse(Validator::cvv('12'));
        $this->assertFalse(Validator::cvv('12a'));

        $this->assertTrue(Validator::quantidade(1));
        $this->assertTrue(Validator::quantidade(99));
        $this->assertFalse(Validator::quantidade(0));
        $this->assertFalse(Validator::quantidade(-3));
        $this->assertFalse(Validator::quantidade(100));
    }

    public function testCheckoutComCartaoValidoNaoTemErros(): void
    {
        $dados = [
            'nome' => 'Maria da Silva',
            'email' => 'maria@email.com',
            'nome_cartao' => 'MARIA DA SILVA',
            'numero_cartao' => '4111 1111 1111 1111',
            'validade' => '12/99',
            'cvv' => '123',
        ];
        $this->assertSame([], Validator::validarCheckout($dados, 'CARTAO'));
    }

    public function testCheckoutComCartaoVazioApontaCadaCampo(): void
    {
        $erros = Validator::validarCheckout([], 'CARTAO');

        foreach (['nome', 'email', 'nome_cartao', 'numero_cartao', 'validade', 'cvv'] as $campo) {
            $this->assertArrayHasKey($campo, $erros, "esperava erro em $campo");
        }
    }

    public function testCheckoutPixExigeSomenteNomeEEmail(): void
    {
        $this->assertSame([], Validator::validarCheckout(['nome' => 'Ana', 'email' => 'ana@email.com'], 'PIX'));

        $erros = Validator::validarCheckout(['nome' => 'Ana', 'email' => 'invalido'], 'PIX');
        $this->assertSame('Informe um endereço de e-mail válido.', $erros['email']);
        $this->assertArrayNotHasKey('numero_cartao', $erros);
    }

    public function testCheckoutMetodoInvalido(): void
    {
        $erros = Validator::validarCheckout(['nome' => 'Ana', 'email' => 'ana@email.com'], 'BOLETO');
        $this->assertArrayHasKey('metodo', $erros);
    }
}
