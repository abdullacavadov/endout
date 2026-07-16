<?php

require_once("../../inc/config.php");

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode([
        'status' => false
    ]);
    exit;
}

$pdo->beginTransaction();

try {

    $stmt = $pdo->prepare("
        UPDATE packages
        SET sort_order=?
        WHERE id=?
    ");

    foreach ($data as $item) {

        $stmt->execute([
            $item['sort_order'],
            $item['id']
        ]);

    }

    $pdo->commit();

    echo json_encode([
        'status' => true
    ]);

} catch (Exception $e) {

    $pdo->rollBack();

    echo json_encode([
        'status' => false,
        'message' => $e->getMessage()
    ]);

}