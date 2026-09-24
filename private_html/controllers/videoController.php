<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/videosModel.php';
exigir_autenticacao();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validar_csrf($_POST['csrf_token'] ?? null)) {
    header('Location: /Globo-Viagens/public_html/view/videos?erro=csrf');
    exit;
}

if (($_POST['acao'] ?? '') === 'excluir') {
    excluir_video_relato((int) ($_POST['id'] ?? 0));
    header('Location: /Globo-Viagens/public_html/view/videos?sucesso=excluido');
    exit;
}

function salvar_video_relato(array $arquivo): string
{
    if (($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || empty($arquivo['name'])) {
        return '';
    }

    if ((int) ($arquivo['size'] ?? 0) > 100 * 1024 * 1024) {
        return '';
    }

    $extensao = strtolower(pathinfo((string) $arquivo['name'], PATHINFO_EXTENSION));
    if (!in_array($extensao, ['mp4', 'webm', 'ogg'], true)) {
        return '';
    }

    $diretorio = dirname(__DIR__, 2) . '/videos/uploads';
    if (!is_dir($diretorio)) {
        mkdir($diretorio, 0775, true);
    }

    $nomeArquivo = 'relato-' . time() . '-' . bin2hex(random_bytes(6)) . '.' . $extensao;
    if (!move_uploaded_file((string) $arquivo['tmp_name'], $diretorio . '/' . $nomeArquivo)) {
        return '';
    }

    return 'videos/uploads/' . $nomeArquivo;
}

$video = salvar_video_relato($_FILES['video'] ?? []);
$titulo = trim((string) ($_POST['titulo'] ?? ''));
$autor = trim((string) ($_POST['autor'] ?? ''));
$destino = trim((string) ($_POST['destino'] ?? ''));
$descricao = trim((string) ($_POST['descricao'] ?? ''));

if ($video === '' || $titulo === '' || $autor === '') {
    header('Location: /Globo-Viagens/public_html/view/videos?erro=validacao');
    exit;
}

criar_video_relato([
    'titulo' => $titulo,
    'autor' => $autor,
    'destino' => $destino,
    'descricao' => $descricao,
    'url' => $video,
]);
header('Location: /Globo-Viagens/public_html/view/videos?sucesso=criado');
exit;