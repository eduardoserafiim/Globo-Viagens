<?php
$pagina_painel = $pagina_painel ?? 'dashboard';
$perfil = perfil_admin();
?>
<aside class="dashboard-sidebar">
    <div class="sidebar-top">
        <a href="dashboard" class="dashboard-brand">
            <img src="<?= asset('images/Logo.png') ?>" alt="Globo Viagens">
        </a>
        <p class="sidebar-kicker">Painel de controle</p>

        <nav class="sidebar-nav" aria-label="Menu do painel">
            <a class="sidebar-link <?= $pagina_painel === 'dashboard' ? 'active' : '' ?>" href="dashboard">
                <i class="fa-solid fa-chart-line"></i><span>Visao geral</span>
            </a>

            <details class="sidebar-group">
                <summary class="sidebar-link <?= in_array($pagina_painel, ['destinos', 'novo-destino'], true) ? 'active' : '' ?>">
                    <i class="fa-solid fa-map-location-dot"></i><span>Destinos</span><i class="fa-solid fa-chevron-down sidebar-chevron"></i>
                </summary>
                <div class="sidebar-submenu">
                    <a class="<?= $pagina_painel === 'destinos' ? 'active' : '' ?>" href="destinos"><i class="fa-solid fa-list"></i> Gerenciar destinos</a>
                    <a class="<?= $pagina_painel === 'novo-destino' ? 'active' : '' ?>" href="destino-criar"><i class="fa-solid fa-plus"></i> Criar destino</a>
                </div>
            </details>

            <details class="sidebar-group">
                <summary class="sidebar-link <?= $pagina_painel === 'planos' ? 'active' : '' ?>">
                    <i class="fa-solid fa-file-contract"></i><span>Planos</span><i class="fa-solid fa-chevron-down sidebar-chevron"></i>
                </summary>
                <div class="sidebar-submenu">
                    <a class="<?= $pagina_painel === 'planos' ? 'active' : '' ?>" href="planos"><i class="fa-solid fa-layer-group"></i> Gerenciar planos</a>
                    <a href="planos#novo-plano"><i class="fa-solid fa-circle-plus"></i> Criar plano</a>
                </div>
            </details>

            <a class="sidebar-link <?= $pagina_painel === 'videos' ? 'active' : '' ?>" href="videos"><i class="fa-solid fa-film"></i><span>Relatos em video</span></a>
            <a class="sidebar-link <?= $pagina_painel === 'perfil' ? 'active' : '' ?>" href="perfil"><i class="fa-solid fa-user-gear"></i><span>Meu perfil</span></a>
        </nav>
    </div>

    <div class="sidebar-bottom">
        <div class="sidebar-user-card">
            <img src="<?= asset($perfil['foto']) ?>" alt="Foto de <?= htmlspecialchars($perfil['nome']) ?>">
            <div class="sidebar-user-copy">
                <strong><?= htmlspecialchars($perfil['nome']) ?></strong>
                <small><?= htmlspecialchars($perfil['email']) ?></small>
            </div>
            <a class="sidebar-user-edit" href="perfil" aria-label="Editar perfil" title="Editar perfil"><i class="fa-solid fa-pen"></i></a>
        </div>
        <a class="sidebar-link sidebar-link-muted" href="index"><i class="fa-solid fa-arrow-up-right-from-square"></i><span>Ver site</span></a>
        <a class="sidebar-link sidebar-link-muted" href="dashboard?sair=1"><i class="fa-solid fa-right-from-bracket"></i><span>Sair</span></a>
    </div>
</aside>
