<?php
require_once __DIR__ . '/../private_html/models/destinosModel.php';
$filtros = [
    'busca' => trim((string) ($_GET['busca'] ?? '')),
    'estado' => trim((string) ($_GET['estado'] ?? '')),
    'tipo' => trim((string) ($_GET['tipo'] ?? '')),
];
$pagina = filter_var($_GET['pagina'] ?? 1, FILTER_VALIDATE_INT) ?: 1;
$resultado = listar_destinos_paginados($filtros, $pagina, 20);
$opcoes = opcoes_filtro_destinos();
$totalPaginas = max(1, (int) ceil($resultado['total'] / $resultado['por_pagina']));
if ($pagina > $totalPaginas && $resultado['total'] > 0) {
    $pagina = $totalPaginas;
    $resultado = listar_destinos_paginados($filtros, $pagina, 20);
}
$preco = static fn (float $valor): string => number_format($valor, 2, ',', '.');
$query = static function (int $numero) use ($filtros): string {
    return http_build_query(array_filter($filtros + ['pagina' => $numero], static fn ($valor): bool => $valor !== ''));
};
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Explore todos os destinos ativos da Globo Viagens.">
    <title>Todos os destinos | Globo Viagens</title>
    <link rel="icon" href="<?= asset('images/Logo.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../private_html/styles/styles.css">
</head>
<body class="destinations-list-body">
    <header class="site-header detail-header">
        <div class="shell header-inner">
            <a class="brand" href="index" aria-label="Globo Viagens, inicio"><img src="<?= asset('images/Logo.png') ?>" alt="Globo Viagens"></a>
            <nav class="main-nav" aria-label="Navegacao principal"><a href="index#destinos">Destinos</a><a href="index#relatos">Relatos</a><a href="index#sobre">A Globo</a></nav>
            <a class="button button-dark" href="index"><i class="fa-solid fa-arrow-left"></i> Voltar ao inicio</a>
        </div>
    </header>
    <main class="destinations-list-page">
        <section class="shell destinations-list-heading"><p class="eyebrow">Catalogo completo</p><h1>Todos os destinos</h1><p>Encontre o lugar certo para a próxima história.</p></section>
        <section class="shell destination-filters-section">
            <form class="destination-filters" method="get">
                <label class="destination-search"><i class="fa-solid fa-magnifying-glass"></i><input type="search" name="busca" value="<?= htmlspecialchars($filtros['busca']) ?>" placeholder="Buscar por destino ou cidade"></label>
                <label>Estado<select name="estado"><option value="">Todos os estados</option><?php foreach ($opcoes['estados'] as $estado): ?><option value="<?= htmlspecialchars($estado) ?>" <?= $filtros['estado'] === $estado ? 'selected' : '' ?>><?= htmlspecialchars($estado) ?></option><?php endforeach; ?></select></label>
                <label>Tipo<select name="tipo"><option value="">Todos os tipos</option><?php foreach ($opcoes['tipos'] as $tipo): ?><option value="<?= htmlspecialchars($tipo) ?>" <?= $filtros['tipo'] === $tipo ? 'selected' : '' ?>><?= htmlspecialchars($tipo) ?></option><?php endforeach; ?></select></label>
                <button class="button button-dark" type="submit"><i class="fa-solid fa-filter"></i> Filtrar</button>
                <?php if ($filtros['busca'] || $filtros['estado'] || $filtros['tipo']): ?><a class="clear-filters" href="destinos-lista">Limpar</a><?php endif; ?>
            </form>
        </section>
        <section class="shell destinations-results">
            <div class="results-heading"><p><?= $resultado['total'] ?> destino(s) encontrado(s)</p><span>Página <?= $pagina ?> de <?= $totalPaginas ?></span></div>
            <?php if ($resultado['itens'] === []): ?><div class="testimonials-empty destinations-empty"><i class="fa-solid fa-map-location-dot"></i><p>Nenhum destino encontrado com esses filtros.</p><a class="button button-dark" href="destinos-lista">Ver todos</a></div><?php else: ?>
                <div class="destination-grid destinations-results-grid">
                    <?php foreach ($resultado['itens'] as $index => $destino): ?>
                        <article class="destination-card" data-href="destino?id=<?= (int) $destino['id'] ?>"><div class="destination-image"><img src="<?= asset($destino['imagem']) ?>" alt="Vista de <?= htmlspecialchars($destino['nome']) ?>, <?= htmlspecialchars($destino['estado']) ?>"><span class="card-index"><?= str_pad((string) (($pagina - 1) * 20 + $index + 1), 2, '0', STR_PAD_LEFT) ?></span></div><div class="destination-content"><p class="card-kicker"><?= htmlspecialchars($destino['tipo']) ?> · <?= htmlspecialchars($destino['estado']) ?></p><h3><?= htmlspecialchars($destino['nome']) ?></h3><p><?= htmlspecialchars($destino['resumo']) ?></p><div class="destination-footer"><strong>A partir de <b>R$ <?= $preco((float) $destino['preco']) ?></b></strong><a href="destino?id=<?= (int) $destino['id'] ?>" aria-label="Ver detalhes de <?= htmlspecialchars($destino['nome']) ?>"><i class="fa-solid fa-arrow-up-right-from-square"></i></a></div></div></article>
                    <?php endforeach; ?>
                </div>
                <?php if ($totalPaginas > 1): ?><nav class="pagination" aria-label="Paginação de destinos"><a class="pagination-arrow <?= $pagina <= 1 ? 'disabled' : '' ?>" href="<?= $pagina > 1 ? 'destinos-lista?' . $query($pagina - 1) : '#' ?>" aria-label="Página anterior"><i class="fa-solid fa-arrow-left"></i></a><?php for ($numero = 1; $numero <= $totalPaginas; $numero++): ?><a class="<?= $numero === $pagina ? 'active' : '' ?>" href="destinos-lista?<?= $query($numero) ?>"><?= $numero ?></a><?php endfor; ?><a class="pagination-arrow <?= $pagina >= $totalPaginas ? 'disabled' : '' ?>" href="<?= $pagina < $totalPaginas ? 'destinos-lista?' . $query($pagina + 1) : '#' ?>" aria-label="Próxima página"><i class="fa-solid fa-arrow-right"></i></a></nav><?php endif; ?>
            <?php endif; ?>
        </section>
    </main>
    <script>
        document.querySelectorAll('.destination-card').forEach((card) => card.addEventListener('click', (event) => { if (!event.target.closest('a')) window.location.href = card.dataset.href; }));
    </script>
</body>
</html>
