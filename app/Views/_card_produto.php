<article class="card">
    <a class="card-img" href="/produto/<?= (int) $p['id_produto'] ?>">
        <?php if (!empty($p['imagem_url'])): ?>
            <img src="<?= e(asset_url($p['imagem_url'])) ?>" alt="<?= e($p['nome']) ?>" loading="lazy">
        <?php else: ?>
            <span class="placeholder" aria-hidden="true"></span>
        <?php endif; ?>
    </a>
    
    <div class="card-corpo">
        <h3><?= e($p['nome']) ?></h3>
        <?php if (!empty($p['descricao'])): ?>
            <p class="desc"><?= e($p['descricao']) ?></p>
        <?php endif; ?>
        <p class="preco"><?= dinheiro($p['preco']) ?></p>
        <form method="post" action="/carrinho/adicionar">
            <?= csrf_field() ?>
            <input type="hidden" name="id_produto" value="<?= (int) $p['id_produto'] ?>">
            <input type="hidden" name="quantidade" value="1">
            <input type="hidden" name="voltar" value="<?= e($_SERVER['REQUEST_URI'] ?? '/cardapio') ?>">
            <button class="btn btn-bloco" type="submit">Adicionar 🛒</button>
        </form>
        <a class="link" href="/produto/<?= (int) $p['id_produto'] ?>">Detalhes do cupcake</a>
    </div>
</article>
