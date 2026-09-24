<?php
declare(strict_types=1);

$acoes = [
    'login' => 'loginController.php',
    'destino' => 'destinoController.php',
    'planos' => 'planosController.php',
    'videos' => 'videoController.php',
];

$acao = (string) ($_GET['acao'] ?? '');
if (!isset($acoes[$acao])) {
    http_response_code(404);
    exit;
}

require dirname(__DIR__) . '/private_html/controllers/' . $acoes[$acao];
