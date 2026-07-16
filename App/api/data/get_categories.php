<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . "/../../inc/config.php";

$parentId = isset($_GET['parent_id']) ? (int)$_GET['parent_id'] : null;
$depth = isset($_GET['depth']) ? (int)$_GET['depth'] : null;

$params = [];

if ($depth === 0) {

    $sql = "
    SELECT c.id, c.name
    FROM categories c
    WHERE c.depth = 0
    AND EXISTS (
        SELECT 1 FROM categories m
        WHERE m.parent_id = c.id AND m.depth = 1
        AND EXISTS (
            SELECT 1 FROM categories s
            WHERE s.parent_id = m.id AND s.depth = 2
        )
    )
    ORDER BY c.name ASC
    ";

} else {

    $sql = "SELECT id, name FROM categories WHERE 1";

    if ($depth !== null) {
        $sql .= " AND depth = ?";
        $params[] = $depth;
    }

    if ($parentId !== null) {
        $sql .= " AND parent_id = ?";
        $params[] = $parentId;
    }

    $sql .= " ORDER BY name ASC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));