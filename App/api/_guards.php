<?php


function ensure_session(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function require_login(PDO $pdo, string $redirectLogin = './login', string $redirectHome = './home'): void
{
    ensure_session();

    // 1) Login yoxla (session-da customer_id)
    if (empty($_SESSION['customer_id'])) {
        header("Location: {$redirectLogin}");
        exit;
    }

    // 2) Login olubsa DB-də customer var-yox yoxla
    $customerId = (int) $_SESSION['customer_id'];

    $stmt = $pdo->prepare("SELECT id FROM customers WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $customerId]);

    // 3) Sessiondakı customer_id SQL-də yoxdursa home-a at + sessioni təmizlə
    if (!$stmt->fetchColumn()) {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        header("Location: {$redirectHome}");
        exit;
    }
}


function require_guest(): void
{
    ensure_session();

    // istifadəçi login olubsa burdan keçməsin
    if (!empty($_SESSION['customer_id'])) {

        $cust_type = $_SESSION['cust_type'] ?? null;


        if ($cust_type === 'partner') {
            header("Location: ./dashboard");
        } else {
            header("Location: ./dashboard-user");
        }

        exit;
    }
}



function require_role(string $role): void
{
    ensure_session();


    $currentRole = $_SESSION['cust_type'] ?? null;

    // role uyğun deyilsə → düzgün dashboard-a yönləndir
    if ($currentRole !== $role) {

        if ($currentRole === 'partner') {
            header("Location: ./dashboard");
        } else {
            header("Location: ./dashboard-user");
        }

        exit;
    }

    // əgər buraya çatıbsa → hər şey OK (heç nə etmir)
}



function require_login_api(PDO $pdo): void
{
    ensure_session();

    if (empty($_SESSION['customer_id'])) {
        json_out(['ok' => false, 'error' => 'Unauthorized'], 401);
    }

    $stmt = $pdo->prepare("SELECT id FROM customers WHERE id = ? LIMIT 1");
    $stmt->execute([$_SESSION['customer_id']]);

    if (!$stmt->fetchColumn()) {
        $_SESSION = [];
        session_destroy();

        json_out(['ok' => false, 'error' => 'Invalid session'], 401);
    }
}



/*
|--------------------------------------------------------------------------
| OTP verify səhifəsi üçün
|--------------------------------------------------------------------------
*/
function require_unverified(PDO $pdo, string $redirectIfVerified = './dashboard'): void
{
    require_login($pdo);

    $stmt = $pdo->prepare("SELECT phone_verified FROM customers WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => (int) $_SESSION['customer_id']]);
    $row = $stmt->fetch();
    $verify = (int) ($row['phone_verified'] ?? 0);

    if ($verify == 1) {
        header("Location: {$redirectIfVerified}");
        exit;
    }
}



/* ---------------------------------------------------------------------------
| OTP verify səhifəsi üçün (step=1 yoxlamaq üçün)
| ---------------------------------------------------------------------------*/
function require_verify(PDO $pdo, string $redirectIfNot = './login'): void
{
    require_login($pdo);

    $stmt = $pdo->prepare("SELECT phone_verified FROM customers WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => (int) $_SESSION['customer_id']]);
    $row = $stmt->fetch();
    $verify = (int) ($row['phone_verified'] ?? 0);

    if ($verify != 1) {
        header("Location: {$redirectIfNot}");
        exit;
    }
}



/*
|--------------------------------------------------------------------------
| Mail verify səhifəsi üçün
|--------------------------------------------------------------------------
*/
function require_unverified_email(PDO $pdo, string $redirectLogin = './login'): void
{
    ensure_session();

    // 1) Login yoxla
    if (empty($_SESSION['customer_id'])) {
        header("Location: {$redirectLogin}");
        exit;
    }

    $customerId = (int) $_SESSION['customer_id'];

    // 2) email_verified çək
    $stmt = $pdo->prepare("
        SELECT email_verified, type 
        FROM customers 
        WHERE id = :id 
        LIMIT 1
    ");
    $stmt->execute([':id' => $customerId]);

    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    // 3) User DB-də yoxdursa → session təmizlə
    if (!$customer) {
        $_SESSION = [];
        session_destroy();
        header("Location: {$redirectLogin}");
        exit;
    }

    // 4) Əgər artıq verify olunubsa → dashboard-a yönləndir
    if ((int) $customer['email_verified'] === 1) {

        // type-a görə yönləndirmə (əgər fərqli dashboardlar varsa)
        if ($customer['type'] === 'partner') {
            header("Location: ./dashboard");
        } else {
            header("Location: ./dashboard-user");
        }

        exit;
    }

}



//-------------------------------------------------------------------------
// Elan yerləşdirmək üçün aktiv abonementi və limitləri yoxlamaq
//-------------------------------------------------------------------------
function can_create_listing(PDO $pdo, int $customerId): array
{
    // Aktiv abonəliyi tapırıq
    $stmt = $pdo->prepare("
    SELECT
        cs.id,
        cs.package_id,

        (
            SELECT pf.feature_value
            FROM package_features pf
            WHERE
                pf.package_id = cs.package_id
                AND pf.feature_key = 'max_post'
                AND pf.is_active = 1
            LIMIT 1
        ) AS max_post

    FROM customer_subs cs

    INNER JOIN packages p
        ON p.id = cs.package_id

    WHERE
        cs.cust_id = :customer_id
        AND cs.status = 'active'
        AND p.is_active = 1
        AND (
            cs.starts_at IS NULL
            OR cs.starts_at <= NOW()
        )
        AND (
            cs.ends_at IS NULL
            OR cs.ends_at >= NOW()
        )

    ORDER BY cs.ends_at DESC
    LIMIT 1
");

    $stmt->execute([
        ':customer_id' => $customerId
    ]);

    $subscription = $stmt->fetch(PDO::FETCH_ASSOC);


    // Aktiv abonəlik yoxdur
    if (!$subscription) {
        return [
            'allowed' => false,
            'reason' => 'Aktiv abunəliyiniz yoxdur.',
            'remaining' => 0,
            'max_post' => 0
        ];
    }

    $maxPost = (int) ($subscription['max_post'] ?? 0);

    // max_post təyin edilməyibsə
    if ($maxPost <= 0) {
        return [
            'allowed' => false,
            'reason' => 'Paket üçün elan limiti təyin edilməyib.',
            'remaining' => 0,
            'max_post' => 0
        ];
    }

    // Mövcud elan sayı
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM listings
        WHERE customer_id = :customer_id
    ");

    $stmt->execute([
        ':customer_id' => $customerId
    ]);

    $currentListings = (int) $stmt->fetchColumn();

    // Limit dolub
    if ($currentListings >= $maxPost) {
        return [
            'allowed' => false,
            'reason' => 'Elan limitiniz dolub.',
            'remaining' => 0,
            'max_post' => $maxPost
        ];
    }

    return [
        'allowed' => true,
        'reason' => null,
        'remaining' => $maxPost - $currentListings,
        'max_post' => $maxPost
    ];
}




//-------------------------------------------------------------------------
// Tender yerləşdirmək üçün aktiv abonementi və limitləri yoxlamaq
//-------------------------------------------------------------------------
function can_create_tender(PDO $pdo, int $customerId): array
{
    // Aktiv abunəliyi tapırıq
    $stmt = $pdo->prepare("
    SELECT
        cs.id,
        cs.package_id,

        (
            SELECT pf.feature_value
            FROM package_features pf
            WHERE
                pf.package_id = cs.package_id
                AND pf.feature_key = 'max_tender'
                AND pf.is_active = 1
            LIMIT 1
        ) AS max_tender

    FROM customer_subs cs

    INNER JOIN packages p
        ON p.id = cs.package_id

    WHERE
        cs.cust_id = :customer_id
        AND cs.status = 'active'
        AND p.is_active = 1
        AND (
            cs.starts_at IS NULL
            OR cs.starts_at <= NOW()
        )
        AND (
            cs.ends_at IS NULL
            OR cs.ends_at >= NOW()
        )

    ORDER BY cs.ends_at DESC
    LIMIT 1
");

    $stmt->execute([
        ':customer_id' => $customerId
    ]);

    $subscription = $stmt->fetch(PDO::FETCH_ASSOC);


    // Aktiv abonəlik yoxdur
    if (!$subscription) {
        return [
            'allowed' => false,
            'reason' => 'Aktiv abunəliyiniz yoxdur.',
            'remaining' => 0,
            'max_post' => 0
        ];
    }

    $maxTender = (int) ($subscription['max_tender'] ?? 0);

    // max_tender təyin edilməyibsə
    if ($maxTender <= 0) {
        return [
            'allowed' => false,
            'reason' => 'Paket üçün tender limiti təyin edilməyib.',
            'remaining' => 0,
            'max_tender' => 0
        ];
    }

    // Mövcud tender sayı
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM tenders
        WHERE customer_id = :customer_id
    ");

    $stmt->execute([
        ':customer_id' => $customerId
    ]);

    $currentTenders = (int) $stmt->fetchColumn();

    // Limit dolub
    if ($currentTenders >= $maxTender) {
        return [
            'allowed' => false,
            'reason' => 'Tender limitiniz dolub.',
            'remaining' => 0,
            'max_tender' => $maxTender
        ];
    }

    return [
        'allowed' => true,
        'reason' => null,
        'remaining' => $maxTender - $currentTenders,
        'max_tender' => $maxTender
    ];
}