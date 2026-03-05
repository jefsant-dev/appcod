<?php
declare(strict_types=1);

function json_response(int $statusCode, array $payload): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function load_env(string $path): array
{
    if (!is_file($path)) {
        throw new RuntimeException('.env file not found');
    }

    $vars = [];
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        $parts = explode('=', $line, 2);
        if (count($parts) !== 2) {
            continue;
        }

        $key = trim($parts[0]);
        $value = trim($parts[1]);
        $vars[$key] = $value;
    }

    return $vars;
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $env = load_env(__DIR__ . '/../.env');
    $required = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'];
    foreach ($required as $key) {
        if (!isset($env[$key]) || $env[$key] === '') {
            throw new RuntimeException("Missing env key: {$key}");
        }
    }

    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $env['DB_HOST'], $env['DB_NAME']);
    $pdo = new PDO($dsn, $env['DB_USER'], $env['DB_PASS'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}

function read_json_input(): array
{
    $raw = file_get_contents('php://input');
    if (!is_string($raw) || trim($raw) === '') {
        return [];
    }

    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

function client_ip(): array
{
    $forwardedFor = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
    $candidateIps = [];

    if ($forwardedFor !== '') {
        $parts = explode(',', $forwardedFor);
        foreach ($parts as $part) {
            $candidateIps[] = trim($part);
        }
    }

    if (!empty($_SERVER['REMOTE_ADDR'])) {
        $candidateIps[] = trim((string) $_SERVER['REMOTE_ADDR']);
    }

    foreach ($candidateIps as $ip) {
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return ['ip' => $ip, 'forwarded_for' => $forwardedFor];
        }
    }

    return ['ip' => null, 'forwarded_for' => $forwardedFor ?: null];
}

function detect_bot(?string $userAgent): array
{
    $ua = strtolower(trim((string) $userAgent));
    if ($ua === '') {
        return ['is_bot' => 1, 'reason' => 'missing_user_agent'];
    }

    $patterns = [
        'bot', 'spider', 'crawl', 'slurp', 'bingpreview', 'headless', 'phantomjs',
        'selenium', 'puppeteer', 'curl', 'wget', 'python-requests', 'go-http-client',
        'facebookexternalhit', 'telegrambot', 'discordbot', 'linkedinbot'
    ];

    foreach ($patterns as $pattern) {
        if (str_contains($ua, $pattern)) {
            return ['is_bot' => 1, 'reason' => "ua_contains_{$pattern}"];
        }
    }

    return ['is_bot' => 0, 'reason' => null];
}

function str_limit(?string $value, int $maxLen): ?string
{
    if ($value === null) {
        return null;
    }
    $value = trim($value);
    if ($value === '') {
        return null;
    }
    return mb_substr($value, 0, $maxLen);
}
