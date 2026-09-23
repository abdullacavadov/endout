<?php
declare(strict_types=1);

if (!function_exists('admin_h')) {
    function admin_h(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

function admin_current_user(PDO $pdo): ?array
{
    $id = (int) ($_SESSION['user']['id'] ?? 0);
    if ($id <= 0) {
        return null;
    }

    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }

    $stmt = $pdo->prepare("
        SELECT u.id, u.full_name, u.email, u.role, u.status,
               r.role_id, r.role_permissions, r.role_is_active
        FROM tbl_user u
        LEFT JOIN tbl_roles r ON r.role_name = u.role
        WHERE u.id = ?
        LIMIT 1
    ");
    $stmt->execute([$id]);
    $cached = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

    if (!$cached || ($cached['status'] ?? '') !== 'Active' || ($cached['role_is_active'] ?? 'Aktiv') !== 'Aktiv') {
        return null;
    }

    return $cached;
}

function admin_is_super(PDO $pdo): bool
{
    $user = admin_current_user($pdo);
    if (!$user) return false;

    $role = strtolower(trim((string) ($user['role'] ?? '')));
    if (in_array($role, ['super admin', 'superadmin', 'super_admin'], true)) {
        return true;
    }

    $permissions = preg_split('/\s*,\s*/', trim((string) ($user['role_permissions'] ?? '')));
    return in_array('*', $permissions, true);
}

function admin_has_permission(PDO $pdo, string $permission): bool
{
    if (admin_is_super($pdo)) return true;

    $user = admin_current_user($pdo);
    if (!$user) return false;

    $permissions = preg_split('/\s*,\s*/', trim((string) ($user['role_permissions'] ?? '')));
    $permissions = array_map(
        static fn(string $item): string => trim($item, " '\""),
        array_filter($permissions ?: [])
    );

    if (in_array('*', $permissions, true)) return true;
    if (!in_array($permission, $permissions, true)) return false;

    $stmt = $pdo->prepare("
        SELECT 1 FROM tbl_permissions
        WHERE permission = ? AND permission_is_active = 'Aktiv'
        LIMIT 1
    ");
    $stmt->execute([$permission]);

    return (bool) $stmt->fetchColumn();
}

function require_admin_permission(PDO $pdo, string $permission): void
{
    if (!admin_current_user($pdo)) {
        header('Location: signin.php');
        exit;
    }

    if (!admin_has_permission($pdo, $permission)) {
        http_response_code(403);
        exit('Bu əməliyyat üçün icazəniz yoxdur.');
    }
}

function admin_csrf_token(): string
{
    if (empty($_SESSION['admin_csrf'])) {
        $_SESSION['admin_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['admin_csrf'];
}

function admin_verify_csrf(?string $token): void
{
    if (!$token || empty($_SESSION['admin_csrf']) || !hash_equals($_SESSION['admin_csrf'], $token)) {
        http_response_code(419);
        exit('CSRF validation failed.');
    }
}

function admin_audit(
    PDO $pdo,
    string $action,
    ?string $entityType = null,
    ?int $entityId = null,
    ?array $oldValues = null,
    ?array $newValues = null
): void {
    $adminId = (int) ($_SESSION['user']['id'] ?? 0);
    if ($adminId <= 0) return;

    try {
        $stmt = $pdo->prepare("
            INSERT INTO admin_audit_logs
            (admin_id, action, entity_type, entity_id, old_values, new_values, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $adminId,
            $action,
            $entityType,
            $entityId,
            $oldValues === null ? null : json_encode($oldValues, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            $newValues === null ? null : json_encode($newValues, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            $_SERVER['REMOTE_ADDR'] ?? null,
            substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
        ]);
    } catch (Throwable $e) {
        error_log('Admin audit failed: ' . $e->getMessage());
    }
}
