<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

session_start();

class Database
{
    private static ?PDO $connection = null;

    public static function connect(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        date_default_timezone_set('America/Cuiaba');

        $configRoot = dirname(__DIR__);
        if (is_file($configRoot . '/.env')) {
            Dotenv::createImmutable($configRoot)->safeLoad();
        }

        $host = $_ENV['DB_HOST'] ?? getenv('DB_HOST');
        $port = $_ENV['DB_PORT'] ?? getenv('DB_PORT');
        $name = $_ENV['DB_NAME'] ?? getenv('DB_NAME');
        $user = $_ENV['DB_USER'] ?? getenv('DB_USER');
        $password = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD');

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $host,
            $port,
            $name
        );
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            self::$connection = new PDO($dsn, $user, $password, $options);
            self::$connection->exec("SET time_zone = '-04:00'");
        } catch (PDOException $exception) {
            error_log(sprintf(
                'Falha na conexão com o banco: host=%s port=%s banco=%s usuário=%s mensagem=%s',
                $host,
                $port,
                $name,
                $user,
                $exception->getMessage()
            ));
            throw new RuntimeException('Não foi possível conectar ao banco de dados.', 0, $exception);
        }

        return self::$connection;
    }
}

function database(): PDO
{
    return Database::connect();
}

const DEFAULT_ADMIN_PHOTO = 'images/Logo.png';

function asset(string $path): string
{
    if (str_starts_with($path, 'upload/images/') || str_starts_with($path, 'upload/videos/')) {
        return '../media.php?file=' . rawurlencode(ltrim($path, '/'));
    }

    if (str_starts_with($path, 'images/uploads/')) {
        return '../media.php?file=upload/images/' . rawurlencode(basename($path));
    }

    if (str_starts_with($path, 'videos/uploads/')) {
        return '../media.php?file=upload/videos/' . rawurlencode(basename($path));
    }

    return '../' . ltrim($path, '/');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validar_csrf(?string $token): bool
{
    return is_string($token) && hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function autenticado(): bool
{
    return !empty($_SESSION['usuario_id']) && !empty($_SESSION['usuario_email']);
}

function perfil_admin(): array
{
    return [
        'nome' => $_SESSION['usuario_nome'] ?? 'Usuario',
        'email' => $_SESSION['usuario_email'] ?? '',
        'foto' => $_SESSION['admin_foto'] ?? DEFAULT_ADMIN_PHOTO,
    ];
}

function atualizar_perfil_admin(array $dados): void
{
    $_SESSION['usuario_nome'] = trim((string) ($dados['nome'] ?? 'Usuario'));
    $_SESSION['admin_foto'] = trim((string) ($dados['foto'] ?? DEFAULT_ADMIN_PHOTO));
}

function exigir_autenticacao(): void
{
    if (!autenticado()) {
        header('Location: /Globo-Viagens/public_html/view/login');
        exit;
    }
}
