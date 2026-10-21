<?php
$titulo = 'Pagamento';
$metodoAtual = $antigo['metodo'] ?? 'CARTAO';
$campo = fn (string $n): string => e($antigo[$n] ?? '');
$erro = fn (string $n): string => e($erros[$n] ?? '');
?>
<p><a class="link" href="/carrinho">← Voltar ao carrinho</a></p>
<h1>Selecione o método de pagamento</h1>

<aside class="caixa resumo">
    <p><strong>Resumo do pedido</strong></p>
    <ul>
        <?php foreach ($itens as $i): ?>
            <li><?= (int) $i['quantidade'] ?> × <?= e($i['nome']) ?> <span><?= dinheiro((float) $i['preco_unitario'] * (int) $i['quantidade']) ?></span></li>
        <?php endforeach; ?>
    </ul>
    <p>Entrega (CEP <?= e(formata_cep($cep)) ?>): <strong><?= dinheiro($frete) ?></strong></p>
    <p class="total-linha">Total: <strong><?= dinheiro($total) ?></strong></p>
</aside>

<p class="aviso">Pagamento <strong>simulado</strong>: nenhum valor será cobrado.</p>

<form method="post" action="/checkout" class="formulario" data-form-checkout novalidate>
    <?= csrf_field() ?>

    <?php if (!empty($erros['metodo'])): ?><p class="erro-campo"><?= $erro('metodo') ?></p><?php endif; ?>
    <div class="metodos">
        <label class="opcao-pagto">
            <input type="radio" name="metodo" value="CARTAO" <?= $metodoAtual === 'CARTAO' ? 'checked' : '' ?>>
            <span>💳<br>Cartão de Crédito</span>
        </label>
        <label class="opcao-pagto">
            <input type="radio" name="metodo" value="PIX" <?= $metodoAtual === 'PIX' ? 'checked' : '' ?>>
            <span>◆<br>Pix</span>
        </label>
    </div>

    <label>Nome
        <input type="text" name="nome" value="<?= $campo('nome') ?>" autocomplete="name">
        <span class="erro-campo" data-erro-for="nome"><?= $erro('nome') ?></span>
    </label>
    <label>E-mail
        <input type="email" name="email" value="<?= $campo('email') ?>" placeholder="exemplo@email.com" autocomplete="email">
        <span class="erro-campo" data-erro-for="email"><?= $erro('email') ?></span>
    </label>

    <div data-painel="CARTAO">
        <label>Nome impresso no cartão
            <input type="text" name="nome_cartao" value="<?= $campo('nome_cartao') ?>" placeholder="Maria da Silva" autocomplete="cc-name">
            <span class="erro-campo" data-erro-for="nome_cartao"><?= $erro('nome_cartao') ?></span>
        </label>
        <label>Número do cartão
            <input type="text" name="numero_cartao" placeholder="0000 0000 0000 0000" inputmode="numeric" autocomplete="cc-number" data-mask="cartao">
            <span class="erro-campo" data-erro-for="numero_cartao"><?= $erro('numero_cartao') ?></span>
        </label>
        <div class="duas-colunas">
            <label>Validade
                <input type="text" name="validade" value="<?= $campo('validade') ?>" placeholder="MM/AA" inputmode="numeric" autocomplete="cc-exp" data-mask="validade">
                <span class="erro-campo" data-erro-for="validade"><?= $erro('validade') ?></span>
            </label>
            <label>CVV
                <input type="text" name="cvv" placeholder="123" inputmode="numeric" autocomplete="cc-csc" data-mask="cvv">
                <span class="erro-campo" data-erro-for="cvv"><?= $erro('cvv') ?></span>
            </label>
        </div>
        <details class="dica">
            <summary>Cartões de teste</summary>
            <p>Aprovado: <code>4111 1111 1111 1111</code><br>Recusado: <code>4000 0000 0000 0002</code><br>Validade futura e CVV com 3 dígitos.</p>
        </details>
    </div>

    <div data-painel="PIX" hidden>
        <div class="pix-box" aria-label="QR Code simulado">
            <span class="qr" aria-hidden="true">▦</span>
            <p>QR Code simulado. Ao confirmar, o pagamento via Pix é aprovado automaticamente.</p>
        </div>
    </div>

    <button class="btn btn-bloco" type="submit">🔒 PAGAR <?= dinheiro($total) ?></button>
</form>
