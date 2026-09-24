<?php
require_once __DIR__ . '/../../private_html/models/destinosModel.php';
require_once __DIR__ . '/../../private_html/components/header/header.php';

$usuarioAutenticado = autenticado();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$destino = $id ? buscar_destino_por_id($id) : null;
$whatsapp = 'https://api.whatsapp.com/send?phone=67992027942&text=Ola%2C%20quero%20planejar%20uma%20viagem!';

if (!$destino) {
    header('Location: index.php');
    exit;
}

$galeria = [];
if (!empty($destino['galeria'])) {
    foreach (explode(',', $destino['galeria']) as $item) {
        $imagem = trim((string) $item);
        if ($imagem !== '') {
            $galeria[] = $imagem;
        }
    }
}
if ($galeria === []) {
    $galeria[] = $destino['imagem'];
}

function formatar_preco(float $valor): string
{
    return number_format($valor, 2, ',', '.');
}
?>
<?php  getHeader($destino['nome'], $destino['nome'] . ' em ' . $destino['estado'] . '.'); ?>
<body class="detail-page-body">
    <header class="site-header detail-header destination-page-header"><div class="shell header-inner"><a class="brand" href="index" aria-label="Voltar para o inicio"><img src="<?= asset('images/Logo.png') ?>" alt="Globo Viagens"></a><nav class="main-nav" aria-label="Navegacao principal"><a href="index#destinos">Destinos</a><a href="index#relatos">Relatos</a><a href="index#sobre">Sobre</a></nav><a class="button button-dark" href="<?= $whatsapp ?>" target="_blank" rel="noopener">Falar com especialista</a><?php if ($usuarioAutenticado): ?><a class="dashboard-return-button dashboard-site-return" href="dashboard"><i class="fa-solid fa-gauge-high"></i><span>Dashboard</span></a><?php endif; ?></div></header>

    <main class="detail-page">
        <section class="detail-hero shell">
            <div class="detail-hero-media">
                <img src="<?= asset($destino['imagem']) ?>" alt="Vista de <?= htmlspecialchars($destino['nome']) ?>, <?= htmlspecialchars($destino['estado']) ?>">
            </div>
            <div class="detail-hero-copy">
                <p class="eyebrow"><?= htmlspecialchars($destino['tipo']) ?> · <?= htmlspecialchars($destino['estado']) ?></p>
                <h1><?= htmlspecialchars($destino['nome']) ?></h1>
                <p class="detail-location"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($destino['localizacao']) ?></p>
                <p class="detail-summary"><?= htmlspecialchars($destino['resumo']) ?></p>
                <div class="detail-price-box">
                    <span>A partir de</span>
                    <strong>R$ <?= formatar_preco((float) $destino['preco']) ?></strong>
                </div>
                <div class="detail-actions">
                    <a class="button button-dark" href="<?= $whatsapp ?>" target="_blank" rel="noopener">Solicitar viagem</a>
                    <a class="text-link text-link-dark" href="index#destinos">Voltar aos destinos</a>
                </div>
            </div>
        </section>

        <section class="detail-content shell">
            <div class="detail-main">
                <div class="detail-block">
                    <p class="eyebrow">Sobre o destino</p>
                    <h2>Uma estadia pensada para viver cada detalhe.</h2>
                    <p><?= nl2br(htmlspecialchars($destino['descricao'])) ?></p>
                </div>

                <div class="detail-block">
                    <p class="eyebrow">Destaques</p>
                    <ul class="detail-points">
                        <?php $itens = array_filter(array_map('trim', preg_split('/[;\n]+/', (string) $destino['destaque'] ?: ''))); ?>
                        <?php if ($itens === []): ?>
                            <li>Ambiente confortável para descanso e lazer.</li>
                            <li>Proximidade com praias, restaurantes e cultura local.</li>
                            <li>Estrutura ideal para viagem em família, casal ou amigos.</li>
                        <?php else: ?>
                            <?php foreach ($itens as $item): ?>
                                <li><?= htmlspecialchars($item) ?></li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <aside class="detail-sidebar">
                <div class="detail-card">
                    <p class="eyebrow">Inclui</p>
                    <ul class="detail-meta">
                        <li><i class="fa-solid fa-house"></i> <?= htmlspecialchars($destino['tipo']) ?></li>
                        <li><i class="fa-solid fa-map-location-dot"></i> <?= htmlspecialchars($destino['estado']) ?></li>
                        <li><i class="fa-solid fa-mountain-city"></i> <?= htmlspecialchars($destino['localizacao']) ?></li>
                    </ul>
                </div>
            </aside>
        </section>

        <section class="detail-gallery shell">
            <div class="section-heading detail-heading">
                <div>
                    <p class="eyebrow">Galeria</p>
                    <h2>Veja como é viver aqui.</h2>
                </div>
            </div>
            <div class="gallery-grid">
                <?php foreach ($galeria as $imagem): ?>
                    <figure class="gallery-item">
                        <img src="<?= asset($imagem) ?>" alt="Foto do destino <?= htmlspecialchars($destino['nome']) ?>">
                    </figure>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="shell footer-inner">
            <div>
                <img src="<?= asset('images/Logo.png') ?>" alt="Globo Viagens" class="footer-logo">
                <p>Seja <strong>Globo</strong>.</p>
                <p class="copyright">© <?= date('Y') ?> Globo Viagens</p>
            </div>
            <div class="footer-links">
                <div class="footer-group">
                    <i class="fa-solid fa-location-arrow"></i>
                    <a href="#destinos">Destinos</a>
                </div>
                <div class="footer-group">
                    <i class="fa-solid fa-location-arrow"></i>
                    <a href="#relatos">Relatos</a>
                </div>
                <div class="footer-group">
                    <i class="fa-solid fa-location-arrow"></i>
                    <a href="#sobre">Sobre a Globo</a>
                </div>
            </div>
            <div class="footer-contact">
                <div class="footer-group">
                    <i class="fa-solid fa-location-dot"></i>
                    <p>Campo Grande · MS</p>
                </div>
                <div class="footer-group">
                    <i class="fa-solid fa-location-dot"></i>
                    <p>Rua Ciríaco Maymone · 488</p>
                </div>
                <div class="footer-group">
                    <i class="fa-solid fa-phone"></i>
                    <p>Telefone: (67) 99202-7942</p>
                </div>
                <div class="footer-group">
                    <i class="fa-solid fa-envelope"></i>
                    <p>Email: contato@globoviagens.com.br</p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
