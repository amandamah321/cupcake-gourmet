<?php $titulo = 'Pedido confirmado'; $pg = $pedido['pagamento']; ?>
<section class="confirmacao">
    <p class="vazio-emoji" aria-hidden="true">✅</p>
    <h1>Pedido confirmado!</h1>
    <p>Obrigado, <strong><?= e($pedido['cliente_nome'] ?? 'cliente') ?></strong>. Seu pedido nº <strong><?= (int) $pedido['id_pedido'] ?></strong> foi registrado.</p>

    <div class="caixa">
        <ul class="lista-resumo">
            <?php foreach ($pedido['itens'] as $i): ?>
                <li><?= (int) $i['quantidade'] ?> × <?= e($i['nome_produto_snapshot']) ?>
                    <span><?= dinheiro((float) $i['preco_unitario'] * (int) $i['quantidade']) ?></span></li>
            <?php endforeach; ?>
        </ul>
        <dl class="totais">
            <div><dt>Subtotal</dt><dd><?= dinheiro($subtotal) ?></dd></div>
            <div><dt>Entrega (CEP <?= e(formata_cep((string) $pedido['cep'])) ?>)</dt><dd><?= dinheiro($frete) ?></dd></div>
            <div class="total"><dt>Total</dt><dd><?= dinheiro($pedido['total']) ?></dd></div>
        </dl>
        <p class="muted">
            Status: <?= e($pedido['status']) ?>
            <?php if ($pg !== null): ?> · Pagamento: <?= e($pg['metodo']) ?> (<?= e($pg['status']) ?>) · Ref. <?= e($pg['referencia_transacao']) ?><?php endif; ?>
        </p>
        <?php if (!empty($pedido['comentario'])): ?><p class="muted">Comentário: <?= e($pedido['comentario']) ?></p><?php endif; ?>
        <p class="aviso">Pagamento simulado: nenhum valor foi cobrado.</p>
    </div>

    <a class="btn" href="/cardapio">Voltar ao cardápio</a>
</section>
