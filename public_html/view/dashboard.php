<?php
require_once __DIR__ . '/../../private_html/models/destinosModel.php';
require_once __DIR__ . '/../../private_html/components/header/header.php';

if (isset($_GET['sair'])) {
    $_SESSION = [];
    session_destroy();
    header('Location: login');
    exit;
}

exigir_autenticacao();
$destinos = listar_destinos(false);
$perfil = perfil_admin();
$planos = [
    ['nome' => 'Bronze', 'preco' => '29,90', 'classe' => 'bronze', 'descricao' => 'O essencial para viajar com conforto.'],
    ['nome' => 'Prata', 'preco' => '39,90', 'classe' => 'prata', 'descricao' => 'Mais estrutura para aproveitar sem pressa.'],
    ['nome' => 'Ouro', 'preco' => '49,90', 'classe' => 'ouro', 'descricao' => 'A experiencia completa da Globo Viagens.'],
];

$totalDestinos = count($destinos);
$totalPlanos = count($planos);
$estatisticas = estatisticas_destinos();
$statusDestinos = $estatisticas['status'];
$faturamento = 0;

$cadastrosPorDia = [];
for ($indice = 13; $indice >= 0; $indice--) {
    $periodo = (new DateTimeImmutable('today'))->modify('-' . $indice . ' days')->format('Y-m-d');
    $cadastrosPorDia[$periodo] = 0;
}
foreach ($estatisticas['por_dia'] as $linha) {
    if (array_key_exists($linha['periodo'], $cadastrosPorDia)) {
        $cadastrosPorDia[$linha['periodo']] = $linha['total'];
    }
}

$cadastrosPorMes = [];
for ($indice = 5; $indice >= 0; $indice--) {
    $periodo = (new DateTimeImmutable('first day of this month'))->modify('-' . $indice . ' months')->format('Y-m');
    $cadastrosPorMes[$periodo] = 0;
}
foreach ($estatisticas['por_mes'] as $linha) {
    if (array_key_exists($linha['periodo'], $cadastrosPorMes)) {
        $cadastrosPorMes[$linha['periodo']] = $linha['total'];
    }
}
?>
<?php  getHeader('Painel', 'Area administrativa da Globo Viagens.'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<body class="dashboard-body overview-page">
    <div class="dashboard-layout">
        <?php require_once __DIR__ . '/../../private_html/components/sidebar/sidebar.php'; renderSidebar('dashboard'); ?>

        <main class="dashboard-main">
            <header class="dashboard-top" id="resumo"><div><p class="eyebrow">Painel administrativo</p><h1>Bom dia, equipe.</h1></div><a class="button button-dark" href="https://api.whatsapp.com/send?phone=67992027942" target="_blank" rel="noopener">Novo atendimento <span>+</span></a></header>

            <div class="metric-grid">
                <div class="metric-card">
                    <span>Destinos cadastrados</span>
                    <strong><?= $statusDestinos['total'] ?></strong>
                    <small class="positive"><?= $statusDestinos['ativos'] ?> ativos no catalogo</small>
                </div>
                <div class="metric-card">
                    <span>Destinos ativos</span>
                    <strong><?= $statusDestinos['ativos'] ?></strong>
                    <small class="positive"><?= $statusDestinos['inativos'] ?> inativos</small>
                </div>
                <div class="metric-card metric-card-coral">
                    <span>Planos disponiveis</span>
                    <strong><?= $totalPlanos ?></strong>
                    <small>Oferta ativa no site</small>
                </div>
            </div>

            <?php if ($statusDestinos['total'] === 0): ?>
                <div class="empty-state dashboard-empty-state">
                    <i class="fa-solid fa-map-location-dot"></i>
                    <h2>Nao foram cadastrados destinos ainda</h2>
                    <p>Cadastre o primeiro destino para começar a montar o catalogo.</p>
                    <a class="button button-dark" href="destino-criar"><i class="fa-solid fa-plus"></i> Cadastrar destino</a>
                </div>
            <?php endif; ?>

            <section class="dashboard-charts">
                <div class="panel chart-panel">
                    <div class="panel-heading">
                        <div>
                            <p class="eyebrow">Evolucao do catalogo</p>
                            <h2>Destinos cadastrados por dia</h2>
                        </div>
                    </div>
                    <div class="chart-canvas-wrap"><canvas id="cadastrosPorDiaChart" aria-label="Destinos cadastrados por dia"></canvas></div>
                </div>

                <div class="panel chart-panel">
                    <div class="panel-heading">
                        <div>
                            <p class="eyebrow">Status do catalogo</p>
                            <h2>Ativos e inativos</h2>
                        </div>
                    </div>
                    <div class="chart-canvas-wrap chart-canvas-doughnut"><canvas id="statusDestinosChart" aria-label="Destinos ativos e inativos"></canvas></div>
                </div>
            </section>

            <section class="dashboard-charts dashboard-charts-secondary">
                <div class="panel chart-panel">
                    <div class="panel-heading"><div><p class="eyebrow">Historico</p><h2>Cadastros por mes</h2></div></div>
                    <div class="chart-canvas-wrap"><canvas id="cadastrosPorMesChart" aria-label="Destinos cadastrados por mes"></canvas></div>
                </div>
                <div class="panel chart-panel">
                    <div class="panel-heading"><div><p class="eyebrow">Distribuicao geografica</p><h2>Destinos por estado</h2></div></div>
                    <div class="chart-canvas-wrap"><canvas id="destinosPorEstadoChart" aria-label="Destinos por estado"></canvas></div>
                </div>
            </section>

            <section class="dashboard-content" id="modulo-destinos">
                <div class="panel panel-large">
                    <div class="panel-heading">
                        <div>
                            <p class="eyebrow">Catalogo</p>
                            <h2>Destinos cadastrados</h2>
                        </div>
                        <a href="index#destinos">Ver site ↗</a>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Destino</th>
                                <th>Categoria</th>
                                <th>Preco inicial</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($destinos as $destino): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($destino['nome']) ?>, <?= htmlspecialchars($destino['estado']) ?></strong>
                                        <small><?= htmlspecialchars($destino['tipo']) ?></small>
                                    </td>
                                    <td>Hospedagem</td>
                                    <td>R$ <?= number_format((float) $destino['preco'], 2, ',', '.') ?></td>
                                    <td><span class="status">Ativo</span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <aside class="panel destination-form-panel" id="novo-destino">
                    <div class="panel-heading">
                        <div>
                            <p class="eyebrow">Adicionar</p>
                            <h2>Novo destino</h2>
                        </div>
                    </div>

                        <form class="dashboard-form" method="post" action="/action.php?acao=destino" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">

                        <label>Nome
                            <input name="nome" type="text" required>
                        </label>

                        <div class="dashboard-form-row">
                            <label>Estado
                                <input name="estado" type="text" maxlength="2" placeholder="SP" required>
                            </label>
                            <label>Tipo
                                <input name="tipo" type="text" placeholder="Apartamento mobiliado" required>
                            </label>
                        </div>

                        <div class="dashboard-form-row">
                            <label>Preco
                                <input name="preco" type="number" step="0.01" min="0" required>
                            </label>
                            <label>Imagem
                                <input name="imagem" type="file" accept=".jpg,.jpeg,.png,.webp">
                            </label>
                        </div>

                        <label>URL da imagem (opcional)
                            <input name="imagem_url" type="text" placeholder="images/Nome-do-lugar/arquivo.jpg">
                        </label>

                        <label>Resumo
                            <textarea name="resumo" maxlength="220" required></textarea>
                        </label>

                        <label>Descricao completa
                            <textarea name="descricao" required></textarea>
                        </label>

                        <label>Localizacao
                            <input name="localizacao" type="text" placeholder="Fortaleza - CE" required>
                        </label>

                        <label>Destaque
                            <textarea name="destaque" placeholder="Piscina, Wi-Fi, vista para o mar..."></textarea>
                        </label>

                        <label>Galeria
                            <input name="galeria" type="text" placeholder="images/xxx.jpg, images/yyy.jpg">
                        </label>

                        <button class="button button-dark" type="submit">Salvar destino</button>
                    </form>
                </aside>
            </section>

            <section class="dashboard-section" id="modulo-planos">
                <div class="panel">
                    <div class="panel-heading">
                        <div>
                            <p class="eyebrow">Planos</p>
                            <h2>Gerenciar ofertas</h2>
                        </div>
                    </div>

                    <div class="dashboard-plans-grid">
                        <?php foreach ($planos as $plano): ?>
                            <article class="plan-card plan-card-<?= htmlspecialchars($plano['classe']) ?>">
                                <p class="card-kicker">Plano <?= htmlspecialchars($plano['nome']) ?></p>
                                <h3>R$ <?= htmlspecialchars($plano['preco']) ?></h3>
                                <p><?= htmlspecialchars($plano['descricao']) ?></p>
                                <div class="plan-actions">
                                    <button type="button" class="inline-button">Editar</button>
                                    <button type="button" class="inline-button inline-button-muted">Excluir</button>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="panel" id="novo-plano">
                    <div class="panel-heading">
                        <div>
                            <p class="eyebrow">Adicionar</p>
                            <h2>Novo plano</h2>
                        </div>
                    </div>

                    <form class="dashboard-form">
                        <label>Nome do plano
                            <input type="text" placeholder="Plano Premium">
                        </label>

                        <div class="dashboard-form-row">
                            <label>Valor
                                <input type="number" step="0.01" min="0" placeholder="79.90">
                            </label>
                            <label>Categoria
                                <select>
                                    <option>Bronze</option>
                                    <option>Prata</option>
                                    <option>Ouro</option>
                                </select>
                            </label>
                        </div>

                        <label>Descricao
                            <textarea placeholder="Descreva os beneficios do plano"></textarea>
                        </label>

                        <button class="button button-dark" type="submit">Salvar plano</button>
                    </form>
                </div>
            </section>

        </main>
    </div>
    <script>
        const chartDefaults = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { displayColors: false }
            },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#718087' } },
                y: { beginAtZero: true, precision: 0, grid: { color: '#e3e7e5' }, ticks: { color: '#718087', precision: 0 } }
            }
        };

        const porDia = <?= json_encode($cadastrosPorDia, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
        const porMes = <?= json_encode($cadastrosPorMes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
        const porEstado = <?= json_encode($estatisticas['por_estado'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
        const statusDestinos = <?= json_encode($statusDestinos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
        const formatarDia = (periodo) => periodo.slice(8) + '/' + periodo.slice(5, 7);
        const formatarMes = (periodo) => periodo.slice(5) + '/' + periodo.slice(2, 4);

        new Chart(document.getElementById('cadastrosPorDiaChart'), {
            type: 'line',
            data: {
                labels: Object.keys(porDia).map(formatarDia),
                datasets: [{
                    label: 'Destinos cadastrados',
                    data: Object.values(porDia),
                    borderColor: '#ef785f',
                    backgroundColor: 'rgba(239, 120, 95, .14)',
                    fill: true,
                    tension: .35,
                    pointRadius: 3,
                    pointBackgroundColor: '#ef785f'
                }]
            },
            options: chartDefaults
        });

        new Chart(document.getElementById('statusDestinosChart'), {
            type: 'doughnut',
            data: {
                labels: ['Ativos', 'Inativos'],
                datasets: [{
                    data: [statusDestinos.ativos, statusDestinos.inativos],
                    backgroundColor: ['#3d8d68', '#ef785f'],
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: { ...chartDefaults, cutout: '70%' }
        });

        new Chart(document.getElementById('cadastrosPorMesChart'), {
            type: 'bar',
            data: {
                labels: Object.keys(porMes).map(formatarMes),
                datasets: [{
                    label: 'Novos destinos',
                    data: Object.values(porMes),
                    backgroundColor: '#7bb8cf',
                    borderRadius: 5,
                    maxBarThickness: 34
                }]
            },
            options: chartDefaults
        });

        new Chart(document.getElementById('destinosPorEstadoChart'), {
            type: 'bar',
            data: {
                labels: porEstado.map((item) => item.estado),
                datasets: [{
                    label: 'Destinos',
                    data: porEstado.map((item) => item.total),
                    backgroundColor: '#f2bd82',
                    borderRadius: 5,
                    maxBarThickness: 34
                }]
            },
            options: { ...chartDefaults, indexAxis: 'y' }
        });
    </script>
</body>
</html>
