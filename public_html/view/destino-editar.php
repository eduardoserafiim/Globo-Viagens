<?php
require_once __DIR__ . '/../../private_html/models/destinosModel.php';
require_once __DIR__ . '/../../private_html/components/header/header.php';

exigir_autenticacao();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$destino = $id ? buscar_destino_por_id($id) : null;
if (!$destino) {
    header('Location: destinos.php?erro=destino');
    exit;
}
$pagina_painel = 'destinos';
$erro = $_GET['erro'] ?? null;
$galeria = array_values(array_filter(array_map('trim', explode(',', (string) $destino['galeria']))));
$imagens = array_merge([$destino['imagem']], $galeria);
?>
<?php  getHeader('Editar destino | Painel', 'Atualizacao de destino.'); ?>
<body class="dashboard-body">
    <div class="dashboard-layout">
        <?php require_once __DIR__ . '/../../private_html/components/sidebar/sidebar.php'; renderSidebar('destinos'); ?>
        <main class="dashboard-main destination-create-page">
            <header class="dashboard-top page-heading"><div><a class="back-link dashboard-back-link" href="destinos"><i class="fa-solid fa-arrow-left"></i> Voltar para destinos</a><p class="eyebrow">Gerenciamento de experiencia</p><h1>Editar destino</h1><p class="page-intro">Atualize os dados de <?= htmlspecialchars($destino['nome']) ?> e acompanhe a prévia ao lado.</p></div></header>
            <?php if ($erro): ?><p class="form-feedback form-feedback-error"><i class="fa-solid fa-circle-exclamation"></i> Não foi possível salvar as alterações. Revise os campos obrigatórios.</p><?php endif; ?>
            <form class="destination-creation-layout" id="destinationEditForm" method="post" action="/action.php?acao=destino" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>"><input type="hidden" name="acao" value="editar"><input type="hidden" name="id" value="<?= (int) $destino['id'] ?>">
                <section class="destination-editor panel"><div class="editor-section-heading"><span class="editor-step">01</span><div><p class="eyebrow">Identidade</p><h2>Dados principais</h2></div></div><div class="destination-editor-grid"><label>Nome do destino<input name="nome" value="<?= htmlspecialchars($destino['nome']) ?>" required></label><label>Estado<input name="estado" maxlength="2" value="<?= htmlspecialchars($destino['estado']) ?>" required></label><label>Tipo de estadia<input name="tipo" value="<?= htmlspecialchars($destino['tipo']) ?>" required></label><label>Localizacao<input name="localizacao" value="<?= htmlspecialchars($destino['localizacao']) ?>" required></label></div></section>
                <section class="destination-editor panel"><div class="editor-section-heading"><span class="editor-step">02</span><div><p class="eyebrow">Narrativa</p><h2>Conte a experiência</h2></div></div><div class="dashboard-form"><label>Resumo<textarea name="resumo" maxlength="220" required><?= htmlspecialchars($destino['resumo']) ?></textarea></label><label>Descricao completa<textarea name="descricao" required><?= htmlspecialchars($destino['descricao']) ?></textarea></label><label>Destaques<textarea name="destaque"><?= htmlspecialchars($destino['destaque']) ?></textarea><small class="field-help">Separe cada destaque com ponto e vírgula.</small></label></div></section>
                <section class="destination-editor panel"><div class="editor-section-heading"><span class="editor-step">03</span><div><p class="eyebrow">Oferta</p><h2>Disponibilidade</h2></div></div><div class="destination-editor-grid"><label>Preco por noite<input name="preco" type="number" step="0.01" min="0" value="<?= htmlspecialchars((string) $destino['preco']) ?>" required></label><label>Status<select name="status"><option value="ativo" <?= ($destino['status'] ?? '') === 'ativo' ? 'selected' : '' ?>>Ativo</option><option value="em_uso" <?= ($destino['status'] ?? '') === 'em_uso' ? 'selected' : '' ?>>Em uso</option><option value="inativo" <?= ($destino['status'] ?? '') === 'inativo' ? 'selected' : '' ?>>Inativo</option></select></label></div></section>
                <section class="destination-editor panel"><div class="editor-section-heading"><span class="editor-step">04</span><div><p class="eyebrow">Imagens</p><h2>Atualizar galeria</h2></div></div><p class="field-help">As imagens atuais serão mantidas se você não escolher novas. Ao escolher novas fotos, elas substituirão toda a galeria.</p><label class="image-upload-card image-upload-card-large" for="destinationImages"><i class="fa-solid fa-images"></i><strong>Escolher novas fotos</strong><small>A primeira será a nova capa · JPG, PNG ou WEBP</small><input id="destinationImages" name="imagens[]" type="file" accept="image/jpeg,image/png,image/webp" multiple></label><div class="selected-images selected-images-large" id="selectedImages"><?php foreach ($imagens as $imagem): ?><div class="selected-image"><img src="<?= asset($imagem) ?>" alt="Imagem atual de <?= htmlspecialchars($destino['nome']) ?>"></div><?php endforeach; ?></div></section>
                <div class="destination-create-actions"><a class="button button-light" href="destinos">Cancelar</a><button class="button button-dark" type="submit"><i class="fa-solid fa-floppy-disk"></i> Salvar alterações</button></div>
            </form>
            <aside class="destination-create-preview"><div class="preview-label"><i class="fa-solid fa-eye"></i> Pré-visualização atual</div><article class="destination-preview-card"><div class="destination-preview-cover"><img id="previewCover" src="<?= asset($destino['imagem']) ?>" alt="Capa de <?= htmlspecialchars($destino['nome']) ?>"><span class="preview-gallery-count"><?= count($imagens) ?> fotos</span></div><div class="destination-preview-body"><p class="card-kicker"><?= htmlspecialchars($destino['tipo']) ?> · <?= htmlspecialchars($destino['estado']) ?></p><h2><?= htmlspecialchars($destino['nome']) ?></h2><p class="preview-location"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($destino['localizacao']) ?></p><p><?= htmlspecialchars($destino['resumo']) ?></p><div class="preview-price"><span>A partir de</span><strong>R$ <?= number_format((float) $destino['preco'], 2, ',', '.') ?></strong><small>por noite</small></div><div class="preview-details"><h3>Sobre o destino</h3><p><?= nl2br(htmlspecialchars($destino['descricao'])) ?></p><h3>Destaques</h3><ul><?php foreach (array_filter(array_map('trim', preg_split('/[;\n]+/', (string) $destino['destaque']))) as $item): ?><li><?= htmlspecialchars($item) ?></li><?php endforeach; ?></ul></div></div></article></aside>
        </main>
    </div>
    <script>
        const editImageInput = document.getElementById('destinationImages');
        const editCover = document.getElementById('previewCover');
        editImageInput.addEventListener('change', () => { const file = editImageInput.files[0]; if (file) editCover.src = URL.createObjectURL(file); });
    </script>
</body>
</html>
