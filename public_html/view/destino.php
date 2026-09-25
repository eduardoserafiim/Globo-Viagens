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
                <?php foreach ($galeria as $indice => $imagem): ?>
                    <button class="gallery-item" type="button" data-gallery-index="<?= $indice ?>" aria-label="Ampliar foto <?= $indice + 1 ?> de <?= count($galeria) ?>">
                        <img src="<?= asset($imagem) ?>" alt="Foto do destino <?= htmlspecialchars($destino['nome']) ?>">
                    </button>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <div class="gallery-modal" data-gallery-modal aria-hidden="true">
        <div class="gallery-modal-backdrop" data-gallery-close></div>
        <div class="gallery-modal-dialog" role="dialog" aria-modal="true" aria-label="Galeria de fotos de <?= htmlspecialchars($destino['nome']) ?>">
            <button class="gallery-modal-close" type="button" data-gallery-close aria-label="Fechar galeria"><i class="fa-solid fa-xmark"></i></button>
            <button class="gallery-modal-arrow gallery-modal-prev" type="button" data-gallery-prev aria-label="Foto anterior"><i class="fa-solid fa-chevron-left"></i></button>
            <div class="gallery-modal-main">
                <img data-gallery-image src="<?= asset($galeria[0]) ?>" alt="Foto do destino <?= htmlspecialchars($destino['nome']) ?>">
            </div>
            <button class="gallery-modal-arrow gallery-modal-next" type="button" data-gallery-next aria-label="Próxima foto"><i class="fa-solid fa-chevron-right"></i></button>
            <div class="gallery-modal-thumbs" aria-label="Miniaturas da galeria">
                <?php foreach ($galeria as $indice => $imagem): ?>
                    <button class="gallery-modal-thumb <?= $indice === 0 ? 'active' : '' ?>" type="button" data-gallery-thumb="<?= $indice ?>" aria-label="Ver foto <?= $indice + 1 ?>">
                        <img src="<?= asset($imagem) ?>" alt="">
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <footer class="site-footer">
        <div class="shell footer-inner">
            <div>
                <img src="<?= asset('images/Logo.png') ?>" alt="Globo Viagens" class="footer-logo">
                <p>Seja <strong>Globo</strong>.</p>
                <p class="copyright">© <?= date('Y') ?> GLOBO VIAGENS RESORTS E TURISMO LTDA</p>
                <img src="<?= asset('images/SistemasS.png') ?>" alt="Sistemas S" class="footer-logo">
                <p>Tecnologia que cuida.</p>
                <p class="copyright">© <?= date('Y') ?> SISTEMAS S</p>
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
    <script>
        (() => {
            const modal = document.querySelector('[data-gallery-modal]');
            const items = Array.from(document.querySelectorAll('[data-gallery-index]'));
            if (!modal || items.length === 0) return;

            const image = modal.querySelector('[data-gallery-image]');
            const thumbs = Array.from(modal.querySelectorAll('[data-gallery-thumb]'));
            let indiceAtual = 0;

            const mostrarImagem = (indice) => {
                indiceAtual = (indice + items.length) % items.length;
                const item = items[indiceAtual];
                const itemImage = item.querySelector('img');
                image.src = itemImage.src;
                image.alt = itemImage.alt;
                thumbs.forEach((thumb, indiceThumb) => thumb.classList.toggle('active', indiceThumb === indiceAtual));
            };

            const abrirModal = (indice) => {
                mostrarImagem(indice);
                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');
                document.body.classList.add('gallery-modal-open');
                modal.querySelector('[data-gallery-close]').focus();
            };

            const fecharModal = () => {
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('gallery-modal-open');
            };

            items.forEach((item) => item.addEventListener('click', () => abrirModal(Number(item.dataset.galleryIndex))));
            thumbs.forEach((thumb) => thumb.addEventListener('click', () => mostrarImagem(Number(thumb.dataset.galleryThumb))));
            modal.querySelector('[data-gallery-prev]').addEventListener('click', () => mostrarImagem(indiceAtual - 1));
            modal.querySelector('[data-gallery-next]').addEventListener('click', () => mostrarImagem(indiceAtual + 1));
            modal.querySelectorAll('[data-gallery-close]').forEach((elemento) => elemento.addEventListener('click', fecharModal));
            document.addEventListener('keydown', (event) => {
                if (!modal.classList.contains('is-open')) return;
                if (event.key === 'Escape') fecharModal();
                if (event.key === 'ArrowLeft') mostrarImagem(indiceAtual - 1);
                if (event.key === 'ArrowRight') mostrarImagem(indiceAtual + 1);
            });
        })();
    </script>
</body>
</html>
