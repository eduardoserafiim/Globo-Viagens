<?php
require_once __DIR__ . '/../../private_html/models/destinosModel.php';
require_once __DIR__ . '/../../private_html/components/header/header.php';

exigir_autenticacao();
$pagina_painel = 'destinos';
$filtro = $_GET['status'] ?? 'ativo';
$permitidos = ['ativo', 'em_uso', 'inativo', 'todos'];
if (!in_array($filtro, $permitidos, true)) {
    $filtro = 'ativo';
}
$destinos = listar_destinos_por_status($filtro === 'todos' ? null : $filtro);
$contagens = [];
foreach (['ativo', 'em_uso', 'inativo'] as $status) {
    $contagens[$status] = count(listar_destinos_por_status($status));
}
$statusNomes = ['ativo' => 'Ativo', 'em_uso' => 'Em uso', 'inativo' => 'Inativo'];
$statusClasses = ['ativo' => 'status-active', 'em_uso' => 'status-in-use', 'inativo' => 'status-inactive'];
?>
<?php  getHeader('Destinos | Painel', 'Gerenciamento de destinos.'); ?>
<body class="dashboard-body">
    <div class="dashboard-layout">
        <?php require_once __DIR__ . '/../../private_html/components/sidebar/sidebar.php'; renderSidebar('destinos'); ?>
        <main class="dashboard-main">
            <header class="dashboard-top page-heading">
                <div>
                    <p class="eyebrow">Catalogo de experiencias</p>
                    <h1>Destinos</h1>
                    <p class="page-intro">Os destinos publicados ficam aqui. Para criar um novo, comece uma experiencia em uma tela própria.</p>
                </div>
                <a class="button button-dark" href="destino-criar"><i class="fa-solid fa-plus"></i> Criar destino</a>
            </header>

            <section class="destination-status-summary">
                <a class="status-summary-card <?= $filtro === 'ativo' ? 'selected' : '' ?>" href="destinos?status=ativo"><span class="summary-icon status-active"><i class="fa-solid fa-check"></i></span><span><small>Publicados</small><strong><?= $contagens['ativo'] ?></strong></span></a>
                <a class="status-summary-card <?= $filtro === 'em_uso' ? 'selected' : '' ?>" href="destinos?status=em_uso"><span class="summary-icon status-in-use"><i class="fa-solid fa-bolt"></i></span><span><small>Em uso</small><strong><?= $contagens['em_uso'] ?></strong></span></a>
                <a class="status-summary-card <?= $filtro === 'inativo' ? 'selected' : '' ?>" href="destinos?status=inativo"><span class="summary-icon status-inactive"><i class="fa-solid fa-pause"></i></span><span><small>Inativos</small><strong><?= $contagens['inativo'] ?></strong></span></a>
            </section>

            <section class="panel destination-catalog-panel">
                <div class="panel-heading">
                    <div><p class="eyebrow">Gerenciamento</p><h2><?= $filtro === 'todos' ? 'Todos os destinos' : $statusNomes[$filtro] . 's' ?></h2></div>
                    <div class="catalog-filter"><a class="<?= $filtro === 'todos' ? 'active' : '' ?>" href="destinos?status=todos">Todos</a><a class="<?= $filtro === 'ativo' ? 'active' : '' ?>" href="destinos?status=ativo">Ativos</a><a class="<?= $filtro === 'em_uso' ? 'active' : '' ?>" href="destinos?status=em_uso">Em uso</a><a class="<?= $filtro === 'inativo' ? 'active' : '' ?>" href="destinos?status=inativo">Inativos</a></div>
                </div>
                <div class="admin-destination-list">
                    <?php if ($destinos === []): ?>
                        <div class="empty-state"><i class="fa-solid fa-map-location-dot"></i><h3>Nenhum destino nesta categoria</h3><p>Crie um destino novo ou altere o status de outro item.</p><a class="button button-dark" href="destino-criar">Criar destino</a></div>
                    <?php endif; ?>
                    <?php foreach ($destinos as $destino): $status = $destino['status'] ?? ((int) $destino['ativo'] === 1 ? 'ativo' : 'inativo'); ?>
                        <article class="admin-destination-row destination-row-rich">
                            <img src="<?= asset($destino['imagem']) ?>" alt="<?= htmlspecialchars($destino['nome']) ?>">
                            <div class="admin-destination-info"><span class="card-kicker"><?= htmlspecialchars($destino['tipo']) ?> · <?= htmlspecialchars($destino['estado']) ?></span><h3><?= htmlspecialchars($destino['nome']) ?></h3><p><?= htmlspecialchars($destino['localizacao']) ?> · A partir de R$ <?= number_format((float) $destino['preco'], 2, ',', '.') ?></p></div>
                            <span class="status <?= $statusClasses[$status] ?>"><?= $statusNomes[$status] ?></span>
                            <div class="destination-row-actions"><a class="icon-action" href="destino?id=<?= (int) $destino['id'] ?>" title="Visualizar destino" aria-label="Visualizar destino"><i class="fa-solid fa-eye"></i></a><a class="icon-action" href="destino-editar?id=<?= (int) $destino['id'] ?>" title="Editar destino" aria-label="Editar destino"><i class="fa-solid fa-pen"></i></a><form method="post" action="/action.php?acao=destino"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>"><input type="hidden" name="acao" value="alterar_status"><input type="hidden" name="id" value="<?= (int) $destino['id'] ?>"><select name="status" aria-label="Alterar status" onchange="this.form.submit()"><option value="ativo" <?= $status === 'ativo' ? 'selected' : '' ?>>Ativo</option><option value="em_uso" <?= $status === 'em_uso' ? 'selected' : '' ?>>Em uso</option><option value="inativo" <?= $status === 'inativo' ? 'selected' : '' ?>>Inativo</select></form></div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
