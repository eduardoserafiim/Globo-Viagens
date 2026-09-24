<?php
require_once __DIR__ . '/../../private_html/models/videosModel.php';
require_once __DIR__ . '/../../private_html/components/header/header.php';

exigir_autenticacao();
$pagina_painel = 'videos';
$videos = listar_videos_relato();
$sucesso = $_GET['sucesso'] ?? null;
$erro = $_GET['erro'] ?? null;
?>
<?php  getHeader('Relatos em video | Painel', 'Gerenciamento de relatos em vídeo.'); ?>
<body class="dashboard-body">
    <div class="dashboard-layout">
        <?php require_once __DIR__ . '/../../private_html/components/sidebar/sidebar.php'; renderSidebar('videos'); ?>
        <main class="dashboard-main">
            <header class="dashboard-top page-heading"><div><p class="eyebrow">Vozes de quem viajou</p><h1>Relatos em vídeo</h1><p class="page-intro">Adicione histórias reais dos passeios para inspirar quem está escolhendo o próximo destino.</p></div></header>
            <?php if ($sucesso === 'criado'): ?><p class="profile-success"><i class="fa-solid fa-circle-check"></i> Relato publicado no site.</p><?php elseif ($sucesso === 'excluido'): ?><p class="profile-success"><i class="fa-solid fa-circle-check"></i> Relato removido.</p><?php elseif ($erro): ?><p class="form-feedback form-feedback-error"><i class="fa-solid fa-circle-exclamation"></i> Verifique os campos e escolha um arquivo de vídeo válido de até 100 MB.</p><?php endif; ?>

            <section class="video-admin-layout">
                <div class="panel video-form-panel">
                    <div class="panel-heading"><div><p class="eyebrow">Novo relato</p><h2>Adicionar vídeo</h2></div><i class="fa-solid fa-video panel-heading-icon"></i></div>
                    <form class="dashboard-form" method="post" action="../../private_html/controllers/videoController.php" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                        <label>Titulo do relato<input type="text" name="titulo" placeholder="Nossa viagem foi inesquecível" required></label>
                        <label>Nome da pessoa<input type="text" name="autor" placeholder="Mariana e Rafael" required></label>
                        <label>Destino visitado<input type="text" name="destino" placeholder="Porto de Galinhas - PE"></label>
                        <label>Arquivo do vídeo
                            <span class="image-upload-card video-upload-card" role="button" tabindex="0" onclick="document.getElementById('videoFile').click()" onkeydown="if (event.key === 'Enter' || event.key === ' ') document.getElementById('videoFile').click()"><i class="fa-solid fa-cloud-arrow-up"></i><strong id="videoFileName">Escolher vídeo</strong><small>MP4, WebM ou OGG · até 100 MB</small><input id="videoFile" type="file" name="video" accept="video/mp4,video/webm,video/ogg" required></span>
                        </label>
                        <video class="admin-video-preview" id="adminVideoPreview" controls hidden></video>
                        <label>Mensagem curta<textarea name="descricao" maxlength="220" placeholder="O que essa pessoa contou sobre a experiência?"></textarea></label>
                        <button class="button button-dark" type="submit"><i class="fa-solid fa-plus"></i> Publicar relato</button>
                    </form>
                </div>

                <div class="panel video-list-panel">
                    <div class="panel-heading"><div><p class="eyebrow">Publicados</p><h2><?= count($videos) ?> relatos</h2></div></div>
                    <div class="video-admin-list">
                        <?php if ($videos === []): ?><div class="empty-state"><i class="fa-solid fa-film"></i><h3>Nenhum relato publicado</h3><p>Adicione o primeiro vídeo ao site.</p></div><?php endif; ?>
                        <?php foreach ($videos as $video): ?>
                            <article class="video-admin-row">
                                <div class="video-admin-thumb"><i class="fa-solid fa-play"></i></div>
                                <div><span class="card-kicker"><?= htmlspecialchars($video['destino'] ?: 'Relato de viagem') ?></span><h3><?= htmlspecialchars($video['titulo']) ?></h3><p><?= htmlspecialchars($video['autor']) ?></p></div>
                                <form method="post" action="../../private_html/controllers/videoController.php"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>"><input type="hidden" name="acao" value="excluir"><input type="hidden" name="id" value="<?= (int) $video['id'] ?>"><button class="icon-action" type="submit" title="Excluir relato" aria-label="Excluir relato"><i class="fa-solid fa-trash"></i></button></form>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        </main>
    </div>
<script>
    const videoFile = document.getElementById('videoFile');
    const videoFileName = document.getElementById('videoFileName');
    const adminVideoPreview = document.getElementById('adminVideoPreview');
    videoFile.addEventListener('change', () => {
        const file = videoFile.files[0];
        if (!file) return;
        videoFileName.textContent = file.name;
        adminVideoPreview.src = URL.createObjectURL(file);
        adminVideoPreview.hidden = false;
    });
</script>
</body>
</html>
