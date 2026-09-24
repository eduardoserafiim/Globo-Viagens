<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/planosModel.php';
exigir_autenticacao();

$redirecionar = static function (string $query = ''): never {
    header('Location: /view/planos' . $query);
    exit;
};

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validar_csrf($_POST['csrf_token'] ?? null)) {
    $redirecionar('?erro=csrf');
}

$acao = (string) ($_POST['acao'] ?? 'salvar');
$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT) ?: 0;

if ($acao === 'excluir') {
    excluir_plano($id);
    $redirecionar('?sucesso=excluido');
}

$nome = trim((string) ($_POST['nome'] ?? ''));
$preco = filter_var($_POST['preco'] ?? null, FILTER_VALIDATE_FLOAT);
$categoria = trim((string) ($_POST['categoria'] ?? ''));
$descricao = trim((string) ($_POST['descricao'] ?? ''));
$classes = ['Bronze' => 'bronze', 'Prata' => 'prata', 'Ouro' => 'ouro'];
$icones = ['Bronze' => 'fa-compass', 'Prata' => 'fa-sun', 'Ouro' => 'fa-crown'];

if ($nome === '' || $preco === false || $preco < 0 || $categoria === '' || $descricao === '' || !isset($classes[$categoria])) {
    $redirecionar('?erro=validacao');
}

$dados = [
    'nome' => $nome,
    'preco' => $preco,
    'categoria' => $categoria,
    'classe' => $classes[$categoria],
    'descricao' => $descricao,
    'icone' => $icones[$categoria],
];

if ($id > 0) {
    atualizar_plano($id, $dados);
    $redirecionar('?sucesso=atualizado');
}

criar_plano($dados);
$redirecionar('?sucesso=criado');
