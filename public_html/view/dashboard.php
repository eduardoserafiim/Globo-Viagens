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

$estatisticas = estatisticas_destinos();
$statusDestinos = $estatisticas['status'];

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
            <header class="dashboard-top" id="resumo">
                <div>
                    <p class="eyebrow">Painel administrativo</p>
                    <h1>Bom dia, <?= $_SESSION['nome'] ?>.</h1>
                </div>
                <a class="button button-dark" href="https://web.whatsapp.com/" target="_blank" rel="noopener"><i class="fa-solid fa-arrow-up-right-from-square"></i> Abrir WhatsApp </a>
            </header>

            <?php if ($statusDestinos['total'] === 0): ?>
                <div class="empty-state dashboard-empty-state">
                    <i class="fa-solid fa-map-location-dot"></i>
                    <h2>Não foram cadastrados destinos ainda</h2>
                    <p>Cadastre o primeiro destino para começar a montar o catálogo.</p>
                    <a class="button button-dark" href="destino-criar"><i class="fa-solid fa-plus"></i> Cadastrar destino</a>
                </div>
            <?php endif; ?>

            <section class="dashboard-charts">
                <div class="panel chart-panel">
                    <div class="panel-heading">
                        <div>
                            <p class="eyebrow">Evolução do catálogo</p>
                            <h2>Destinos cadastrados por Dia</h2>
                        </div>
                    </div>
                    <div class="chart-canvas-wrap"><canvas id="cadastrosPorDiaChart" aria-label="Destinos cadastrados por dia"></canvas></div>
                </div>

                <div class="panel chart-panel">
                    <div class="panel-heading">
                        <div>
                            <p class="eyebrow">Status do catálogo</p>
                            <h2>Ativos e Inativos</h2>
                        </div>
                    </div>
                    <div class="chart-canvas-wrap chart-canvas-doughnut"><canvas id="statusDestinosChart" aria-label="Destinos ativos e inativos"></canvas></div>
                </div>
            </section>
            <section class="dashboard-charts dashboard-charts-secondary">
                <div class="panel chart-panel">
                    <div class="panel-heading">
                        <div>
                            <p class="eyebrow">Histórico</p>
                            <h2>Cadastros por Mês</h2>
                        </div>
                    </div>
                    <div class="chart-canvas-wrap">
                        <canvas id="cadastrosPorMesChart" aria-label="Destinos cadastrados por mes"></canvas>
                    </div>
                </div>
                <div class="panel chart-panel">
                    <div class="panel-heading">
                        <div>
                            <p class="eyebrow">Distribuição geográfica</p>
                            <h2>Destinos por Estado</h2>
                        </div>
                    </div>
                    <div class="chart-canvas-wrap">
                        <canvas id="destinosPorEstadoChart" aria-label="Destinos por estado"></canvas>
                    </div>
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
        const porEstado = <?= json_encode(array_values(array_filter($estatisticas['por_estado'], static fn (array $item): bool => trim($item['estado']) !== '')), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
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
