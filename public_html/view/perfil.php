<?php
require_once __DIR__ . '/../../private_html/models/loginModel.php';
require_once __DIR__ . '/../../private_html/components/header/header.php';

exigir_autenticacao();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'salvar_perfil') {
    if (!validar_csrf($_POST['csrf_token'] ?? null)) {
        header('Location: perfil.php?erro=csrf');
        exit;
    }

    $foto = $_SESSION['admin_foto'] ?? DEFAULT_ADMIN_PHOTO;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $extensao = strtolower(pathinfo((string) $_FILES['foto']['name'], PATHINFO_EXTENSION));
        if (in_array($extensao, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            $diretorio = dirname(__DIR__, 2) . '/private_html/upload/images';
            if (!is_dir($diretorio)) {
                mkdir($diretorio, 0775, true);
            }
            $nomeArquivo = 'perfil-' . time() . '-' . bin2hex(random_bytes(5)) . '.' . $extensao;
            $caminho = $diretorio . '/' . $nomeArquivo;
            if (move_uploaded_file((string) $_FILES['foto']['tmp_name'], $caminho)) {
                $foto = 'upload/images/' . $nomeArquivo;
            }
        }
    }

    $usuarioId = (int) ($_SESSION['usuario_id'] ?? 0);
    atualizar_usuario($usuarioId, (string) ($_POST['nome'] ?? ''), $foto);
    atualizar_perfil_admin(['nome' => $_POST['nome'] ?? 'Usuario', 'foto' => $foto]);
    header('Location: perfil.php?perfil=atualizado');
    exit;
}

$pagina_painel = 'perfil';
$perfil = perfil_admin();
$perfilAtualizado = isset($_GET['perfil']);
?>
<?php  getHeader('Meu perfil | Painel', 'Mantenha suas informações de acesso e identificação atualizadas.'); ?>
<body class="dashboard-body">
    <div class="dashboard-layout">
        <?php require_once __DIR__ . '/../../private_html/components/sidebar/sidebar.php'; renderSidebar('perfil'); ?>
        <main class="dashboard-main">
            <header class="dashboard-top page-heading"><div><p class="eyebrow">Conta administrativa</p><h1>Meu perfil</h1><p class="page-intro">Mantenha suas informações de acesso e identificação atualizadas.</p></div></header>

            <section class="profile-page-grid">
                <div class="profile-cover panel">
                    <div class="profile-cover-shape"></div>
                    <div class="profile-page-avatar">
                        <img src="<?= asset($perfil['foto']) ?>" alt="Foto de <?= htmlspecialchars($perfil['nome']) ?>">
                    </div>
                    <div class="profile-summary">
                        <p class="eyebrow">Administrador</p>
                        <h2><?= htmlspecialchars($perfil['nome']) ?></h2>
                        <p><?= htmlspecialchars($perfil['email']) ?></p>
                    </div>
                </div>

                <div class="panel profile-edit-panel">
                    <div class="panel-heading">
                        <div><p class="eyebrow">Informacoes pessoais</p><h2>Editar perfil</h2></div>
                        <i class="fa-solid fa-user-pen panel-heading-icon"></i>
                    </div>
                    <?php if ($perfilAtualizado): ?>
                        <p class="profile-success"><i class="fa-solid fa-circle-check"></i> Perfil atualizado com sucesso.</p>
                    <?php endif; ?>
                    <form class="dashboard-form profile-form" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="acao" value="salvar_perfil">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                        <label>Nome completo
                            <input type="text" name="nome" value="<?= htmlspecialchars($perfil['nome']) ?>" required>
                        </label>
                        <label>E-mail
                            <input type="email" value="<?= htmlspecialchars($perfil['email']) ?>" readonly aria-readonly="true">
                        </label>
                        <div class="profile-photo-picker">
                            <span class="field-heading">Foto de perfil <small>JPG, PNG ou WEBP</small></span>
                            <label class="image-upload-card profile-upload-card" for="profileImage">
                                <i class="fa-solid fa-camera"></i>
                                <strong>Escolher uma foto</strong>
                                <small>Clique para selecionar uma imagem</small>
                                <input id="profileImage" name="foto" type="file" accept="image/jpeg,image/png,image/webp">
                            </label>
                        </div>
                        <button class="button button-dark" type="submit"><i class="fa-solid fa-floppy-disk"></i> Salvar alteracoes</button>
                    </form>
                </div>
            </section>

        </main>
    </div>
    <script>
        const profileImage = document.getElementById('profileImage');
        const profileAvatars = document.querySelectorAll('.profile-page-avatar img, .sidebar-user-card img');
        profileImage.addEventListener('change', () => {
            const file = profileImage.files[0];
            if (!file) return;
            const previewUrl = URL.createObjectURL(file);
            profileAvatars.forEach((avatar) => { avatar.src = previewUrl; });
        });
    </script>
</body>
</html>
