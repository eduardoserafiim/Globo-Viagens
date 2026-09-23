<?php
require_once __DIR__ . '/../private_html/config/bootstrap.php';
exigir_autenticacao();
$pagina_painel = 'planos';
$planos = [
    ['nome' => 'Bronze', 'preco' => '29,90', 'classe' => 'bronze', 'descricao' => 'O essencial para viajar com conforto.', 'icone' => 'fa-compass'],
    ['nome' => 'Prata', 'preco' => '39,90', 'classe' => 'prata', 'descricao' => 'Mais estrutura para aproveitar sem pressa.', 'icone' => 'fa-sun'],
    ['nome' => 'Ouro', 'preco' => '49,90', 'classe' => 'ouro', 'descricao' => 'A experiencia completa da Globo Viagens.', 'icone' => 'fa-crown'],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planos | Painel Globo Viagens</title>
    <link rel="icon" href="<?= asset('images/Logo.png') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../private_html/styles/styles.css">
</head>
<body class="dashboard-body">
    <div class="dashboard-layout">
        <?php require __DIR__ . '/_sidebar.php'; ?>
        <main class="dashboard-main">
            <header class="dashboard-top page-heading">
                <div>
                    <p class="eyebrow">Acomodacao do seu jeito</p>
                    <h1>Planos</h1>
                    <p class="page-intro">Organize as ofertas e encontre o formato ideal para cada viagem.</p>
                </div>
                <a class="button button-dark" href="#novo-plano"><i class="fa-solid fa-plus"></i> Novo plano</a>
            </header>

            <section class="plans-admin-grid" id="modulo-planos">
                <div class="panel">
                    <div class="panel-heading"><div><p class="eyebrow">Ofertas publicadas</p><h2>Seus planos</h2></div><span class="panel-count">3 ofertas</span></div>
                    <div class="admin-plan-list">
                        <?php foreach ($planos as $plano): ?>
                            <article class="admin-plan-card plan-card-<?= htmlspecialchars($plano['classe']) ?>">
                                <div class="admin-plan-icon"><i class="fa-solid <?= htmlspecialchars($plano['icone']) ?>"></i></div>
                                <div><span class="card-kicker">Plano <?= htmlspecialchars($plano['nome']) ?></span><h3>R$ <?= htmlspecialchars($plano['preco']) ?></h3><p><?= htmlspecialchars($plano['descricao']) ?></p></div>
                                <div class="plan-actions"><button type="button" class="inline-button" title="Editar plano"><i class="fa-solid fa-pen"></i><span>Editar</span></button><button type="button" class="inline-button inline-button-muted" title="Excluir plano"><i class="fa-solid fa-trash"></i><span>Excluir</span></button></div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>

                <aside class="panel" id="novo-plano">
                    <div class="panel-heading"><div><p class="eyebrow">Adicionar ao catalogo</p><h2>Novo plano</h2></div><i class="fa-solid fa-layer-group panel-heading-icon"></i></div>
                    <form class="dashboard-form" method="post" action="planos.php">
                        <label>Nome do plano<input type="text" name="nome" placeholder="Plano Premium"></label>
                        <div class="dashboard-form-row"><label>Valor<input type="number" name="preco" step="0.01" min="0" placeholder="79.90"></label><label>Categoria<select name="categoria"><option>Bronze</option><option>Prata</option><option>Ouro</option></select></label></div>
                        <label>Descricao<textarea name="descricao" placeholder="Descreva os beneficios do plano"></textarea></label>
                        <button class="button button-dark" type="submit"><i class="fa-solid fa-floppy-disk"></i> Salvar plano</button>
                    </form>
                </aside>
            </section>
        </main>
    </div>
</body>
</html>
