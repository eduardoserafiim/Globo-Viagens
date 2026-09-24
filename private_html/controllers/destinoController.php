<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/destinosModel.php';
exigir_autenticacao();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validar_csrf($_POST['csrf_token'] ?? null)) {
    header('Location: /view/destino-criar?erro=csrf');
    exit;
}

if (($_POST['acao'] ?? '') === 'alterar_status') {
    $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
    $status = (string) ($_POST['status'] ?? '');
    atualizar_status_destino((int) $id, $status);
    header('Location: /view/destinos?status=atualizado');
    exit;
}

function salvar_imagem_destino(array $arquivo): string
{
    if (!isset($arquivo['name']) || $arquivo['error'] !== UPLOAD_ERR_OK || empty($arquivo['name'])) {
        return '';
    }

    $extensao = strtolower(pathinfo((string) $arquivo['name'], PATHINFO_EXTENSION));
    if (!in_array($extensao, ['jpg', 'jpeg', 'png', 'webp'], true)) {
        return '';
    }

    $diretorio = dirname(__DIR__) . '/upload/images';
    if (!is_dir($diretorio)) {
        mkdir($diretorio, 0775, true);
    }

    $nomeArquivo = 'destino-' . time() . '-' . bin2hex(random_bytes(6)) . '.' . $extensao;
    $caminhoCompleto = $diretorio . '/' . $nomeArquivo;

    if (!move_uploaded_file((string) $arquivo['tmp_name'], $caminhoCompleto)) {
        return '';
    }

    return 'upload/images/' . $nomeArquivo;
}

function salvar_imagens_destino(array $arquivos): array
{
    $imagens = [];
    $total = count($arquivos['name'] ?? []);

    for ($indice = 0; $indice < $total; $indice++) {
        $arquivo = [
            'name' => $arquivos['name'][$indice] ?? '',
            'type' => $arquivos['type'][$indice] ?? '',
            'tmp_name' => $arquivos['tmp_name'][$indice] ?? '',
            'error' => $arquivos['error'][$indice] ?? UPLOAD_ERR_NO_FILE,
            'size' => $arquivos['size'][$indice] ?? 0,
        ];
        $imagem = salvar_imagem_destino($arquivo);
        if ($imagem !== '') {
            $imagens[] = $imagem;
        }
    }

    return $imagens;
}

if (($_POST['acao'] ?? '') === 'editar') {
    $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
    $destinoAtual = $id ? buscar_destino_por_id((int) $id) : null;
    if (!$destinoAtual) {
        header('Location: /view/destinos?erro=destino');
        exit;
    }

    $nome = trim((string) ($_POST['nome'] ?? ''));
    $estado = strtoupper(trim((string) ($_POST['estado'] ?? '')));
    $tipo = trim((string) ($_POST['tipo'] ?? ''));
    $resumo = trim((string) ($_POST['resumo'] ?? ''));
    $descricao = trim((string) ($_POST['descricao'] ?? ''));
    $localizacao = trim((string) ($_POST['localizacao'] ?? ''));
    $destaque = trim((string) ($_POST['destaque'] ?? ''));
    $status = (string) ($_POST['status'] ?? 'ativo');
    $preco = filter_var($_POST['preco'] ?? null, FILTER_VALIDATE_FLOAT);
    $imagens = isset($_FILES['imagens']) && is_array($_FILES['imagens']) ? salvar_imagens_destino($_FILES['imagens']) : [];
    $imagem = $imagens[0] ?? (string) $destinoAtual['imagem'];
    $galeria = $imagens !== []
        ? implode(',', array_slice($imagens, 1))
        : (string) $destinoAtual['galeria'];

    if ($nome === '' || strlen($estado) !== 2 || $tipo === '' || $resumo === '' || $descricao === '' || $localizacao === '' || !in_array($status, ['ativo', 'em_uso', 'inativo'], true) || $preco === false || $preco < 0) {
        header('Location: /view/destino-editar?id=' . (int) $id . '&erro=validacao');
        exit;
    }

    atualizar_destino((int) $id, [
        'nome' => $nome,
        'estado' => $estado,
        'tipo' => $tipo,
        'preco' => $preco,
        'imagem' => $imagem,
        'resumo' => $resumo,
        'descricao' => $descricao,
        'localizacao' => $localizacao,
        'destaque' => $destaque,
        'galeria' => $galeria,
        'status' => $status,
        'ativo' => $status === 'ativo' ? 1 : 0,
    ]);
    header('Location: /view/destinos?status=atualizado');
    exit;
}

$nome = trim((string) ($_POST['nome'] ?? ''));
$estado = strtoupper(trim((string) ($_POST['estado'] ?? '')));
$tipo = trim((string) ($_POST['tipo'] ?? ''));
$resumo = trim((string) ($_POST['resumo'] ?? ''));
$descricao = trim((string) ($_POST['descricao'] ?? ''));
$localizacao = trim((string) ($_POST['localizacao'] ?? ''));
$destaque = trim((string) ($_POST['destaque'] ?? ''));
$status = (string) ($_POST['status'] ?? 'ativo');
$imagens = [];
if (isset($_FILES['imagens']) && is_array($_FILES['imagens'])) {
    $imagens = salvar_imagens_destino($_FILES['imagens']);
}
$imagem = $imagens[0] ?? '';
$galeria = implode(',', array_slice($imagens, 1));

$preco = filter_var($_POST['preco'] ?? null, FILTER_VALIDATE_FLOAT);

if (
    $nome === '' ||
    strlen($estado) !== 2 ||
    $tipo === '' ||
    $imagem === '' ||
    $resumo === '' ||
    $descricao === '' ||
    $localizacao === '' ||
    !in_array($status, ['ativo', 'em_uso', 'inativo'], true) ||
    $preco === false ||
    $preco < 0 ||
    !preg_match('/^[a-zA-Z0-9_\/-]+\.(jpg|jpeg|png|webp)$/i', $imagem)
) {
    header('Location: /view/destino-criar?erro=validacao');
    exit;
}

criar_destino([
    'nome' => $nome,
    'estado' => $estado,
    'tipo' => $tipo,
    'preco' => $preco,
    'imagem' => $imagem,
    'resumo' => $resumo,
    'descricao' => $descricao,
    'localizacao' => $localizacao,
    'destaque' => $destaque,
    'galeria' => $galeria,
    'status' => $status,
    'ativo' => $status === 'ativo' ? 1 : 0,
]);
header('Location: /view/destinos?sucesso=destino');
exit;