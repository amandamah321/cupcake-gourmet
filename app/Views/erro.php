<?php $titulo = $titulo ?? 'Erro'; ?>
<section class="vazio">
    <p class="vazio-emoji" aria-hidden="true">😕</p>
    <h1><?= e($titulo) ?></h1>
    <p><?= e($mensagem ?? 'Algo deu errado.') ?></p>
    <a class="btn" href="/">Ir para o início</a>
</section>
