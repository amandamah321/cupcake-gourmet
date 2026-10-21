<?php $titulo = 'Carrinho'; ?>
<h1>Carrinho</h1>

<?php if ($itens === []): ?>
    <div class="vazio">
        <p class="vazio-emoji" aria-hidden="true">🛒</p>
        <p><strong>Seu carrinho está vazio</strong></p>
        <a class="btn" href="/cardapio">Voltar ao cardápio</a>
    </div>
<?php else: ?>
    <ul class="lista-itens">
        <?php foreach ($itens as $i): ?>
            <li class="item">
                <div class="item-img">
                    <?php if (!empty($i['imagem_url'])): ?>
                        <img src="<?= e(asset_url($i['imagem_url'])) ?>" alt="">
                    <?php else: ?>
                        <span class="placeholder" aria-hidden="true">🧁</span>
                    <?php endif; ?>
                </div>
                <div class="item-info">
                    <strong><?= e($i['nome']) ?></strong>
                    <span class="preco"><?= dinheiro($i['preco_unitario']) ?></span>

                    <form method="post" action="/carrinho/atualizar" class="linha">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id_produto" value="<?= (int) $i['id_produto'] ?>">
                        <span class="qtd" data-qty>
                            <button type="button" data-minus aria-label="Diminuir">−</button>
                            <input type="number" name="quantidade" value="<?= (int) $i['quantidade'] ?>" min="1" max="99" inputmode="numeric" aria-label="Quantidade de <?= e($i['nome']) ?>">
                            <button type="button" data-plus aria-label="Aumentar">+</button>
                        </span>
                        <button class="btn btn-pequeno btn-claro" type="submit">Atualizar</button>
                    </form>
                </div>
                <form method="post" action="/carrinho/remover">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id_produto" value="<?= (int) $i['id_produto'] ?>">
                    <button class="btn-icone" type="submit" aria-label="Remover <?= e($i['nome']) ?>" title="Remover">🗑️</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>

    <form method="post" action="/carrinho/dados" class="formulario caixa">
        <?= csrf_field() ?>
        <label>CEP
            <span class="linha">
                <input type="text" name="cep" value="<?= e($cep !== '' ? formata_cep($cep) : '') ?>"
                       placeholder="00000-000" inputmode="numeric" maxlength="9" data-mask="cep">
                <button class="btn btn-pequeno" type="submit" name="acao" value="calcular">Calcular</button>
            </span>
        </label>

        <dl class="totais">
            <div><dt>Subtotal</dt><dd><?= dinheiro($subtotal) ?></dd></div>
            <div><dt>Entrega</dt><dd><?= $frete === null ? 'informe o CEP' : dinheiro($frete) ?></dd></div>
            <div class="total"><dt>Total</dt><dd><?= dinheiro($total) ?></dd></div>
        </dl>

        <label>Adicionar comentário
            <textarea name="comentario" rows="2" maxlength="500" placeholder="Ex.: observações sobre seu pedido"><?= e($comentario) ?></textarea>
        </label>

        <button class="btn btn-bloco" type="submit" name="acao" value="proximo">Próximo →</button>
        <a class="link centro" href="/cardapio">Seguir comprando</a>
    </form>
<?php endif; ?>
