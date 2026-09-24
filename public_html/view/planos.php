<?php
require_once __DIR__ . '/../../private_html/config/database.php';
require_once __DIR__ . '/../../private_html/models/planosModel.php';
require_once __DIR__ . '/../../private_html/components/header/header.php';
 require_once __DIR__ . '/../../private_html/components/sidebar/sidebar.php'; 

exigir_autenticacao();
$pagina_painel = 'planos';
$planos = listar_planos();
$planoEditar = isset($_GET['editar']) ? buscar_plano_por_id((int) $_GET['editar']) : null;
$sucesso = $_GET['sucesso'] ?? null;
$erro = $_GET['erro'] ?? null;
?>
<?php  getHeader('Planos | Painel', 'Gerenciamento de planos.'); ?>
<body class="dashboard-body">
    <div class="dashboard-layout">
        <?php renderSidebar('planos'); ?>
        <main class="dashboard-main">
            <header class="dashboard-top page-heading"><div><p class="eyebrow">Acomodacao do seu jeito</p><h1>Planos</h1><p class="page-intro">Organize as ofertas e encontre o formato ideal para cada viagem.</p></div><a class="button button-dark" href="#novo-plano"><i class="fa-solid fa-plus"></i> Novo plano</a></header>

            <?php if ($sucesso === 'criado' || $sucesso === 'atualizado' || $sucesso === 'excluido'): ?><p class="profile-success"><i class="fa-solid fa-circle-check"></i> Plano <?= $sucesso === 'criado' ? 'criado' : ($sucesso === 'atualizado' ? 'atualizado' : 'excluido') ?> com sucesso.</p><?php elseif ($erro): ?><p class="form-feedback form-feedback-error"><i class="fa-solid fa-circle-exclamation"></i> Não foi possível salvar o plano. Revise os campos.</p><?php endif; ?>

            <section class="plans-admin-grid" id="modulo-planos">
                <div class="panel">
                        <div class="panel-heading"><div><p class="eyebrow">Ofertas publicadas</p><h2>Seus planos</h2></div><span class="panel-count"><?= count($planos) ?> ofertas</span></div>
                    <div class="admin-plan-list">
                        <?php foreach ($planos as $plano): ?>
                            <article class="admin-plan-card plan-card-<?= htmlspecialchars($plano['classe']) ?>">
                                <div class="admin-plan-icon"><i class="fa-solid <?= htmlspecialchars($plano['icone']) ?>"></i></div>
                                <div><span class="card-kicker">Plano <?= htmlspecialchars($plano['nome']) ?></span><h3>R$ <?= number_format((float) $plano['preco'], 2, ',', '.') ?></h3><p><?= htmlspecialchars($plano['descricao']) ?></p></div>
                                <div class="plan-actions"><a class="inline-button" href="planos?editar=<?= (int) $plano['id'] ?>#novo-plano" title="Editar plano"><i class="fa-solid fa-pen"></i><span>Editar</span></a><form method="post" action="../../private_html/controllers/planosController.php"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>"><input type="hidden" name="acao" value="excluir"><input type="hidden" name="id" value="<?= (int) $plano['id'] ?>"><button class="inline-button inline-button-muted" type="submit" title="Excluir plano"><i class="fa-solid fa-trash"></i><span>Excluir</span></button></form></div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>

                <aside class="panel" id="novo-plano">
                    <div class="panel-heading"><div><p class="eyebrow"><?= $planoEditar ? 'Atualizar catalogo' : 'Adicionar ao catalogo' ?></p><h2><?= $planoEditar ? 'Editar plano' : 'Novo plano' ?></h2></div><i class="fa-solid fa-layer-group panel-heading-icon"></i></div>
                    <form class="dashboard-form" method="post" action="../../private_html/controllers/planosController.php">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>"><input type="hidden" name="acao" value="salvar"><input type="hidden" name="id" value="<?= (int) ($planoEditar['id'] ?? 0) ?>">
                        <label>Nome do plano<input type="text" name="nome" value="<?= htmlspecialchars($planoEditar['nome'] ?? '') ?>" placeholder="Plano Premium" required></label>
                        <div class="dashboard-form-row"><label>Valor<input type="number" name="preco" value="<?= $planoEditar ? htmlspecialchars((string) $planoEditar['preco']) : '' ?>" step="0.01" min="0" placeholder="79.90" required></label><label>Categoria<select name="categoria"><option <?= ($planoEditar['categoria'] ?? '') === 'Bronze' ? 'selected' : '' ?>>Bronze</option><option <?= ($planoEditar['categoria'] ?? '') === 'Prata' ? 'selected' : '' ?>>Prata</option><option <?= ($planoEditar['categoria'] ?? '') === 'Ouro' ? 'selected' : '' ?>>Ouro</option></select></label></div>
                        <label>Descricao<textarea name="descricao" placeholder="Descreva os beneficios do plano" required><?= htmlspecialchars($planoEditar['descricao'] ?? '') ?></textarea></label>
                        <button class="button button-dark" type="submit"><i class="fa-solid fa-floppy-disk"></i> <?= $planoEditar ? 'Atualizar plano' : 'Salvar plano' ?></button>
                    </form>
                </aside>
            </section>
        </main>
    </div>
</body>
</html>
