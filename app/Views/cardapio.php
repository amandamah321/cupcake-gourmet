<?php $titulo = 'Cardápio'; ?>
<h1>Cardápio</h1>

<nav class="abas" aria-label="Categorias">
    <a class="aba <?= $filtro === null ? 'ativa' : '' ?>" href="/cardapio">Todos</a>
    <?php foreach ($categorias as $c): ?>
        <a class="aba <?= $filtro === (int) $c['id_categoria'] ? 'ativa' : '' ?>"
           href="/cardapio?categoria=<?= (int) $c['id_categoria'] ?>"><?= e($c['nome']) ?></a>
    <?php endforeach; ?>
</nav>

<?php if ($grupos === []): ?>
    <p class="muted">Nenhum produto disponível nesta categoria.</p>
<?php endif; ?>

<?php foreach ($grupos as $nomeCategoria => $produtos): ?>
    <h2><?= e($nomeCategoria) ?></h2>
    <div class="grade">
        <?php foreach ($produtos as $p): ?>
            <?php include __DIR__ . '/_card_produto.php'; ?>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>
