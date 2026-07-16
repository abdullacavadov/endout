<?php
declare(strict_types=1);

require_once __DIR__ . "/../_db.php";
require_once __DIR__ . "/../_helpers.php";

header("Content-Type: application/json; charset=utf-8");

$code = strtoupper(trim((string)($_GET['country_code'] ?? '')));
if ($code === '' || strlen($code) !== 2) {
  json_out(['ok' => false, 'error' => 'country_code invalid'], 422);
}

try {
  $st = $pdo->prepare("
    SELECT ct.id, ct.name
    FROM cities ct
    JOIN countries c ON c.id = ct.country_id
    WHERE c.iso2 = ?
    ORDER BY ct.name ASC
  ");
  $st->execute([$code]);
  $data = $st->fetchAll(PDO::FETCH_ASSOC);

  json_out(['ok' => true, 'data' => $data]);
} catch (Throwable $e) {
  json_out(['ok' => false, 'error' => 'Cities load error'], 500);
}
