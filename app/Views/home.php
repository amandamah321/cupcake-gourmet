<?php $titulo = 'Início'; ?>

<section class="hero">
    <div class="hero-conteudo">
        <h1>Cupcakes artesanais feitos com amor</h1>
        <p>Sabores especiais para momentos únicos.</p>
        <a class="btn" href="/cardapio">Ver produtos →</a>
    </div>
</section>

<h2>Produtos em Destaque</h2>
<div class="grade">
    <?php foreach (($destaques ?? []) as $p): ?>
        <?php include __DIR__ . '/_card_produto.php'; ?>
    <?php endforeach; ?>
</div>

<?php include __DIR__ . '/_avaliacoes.php'; ?>