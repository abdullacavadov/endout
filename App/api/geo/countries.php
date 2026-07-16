<?php
declare(strict_types=1);

require_once __DIR__ . "/../_db.php";
require_once __DIR__ . "/../_helpers.php";

header("Content-Type: application/json; charset=utf-8");

try {
  $st = $pdo->query("SELECT iso2 AS code, name, country_audience FROM countries ORDER BY name ASC");
  $data = $st->fetchAll(PDO::FETCH_ASSOC);
  json_out(['ok' => true, 'data' => $data]);
} catch (Throwable $e) {
  json_out(['ok' => false, 'error' => 'Countries load error'], 500);
}
