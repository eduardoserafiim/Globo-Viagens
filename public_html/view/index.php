<?php
require_once __DIR__ . '/../../private_html/models/destinosModel.php';
require_once __DIR__ . '/../../private_html/models/videosModel.php';
require_once __DIR__ . '/../../private_html/components/header/header.php';

$destinos = listar_destinos();
$videos = listar_videos_relato();
$usuarioAutenticado = autenticado();
$planos = [
    ['nome' => 'Bronze', 'preco' => '29,90', 'classe' => 'bronze', 'beneficios' => ['Cozinha completa privativa', 'Sala', 'Wi-Fi', 'Quartos com ventilador', 'Garagem', 'Ate 1500m do mar']],
    ['nome' => 'Prata', 'preco' => '39,90', 'classe' => 'prata', 'beneficios' => ['Cozinha completa privativa', 'Churrasqueira', 'Sala', 'Wi-Fi', 'Quartos com ar-condicionado', 'Garagem', 'Ate 500m do mar']],
    ['nome' => 'Ouro', 'preco' => '49,90', 'classe' => 'ouro', 'beneficios' => ['Cozinha completa privativa', 'Churrasqueira privativa', 'Piscina', 'Sala', 'Wi-Fi de alta velocidade', 'Quartos com ar-condicionado', 'Garagem para 02 carros', 'Ate 500m do mar']],
];
$whatsapp = 'https://api.whatsapp.com/send?phone=67992027942&text=Ola%2C%20quero%20planejar%20uma%20viagem!';
$preco = static fn (float $valor): string => number_format($valor, 2, ',', '.');
?>
<?php  getHeader('Início', 'Hospedagens e destinos para criar viagens do seu jeito.'); ?>
<body>
    <header class="site-header">
        <div class="shell header-inner">
            <a class="brand" href="index" aria-label="Globo Viagens, inicio">
                <img src="<?= asset('images/Logo.png') ?>" alt="Globo Viagens"></a>
                <nav class="main-nav" aria-label="Navegacao principal">
                     <div>
                        <i class="fa-solid fa-paper-plane"></i>
                        <a href="#destinos">Destinos</a>
                    </div>
                    <div>
                        <i class="fa-solid fa-web-awesome"></i>
                        <a href="#planos">Planos</a>
                    </div>
                    <div>
                        <i class="fa-solid fa-comments"></i>
                        <a href="#relatos">Relatos</a>
                    </div>
                    <div>
                        <i class="fa-solid fa-earth-americas"></i>
                        <a href="#sobre">A Globo</a>
                    </div>
                </nav>
            <button class="menu-toggle" type="button" aria-label="Abrir menu" aria-expanded="false"><i class="fa-solid fa-bars" aria-hidden="true"></i></button>
            <?php if ($usuarioAutenticado): ?>
                <a class="dashboard-return-button dashboard-site-return" href="dashboard"><i class="fa-solid fa-gauge-high"></i>
                    <span>Dashboard</span>
                </a>
            <?php endif; ?>
        </div>
        <div class="mobile-nav" aria-hidden="true">
            <div>
                <i class="fa-solid fa-paper-plane"></i>
                <a href="#destinos">Destinos</a>
            </div>
            <div>
                <i class="fa-solid fa-web-awesome"></i>
                <a href="#planos">Planos</a>
            </div>
            <div>
                <i class="fa-solid fa-comments"></i>
                <a href="#relatos">Relatos</a>
            </div>
            <div>
                <i class="fa-solid fa-earth-americas"></i>
                <a href="#sobre">A Globo</a>
            </div>
        </div>
    </header>
    <main>
        <section class="hero shell">
            <div class="hero-copy">
                <p class="eyebrow">Viagens que viram memórias inesquecíveis.</p>
                <h1>O proximo capitulo da sua viagem comeca aqui.</h1>
                <p class="hero-text">Destinos especiais, hospedagens escolhidas a dedo e um atendimento próximo para você viajar com conforto.</p>
                <div class="hero-actions">
                    <a class="button button-dark" href="#destinos">
                        Explorar destinos <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
                    </a>
                    <a class="text-link" href="#relatos">
                        Ver relatos de viajantes <i class="fa-solid fa-arrow-down" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
            <div class="hero-visual">
                <img src="<?= asset('images/praiaPara.jpg') ?>" alt="Praia">
            </div>
        </section>
        <section class="trust-strip"><div class="shell trust-inner"><p>Para quem quer mais do que um destino</p><div><i class="fa-solid fa-star" aria-hidden="true"></i> Atendimento humano</div><div><i class="fa-solid fa-star" aria-hidden="true"></i> Curadoria local</div><div><i class="fa-solid fa-star" aria-hidden="true"></i> Experiencias reais</div></div></section>
        <section class="section shell" id="destinos">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Escolha seu proximo sim</p>
                    <h2>Destinos para viver,<br>não apenas visitar.</h2>
                </div>
                <p class="section-intro">Uma selecao de lugares que combinam conforto, paisagens e aquela vontade gostosa de ficar mais um pouco.</p>
            </div>
            <div class="destination-grid">
                <?php foreach ($destinos as $index => $destino): ?>
                    <article class="destination-card <?= $index === 0 ? 'destination-card-featured' : '' ?>" data-href="destino?id=<?= (int) $destino['id'] ?>">
                        <div class="destination-image">
                            <img src="<?= asset($destino['imagem']) ?>" alt="Vista de <?= htmlspecialchars($destino['nome']) ?>, <?= htmlspecialchars($destino['estado']) ?>">
                            <span class="card-index">0<?= $index + 1 ?></span>
                        </div>
                        <div class="destination-content">
                            <p class="card-kicker"><?= htmlspecialchars($destino['tipo']) ?> · <?= htmlspecialchars($destino['estado']) ?></p>
                            <h3><?= htmlspecialchars($destino['nome']) ?></h3>
                            <p><?= htmlspecialchars($destino['resumo']) ?></p>
                            <div class="destination-footer">
                                <strong>A partir de <b>R$ <?= $preco((float) $destino['preco']) ?></b></strong>
                                <a href="destino?id=<?= (int) $destino['id'] ?>" aria-label="Ver detalhes de <?= htmlspecialchars($destino['nome']) ?>"><i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <div class="center-action">
                <a class="button button-light" href="destinos-lista">Ver todos os destinos <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
            </div>
        </section>
        <section class="plans-section" id="planos"><div class="shell"><div class="section-heading"><div><p class="eyebrow">Acomodacao do seu jeito</p><h2>Escolha o plano<br>ideal para voce.</h2></div><p class="section-intro">Estruturas pensadas para deixar sua estadia mais confortavel, do essencial ao completo.</p></div><div class="plans-grid">
                <?php foreach ($planos as $index => $plano): ?>
                    <article class="plan-card plan-card-<?= htmlspecialchars($plano['classe']) ?>">
                        <div class="plan-medal">
                            <i class="fa-solid <?= $index === 0 ? 'fa-compass' : ($index === 1 ? 'fa-star' : 'fa-crown') ?>" aria-hidden="true"></i>
                        </div>
                        <div class="plan-card-top">
                            <div>
                                <span class="plan-rank">Acomodacao</span>
                                <h3><?= htmlspecialchars($plano['nome']) ?></h3>
                            </div>
                        </div>
                        <ul>
                            <?php foreach ($plano['beneficios'] as $beneficio): ?>
                                <li><?= htmlspecialchars($beneficio) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="plan-footer">
                            <div>
                                <small>Diarias<br>a partir de</small>
                                <strong><span>R$</span> <?= htmlspecialchars($plano['preco']) ?></strong>
                                <small>por pessoa</small>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
        </section>
        <section class="testimonials-section" id="relatos">
            <div class="shell">
                <div class="section-heading section-heading-light">
                    <div><p class="eyebrow">Relatos que inspiram</p><h2>Quem viaja,<br>conta melhor.</h2></div>
                    <p class="section-intro">Experiencias reais de quem já viveu um destino com a Globo Viagens.</p>
                </div>
                <?php if ($videos === []): ?>
                    <div class="testimonials-empty"><i class="fa-solid fa-film"></i><p>Em breve, histórias reais dos nossos viajantes.</p></div>
                <?php else: ?>
                    <?php $videoPrincipal = $videos[0]; ?>
                    <div class="testimonials-carousel" data-carousel>
                        <article class="testimonial-main">
                            <div class="testimonial-video"><video id="mainTestimonialVideo" src="<?= asset($videoPrincipal['url']) ?>" title="<?= htmlspecialchars($videoPrincipal['titulo']) ?>" controls preload="metadata"></video></div>
                            <div class="testimonial-copy"><p class="eyebrow" id="mainTestimonialDestination"><?= htmlspecialchars($videoPrincipal['destino'] ?: 'Relato de viagem') ?></p><h3 id="mainTestimonialTitle"><?= htmlspecialchars($videoPrincipal['titulo']) ?></h3><p id="mainTestimonialDescription"><?= htmlspecialchars($videoPrincipal['descricao']) ?></p><strong id="mainTestimonialAuthor"><?= htmlspecialchars($videoPrincipal['autor']) ?></strong></div>
                        </article>
                        <div class="testimonial-carousel-bar">
                            <button class="carousel-arrow" type="button" data-carousel-prev aria-label="Relato anterior"><i class="fa-solid fa-arrow-left"></i></button>
                            <div class="testimonial-track" data-carousel-track>
                                <?php foreach ($videos as $indice => $video): ?>
                                    <button class="testimonial-thumb <?= $indice === 0 ? 'active' : '' ?>" type="button" data-video-url="<?= htmlspecialchars(asset($video['url'])) ?>" data-video-title="<?= htmlspecialchars($video['titulo']) ?>" data-video-destination="<?= htmlspecialchars($video['destino'] ?: 'Relato de viagem') ?>" data-video-description="<?= htmlspecialchars($video['descricao']) ?>" data-video-author="<?= htmlspecialchars($video['autor']) ?>" aria-label="Ver relato de <?= htmlspecialchars($video['autor']) ?>">
                                        <span class="testimonial-thumb-media"><i class="fa-solid fa-play"></i></span><span><small><?= htmlspecialchars($video['destino'] ?: 'Relato') ?></small><strong><?= htmlspecialchars($video['autor']) ?></strong></span>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                            <button class="carousel-arrow" type="button" data-carousel-next aria-label="Próximo relato"><i class="fa-solid fa-arrow-right"></i></button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        <section class="about-section shell" id="sobre">
            <div class="about-image">
                <img src="<?= asset('images/litoralNordestino.jpg') ?>" alt="Litoral nordestino">
            </div>
            <div class="about-copy">
                <p class="eyebrow">Sobre a Globo Viagens</p>
                <h2>Tem lugar que a gente conhece. E tem lugar que conhece a gente.</h2>
                <p>Somos uma agencia de viagens de Campo Grande que acredita em roteiros com verdade, cuidado e boas historias para contar.</p>
                <a class="text-link" href="<?= $whatsapp ?>" target="_blank" rel="noopener">Vamos conversar <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
            </div>
        </section>
        <section class="final-cta shell">
            <p class="eyebrow">Seu proximo destino</p>
            <h2>O mundo esta logo ali.</h2>
            <a class="button button-dark" href="<?= $whatsapp ?>" target="_blank" rel="noopener">Comecar a planejar <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
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
                    <p>Telefone: (67) 99999-9999</p>
                </div>
                <div class="footer-group">
                    <i class="fa-solid fa-envelope"></i>
                    <p>Email: contato@globoviagens.com.br</p>
                </div>
            </div>
        </div>
    </footer>
    <a class="whatsapp-flutuante" href="<?= $whatsapp ?>" target="_blank" rel="noopener" aria-label="Conversar com a Globo Viagens pelo WhatsApp">
        <span class="whatsapp-icone" aria-hidden="true"><i class="fa-brands fa-whatsapp"></i></span>
        <span class="whatsapp-texto">Fale conosco</span>
    </a>
    <script src="<?= asset('javascript/menuMobile.js') ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.destination-card').forEach((card) => {
                card.addEventListener('click', (event) => {
                    if (event.target.closest('a')) {
                        return;
                    }
                    const href = card.dataset.href;
                    if (href) {
                        window.location.href = href;
                    }
                });
            });

            const carousel = document.querySelector('[data-carousel]');
            if (carousel) {
                const mainVideo = document.getElementById('mainTestimonialVideo');
                const thumbs = Array.from(carousel.querySelectorAll('.testimonial-thumb'));
                let activeIndex = 0;

                const showTestimonial = (index) => {
                    activeIndex = (index + thumbs.length) % thumbs.length;
                    const thumb = thumbs[activeIndex];
                    mainVideo.pause();
                    mainVideo.src = thumb.dataset.videoUrl;
                    mainVideo.title = thumb.dataset.videoTitle;
                    mainVideo.load();
                    document.getElementById('mainTestimonialDestination').textContent = thumb.dataset.videoDestination;
                    document.getElementById('mainTestimonialTitle').textContent = thumb.dataset.videoTitle;
                    document.getElementById('mainTestimonialDescription').textContent = thumb.dataset.videoDescription;
                    document.getElementById('mainTestimonialAuthor').textContent = thumb.dataset.videoAuthor;
                    thumbs.forEach((item, itemIndex) => item.classList.toggle('active', itemIndex === activeIndex));
                    thumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                };

                thumbs.forEach((thumb, index) => thumb.addEventListener('click', () => showTestimonial(index)));
                carousel.querySelector('[data-carousel-prev]').addEventListener('click', () => showTestimonial(activeIndex - 1));
                carousel.querySelector('[data-carousel-next]').addEventListener('click', () => showTestimonial(activeIndex + 1));
            }
        });
    </script>
</body>
</html>
