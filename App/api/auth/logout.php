<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . "/../../inc/config.php";
require_login_api($pdo);


try {

    // Remember token sil
    if (!empty($_COOKIE['remember_token']) && isset($pdo)) {

        $tokenHash = hash('sha256', (string) $_COOKIE['remember_token']);

        $stmt = $pdo->prepare("
            DELETE FROM customer_remember_tokens
            WHERE token_hash = :th
        ");
        $stmt->execute([':th' => $tokenHash]);

        setcookie(
            "remember_token",
            "",
            [
                'expires' => time() - 3600,
                'path' => '/',
            ]
        );
    }

    // Session təmizlə
    $_SESSION = [];
    session_destroy();
    json_out([
        'ok' => true,
    ]);

} catch (Throwable $e) {

    json_out([
        'ok' => false,
        'error' => 'Logout zamanı xəta baş verdi.'
    ], 500);
}
