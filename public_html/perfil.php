<?php
require_once __DIR__ . '/../private_html/config/bootstrap.php';
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
            $diretorio = dirname(__DIR__) . '/images/uploads';
            if (!is_dir($diretorio)) {
                mkdir($diretorio, 0775, true);
            }
            $nomeArquivo = 'perfil-' . time() . '-' . bin2hex(random_bytes(5)) . '.' . $extensao;
            $caminho = $diretorio . '/' . $nomeArquivo;
            if (move_uploaded_file((string) $_FILES['foto']['tmp_name'], $caminho)) {
                $foto = 'images/uploads/' . $nomeArquivo;
            }
        }
    }

    atualizar_perfil_admin([
        'nome' => $_POST['nome'] ?? ADMIN_NAME,
        'email' => $_POST['email'] ?? ADMIN_EMAIL,
        'foto' => $foto,
    ]);
    header('Location: perfil.php?perfil=atualizado');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'alterar_senha') {
    if (!validar_csrf($_POST['csrf_token'] ?? null)) {
        header('Location: perfil.php?erro=csrf');
        exit;
    }

    $senhaAtual = (string) ($_POST['senha_atual'] ?? '');
    $novaSenha = (string) ($_POST['nova_senha'] ?? '');
    $confirmacao = (string) ($_POST['confirmar_senha'] ?? '');

    if (!password_verify($senhaAtual, senha_admin_hash())) {
        header('Location: perfil.php?erro=senha-atual');
        exit;
    }
    if (strlen($novaSenha) < 8 || $novaSenha !== $confirmacao) {
        header('Location: perfil.php?erro=senha-nova');
        exit;
    }

    atualizar_senha_admin($novaSenha);
    header('Location: perfil.php?senha=atualizada');
    exit;
}

$pagina_painel = 'perfil';
$perfil = perfil_admin();
$perfilAtualizado = isset($_GET['perfil']);
$senhaAtualizada = isset($_GET['senha']);
$erroSenha = $_GET['erro'] ?? null;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu perfil | Painel Globo Viagens</title>
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
                    <p class="eyebrow">Conta administrativa</p>
                    <h1>Meu perfil</h1>
                    <p class="page-intro">Mantenha suas informações de acesso e identificação atualizadas.</p>
                </div>
            </header>

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

            <section class="password-page-section panel">
                <div class="panel-heading">
                    <div><p class="eyebrow">Seguranca</p><h2>Alterar senha</h2></div>
                    <i class="fa-solid fa-shield-halved panel-heading-icon"></i>
                </div>
                <?php if ($senhaAtualizada): ?><p class="profile-success"><i class="fa-solid fa-circle-check"></i> Senha alterada com sucesso.</p><?php endif; ?>
                <?php if ($erroSenha === 'senha-atual'): ?><p class="form-feedback form-feedback-error"><i class="fa-solid fa-circle-exclamation"></i> A senha atual está incorreta.</p><?php elseif ($erroSenha === 'senha-nova'): ?><p class="form-feedback form-feedback-error"><i class="fa-solid fa-circle-exclamation"></i> A nova senha precisa ter pelo menos 8 caracteres e coincidir com a confirmação.</p><?php endif; ?>
                <form class="dashboard-form password-form" method="post">
                    <input type="hidden" name="acao" value="alterar_senha">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                    <label>Senha atual<input type="password" name="senha_atual" autocomplete="current-password" required></label>
                    <div class="dashboard-form-row"><label>Nova senha<input type="password" name="nova_senha" autocomplete="new-password" minlength="8" required></label><label>Confirmar nova senha<input type="password" name="confirmar_senha" autocomplete="new-password" minlength="8" required></label></div>
                    <button class="button button-dark" type="submit"><i class="fa-solid fa-key"></i> Atualizar senha</button>
                </form>
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
