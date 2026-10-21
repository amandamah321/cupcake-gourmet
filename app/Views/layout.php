<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f23f75">
    <meta name="description" content="CupCake Gourmet — cupcakes artesanais feitos com carinho.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <title><?= e($titulo) ?> | CupCake Gourmet</title>
    <link rel="stylesheet" href="/css/style.css">
</head>

<body>
    <div style="padding:6px 16px;text-align:center;background:#fce8ee;color:#8c4c61;font-size:.78rem;font-weight:800">Cupcakes artesanais feitos com carinho <span style="color:#f23f75;padding:0 8px">•</span> Consulte as opções de entrega</div>
    
    <!--HEADER-->
    <header class="topo">
        <div class="topo-barra">
            <button class="menu-toggle" type="button" aria-label="Abrir menu" aria-expanded="false" data-menu-toggle>☰</button>
            <a class="logo" href="/">CupCake Gourmet</a>

            <nav class="menu" id="menu" aria-label="Menu principal">
                <a href="/">Início</a>
                <a href="/cardapio">Cardápio</a>
                <a href="/sobre">Sobre Nós</a>
                <a href="/carrinho">Carrinho</a>
            </nav>

            <a class="carrinho-link" href="/carrinho" aria-label="Carrinho de compras">🛒<span class="badge"><?= cart_count() ?></span></a>

        </div>

    </header>

    <!--MAIN-->
    <main class="container">
        <?php foreach (\App\Core\Flash::todas() as $f): ?>
            <div class="alerta alerta-<?= e($f['tipo']) ?>" role="alert"><?= e($f['msg']) ?></div>
        <?php endforeach; ?>

        <?= $conteudo ?>
    </main>

    <footer class="rodape">
        <p>São Paulo | (00) 00000-0000 | email.cup@gmail.com</p>
        <p>Instagram | WhatsApp</p>
        <p class="pequeno">© 2026 CupCake Gourmet - Todos os direitos reservados</p>
    </footer>
    <script src="/js/app.js" defer></script>
</body>

</html>