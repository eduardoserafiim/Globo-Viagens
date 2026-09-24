<?php
declare(strict_types=1);

$arquivo = (string) ($_GET['file'] ?? '');
if (!preg_match('#^(?:upload/(images|videos)|javascript)/[A-Za-z0-9._-]+$#', $arquivo)) {
    http_response_code(404);
    exit;
}

$caminho = dirname(__DIR__) . '/private_html/' . $arquivo;
if (!is_file($caminho)) {
    http_response_code(404);
    exit;
}

$tipo = str_starts_with($arquivo, 'javascript/')
    ? 'application/javascript; charset=UTF-8'
    : (mime_content_type($caminho) ?: 'application/octet-stream');
header('Content-Type: ' . $tipo);
header('Content-Length: ' . (string) filesize($caminho));
header('Cache-Control: public, max-age=31536000, immutable');
readfile($caminho);
