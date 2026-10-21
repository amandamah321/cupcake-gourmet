<section class="avaliacoes" aria-labelledby="titulo-avaliacoes">
    <h2 id="titulo-avaliacoes">Avaliações</h2>

    <?php if (empty($avaliacoes)): ?>
        <p class="muted">Ainda não há avaliações. Seja o primeiro a avaliar!</p>
    <?php else: ?>
        <div class="grade-avaliacoes">
            <?php foreach ($avaliacoes as $a): ?>
                <blockquote class="avaliacao">
                    <p class="estrelas" aria-label="<?= (int) $a['estrelas'] ?> de 5 estrelas"><?= estrelas((int) $a['estrelas']) ?></p>
                    <?php if (!empty($a['comentario'])): ?><p>“<?= e($a['comentario']) ?>”</p><?php endif; ?>
                    <footer>— <?= e($a['nome_exibicao']) ?></footer>
                </blockquote>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</section>
