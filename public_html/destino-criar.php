<?php
require_once __DIR__ . '/../private_html/models/destinosModel.php';
exigir_autenticacao();
$pagina_painel = 'novo-destino';
$erroCadastro = $_GET['erro'] ?? null;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar destino | Painel Globo Viagens</title>
    <link rel="icon" href="<?= asset('images/Logo.png') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../private_html/styles/styles.css">
</head>
<body class="dashboard-body">
    <div class="dashboard-layout">
        <?php require __DIR__ . '/_sidebar.php'; ?>
        <main class="dashboard-main destination-create-page">
            <header class="dashboard-top page-heading">
                <div><a class="back-link dashboard-back-link" href="destinos"><i class="fa-solid fa-arrow-left"></i> Voltar para destinos</a><p class="eyebrow">Construa uma nova experiencia</p><h1>Criar destino</h1><p class="page-intro">Preencha os detalhes e acompanhe ao lado como a página ficará para seus viajantes.</p></div>
            </header>

            <?php if ($erroCadastro === 'csrf'): ?><p class="form-feedback form-feedback-error"><i class="fa-solid fa-circle-exclamation"></i> Sua sessão expirou. Recarregue a página e tente novamente.</p><?php elseif ($erroCadastro === 'validacao'): ?><p class="form-feedback form-feedback-error"><i class="fa-solid fa-circle-exclamation"></i> Não foi possível criar o destino. Revise os campos obrigatórios e selecione pelo menos uma imagem válida.</p><?php endif; ?>

            <form class="destination-creation-layout" id="destinationForm" method="post" action="../private_html/controllers/destinoController.php" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                <section class="destination-editor panel">
                    <div class="editor-section-heading"><span class="editor-step">01</span><div><p class="eyebrow">Identidade</p><h2>Apresente o destino</h2></div></div>
                    <div class="destination-editor-grid">
                        <label>Nome do destino<input name="nome" type="text" placeholder="Ex.: Porto Seguro" required></label>
                        <label>Estado<input name="estado" type="text" maxlength="2" placeholder="BA" required></label>
                        <label>Tipo de estadia<input name="tipo" type="text" placeholder="Apartamento beira-mar" required></label>
                        <label>Localizacao<input name="localizacao" type="text" placeholder="Porto Seguro - BA" required></label>
                    </div>
                </section>

                <section class="destination-editor panel">
                    <div class="editor-section-heading"><span class="editor-step">02</span><div><p class="eyebrow">Narrativa</p><h2>Conte como é viver aqui</h2></div></div>
                    <div class="dashboard-form">
                        <label>Resumo<textarea name="resumo" maxlength="220" placeholder="Uma frase que apresenta o destino" required></textarea></label>
                        <label>Descricao completa<textarea name="descricao" placeholder="Conte sobre os ambientes, a localização e a experiência da estadia." required></textarea></label>
                        <label>Destaques<textarea name="destaque" placeholder="Piscina; Wi-Fi; vista para o mar"></textarea><small class="field-help">Separe cada destaque com ponto e vírgula.</small></label>
                    </div>
                </section>

                <section class="destination-editor panel">
                    <div class="editor-section-heading"><span class="editor-step">03</span><div><p class="eyebrow">Oferta</p><h2>Defina a disponibilidade</h2></div></div>
                    <div class="destination-editor-grid">
                        <label>Preco por noite<input name="preco" type="number" step="0.01" min="0" placeholder="49,90" required></label>
                        <label>Status inicial<select name="status"><option value="ativo">Ativo</option><option value="em_uso">Em uso</option><option value="inativo">Inativo</option></select></label>
                    </div>
                </section>

                <section class="destination-editor panel">
                    <div class="editor-section-heading"><span class="editor-step">04</span><div><p class="eyebrow">Atmosfera</p><h2>Escolha as imagens</h2></div></div>
                    <label class="image-upload-card image-upload-card-large" for="destinationImages"><i class="fa-solid fa-images"></i><strong>Adicionar fotos do destino</strong><small>A primeira imagem será a capa. Você pode selecionar várias de uma vez.</small><input id="destinationImages" name="imagens[]" type="file" accept="image/jpeg,image/png,image/webp" multiple required></label>
                    <div class="selected-images selected-images-large" id="selectedImages" aria-live="polite"></div>
                </section>

                <div class="destination-create-actions"><a class="button button-light" href="destinos">Cancelar</a><button class="button button-dark" type="submit"><i class="fa-solid fa-floppy-disk"></i> Publicar destino</button></div>
            </form>

            <aside class="destination-create-preview">
                <div class="preview-label"><i class="fa-solid fa-eye"></i> Pré-visualização em tempo real</div>
                <article class="destination-preview-card" id="destinationPreview">
                    <div class="destination-preview-cover"><img id="previewCover" src="../images/praiaPara.jpg" alt="Prévia da capa"><span class="preview-gallery-count" id="previewGalleryCount">0 fotos</span></div>
                    <div class="destination-preview-body"><p class="card-kicker" id="previewKicker">Tipo do destino · UF</p><h2 id="previewName">Seu destino aparece aqui</h2><p class="preview-location"><i class="fa-solid fa-location-dot"></i> <span id="previewLocation">Localizacao</span></p><p id="previewSummary">Preencha os campos para visualizar a apresentação.</p><div class="preview-price"><span>A partir de</span><strong id="previewPrice">R$ 0,00</strong><small>por noite</small></div><div class="preview-details"><h3>Sobre o destino</h3><p id="previewDescription">A descrição completa aparecerá aqui.</p><h3>Destaques</h3><ul id="previewHighlights"><li>Seus destaques aparecerão aqui.</li></ul></div><div class="preview-gallery" id="previewGallery"></div></div>
                </article>
            </aside>
        </main>
    </div>
<script>
    const form = document.getElementById('destinationForm');
    const imageInput = document.getElementById('destinationImages');
    const selectedImages = document.getElementById('selectedImages');
    const previewCover = document.getElementById('previewCover');
    const previewGallery = document.getElementById('previewGallery');
    const previewGalleryCount = document.getElementById('previewGalleryCount');
    const selectedFiles = [];
    const value = (name) => form.elements[name]?.value.trim() || '';
    const refreshPreview = () => {
        document.getElementById('previewName').textContent = value('nome') || 'Seu destino aparece aqui';
        document.getElementById('previewKicker').textContent = (value('tipo') || 'Tipo do destino') + ' · ' + (value('estado').toUpperCase() || 'UF');
        document.getElementById('previewLocation').textContent = value('localizacao') || 'Localizacao';
        document.getElementById('previewSummary').textContent = value('resumo') || 'Preencha os campos para visualizar a apresentação.';
        document.getElementById('previewDescription').textContent = value('descricao') || 'A descrição completa aparecerá aqui.';
        document.getElementById('previewPrice').textContent = 'R$ ' + Number(value('preco') || 0).toFixed(2).replace('.', ',');
        const highlights = value('destaque').split(/[;\n]+/).map((item) => item.trim()).filter(Boolean);
        document.getElementById('previewHighlights').innerHTML = (highlights.length ? highlights : ['Seus destaques aparecerão aqui.']).map((item) => { const li = document.createElement('li'); li.textContent = item; return li.outerHTML; }).join('');
    };
    const renderImages = () => {
        selectedImages.innerHTML = '';
        previewGallery.innerHTML = '';
        previewGalleryCount.textContent = selectedFiles.length + (selectedFiles.length === 1 ? ' foto' : ' fotos');
        selectedFiles.forEach((file, index) => {
            const url = URL.createObjectURL(file);
            const item = document.createElement('div'); item.className = 'selected-image';
            const image = document.createElement('img'); image.src = url; image.alt = 'Foto selecionada ' + (index + 1); item.appendChild(image);
            const remove = document.createElement('button'); remove.type = 'button'; remove.setAttribute('aria-label', 'Remover foto'); remove.innerHTML = '<i class="fa-solid fa-xmark"></i>'; remove.addEventListener('click', () => { selectedFiles.splice(index, 1); renderImages(); }); item.appendChild(remove); selectedImages.appendChild(item);
            const previewImage = document.createElement('img'); previewImage.src = url; previewImage.alt = 'Foto da galeria ' + (index + 1); previewGallery.appendChild(previewImage);
            if (index === 0) previewCover.src = url;
        });
        if (!selectedFiles.length) previewCover.src = '../images/praiaPara.jpg';
    };
    imageInput.addEventListener('change', () => { selectedFiles.push(...Array.from(imageInput.files)); renderImages(); });
    form.querySelectorAll('input:not([type="file"]), textarea, select').forEach((field) => field.addEventListener('input', refreshPreview));
    form.addEventListener('submit', () => { const transfer = new DataTransfer(); selectedFiles.forEach((file) => transfer.items.add(file)); imageInput.files = transfer.files; });
    refreshPreview();
</script>
</body>
</html>
