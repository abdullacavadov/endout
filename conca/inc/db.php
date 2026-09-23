<?php
declare(strict_types=1);

$dbhost = getenv('DB_HOST') ?: '127.0.0.1';
$dbname = getenv('DB_DATABASE') ?: 'endout';
$dbuser = getenv('DB_USERNAME') ?: 'root';
$dbpass = getenv('DB_PASSWORD') ?: '';
$dbport = (int) (getenv('DB_PORT') ?: 3306);

try {
    $pdo = new PDO(
        "mysql:host={$dbhost};port={$dbport};dbname={$dbname};charset=utf8mb4",
        $dbuser,
        $dbpass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $exception) {
    error_log('Conca database connection failed: ' . $exception->getMessage());
    http_response_code(500);
    exit('Database connection failed.');
}
