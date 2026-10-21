<?php
$titulo = $produto['nome'];
$idProdutoAvaliacao = (int) $produto['id_produto'];
?>
<p style="padding: 0 20px;">
    <a class="link" href="/cardapio">← Voltar ao cardápio</a>
</p>

<section class="detalhe">
    <div class="detalhe-img">
        <?php if (!empty($produto['imagem_url'])): ?>
            <img src="<?= e(asset_url($produto['imagem_url'])) ?>" alt="<?= e($produto['nome']) ?>">
        <?php else: ?>
            <span class="placeholder grande" aria-hidden="true">🧁</span>
        <?php endif; ?>
    </div>
    <div>
        <p class="muted"><?= e($produto['categoria']) ?></p>
        <h1><?= e($produto['nome']) ?></h1>
        <p class="preco grande"><?= dinheiro($produto['preco']) ?></p>
        <?php if (!empty($produto['descricao'])): ?><p><?= e($produto['descricao']) ?></p><?php endif; ?>

        <form method="post" action="/carrinho/adicionar" class="formulario">
            <?= csrf_field() ?>
            <input type="hidden" name="id_produto" value="<?= (int) $produto['id_produto'] ?>">
            <input type="hidden" name="voltar" value="/produto/<?= (int) $produto['id_produto'] ?>">
            <label>Quantidade
                <span class="qtd" data-qty>
                    <button type="button" data-minus aria-label="Diminuir">−</button>
                    <input type="number" name="quantidade" value="1" min="1" max="99" inputmode="numeric">
                    <button type="button" data-plus aria-label="Aumentar">+</button>
                </span>
            </label>
            <button class="btn btn-bloco" type="submit">Adicionar ao carrinho 🛒</button>
        </form>
    </div>
</section>

<?php include __DIR__ . '/_avaliacoes.php'; ?>