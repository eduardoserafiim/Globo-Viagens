<?php
function renderSidebar(string $paginaPainel = 'dashboard'): void
{
	$perfil = perfil_admin();
	?>
	<button class="sidebar-toggle" type="button" aria-label="Abrir menu" title="Abrir menu" aria-controls="dashboardSidebar" aria-expanded="false"><i class="fa-solid fa-bars"></i></button>
	<div class="sidebar-backdrop" data-sidebar-backdrop></div>
	<aside class="dashboard-sidebar" id="dashboardSidebar">
		<div class="sidebar-top">
			<a href="dashboard" class="dashboard-brand"><img src="<?= asset('images/Logo.png') ?>" alt="Globo Viagens"></a>
			<p class="sidebar-kicker">Painel de controle</p>
			<div class="sidebar-flight" aria-hidden="true"><i class="fa-solid fa-plane"></i></div>
			<nav class="sidebar-nav" aria-label="Menu do painel">
				<a class="sidebar-link <?= $paginaPainel === 'dashboard' ? 'active' : '' ?>" href="dashboard">
					<i class="fa-solid fa-chart-line"></i>
					<span>Visao geral</span>
				</a>
				<details class="sidebar-group">
					<summary class="sidebar-link <?= in_array($paginaPainel, ['destinos', 'novo-destino'], true) ? 'active' : '' ?>">
						<i class="fa-solid fa-map-location-dot"></i>
						<span>Destinos</span>
						<i class="fa-solid fa-chevron-down sidebar-chevron"></i>
					</summary>
					<div class="sidebar-submenu">
						<a class="<?= $paginaPainel === 'destinos' ? 'active' : '' ?>" href="destinos">
							<i class="fa-solid fa-list"></i> Gerenciar destinos
						</a>
						<a class="<?= $paginaPainel === 'novo-destino' ? 'active' : '' ?>" href="destino-criar">
							<i class="fa-solid fa-plus"></i> Criar destino
						</a>
					</div>
				</details>
				<a class="sidebar-link <?= $paginaPainel === 'videos' ? 'active' : '' ?>" href="videos">
					<i class="fa-solid fa-film"></i>
					<span>Relatos em video</span>
				</a>
				<a class="sidebar-link <?= $paginaPainel === 'perfil' ? 'active' : '' ?>" href="perfil">
					<i class="fa-solid fa-user-gear"></i>
					<span>Meu perfil</span>
				</a>
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
	<script>
	(() => {
		const button = document.querySelector('.sidebar-toggle');
		const sidebar = document.getElementById('dashboardSidebar');
		const backdrop = document.querySelector('[data-sidebar-backdrop]');
		if (!button || !sidebar || !backdrop) return;

		const setOpen = (open) => {
			sidebar.classList.toggle('is-open', open);
			backdrop.classList.toggle('is-visible', open);
			button.classList.toggle('is-hidden', open);
			document.body.classList.toggle('sidebar-open', open);
			button.setAttribute('aria-expanded', String(open));
		};

		button.addEventListener('click', () => setOpen(!sidebar.classList.contains('is-open')));
		backdrop.addEventListener('click', () => setOpen(false));
		sidebar.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => setOpen(false)));
		document.addEventListener('keydown', (event) => {
			if (event.key === 'Escape') setOpen(false);
		});
	})();
	</script>
	<?php
}
