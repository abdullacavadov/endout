<?php

require_once __DIR__ . "/../../inc/config.php";

header("Content-Type: application/json");

if (!isset($_SESSION['customer_id'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$customer_id = $_SESSION['customer_id'];

$tid = (int) ($_POST['tid'] ?? 0);

if (!$tid) {
    echo json_encode(["error" => "Tender ID boşdur"]);
    exit;
}

$title = trim($_POST['title'] ?? '');
$desc = trim($_POST['description'] ?? '');
$budget_min = (float) ($_POST['budget_min'] ?? 0);
$budget_max = (float) ($_POST['budget_max'] ?? 0);
$start_date = trim($_POST['start_date'] ?? '');
$end_date = trim($_POST['end_date'] ?? '');
$currency = trim($_POST['currency'] ?? '');
$contact_number = (int) ($_POST['contact_number'] ?? 0);
$contact_email = trim($_POST['contact_email'] ?? '');
$status = "pending";



if (!$title || !$desc || !$contact_number || !$contact_email || !$start_date || !$end_date) {
    //json_encode(['error' => $title, $desc, $contact_number, $contact_email, $start_date, $end_date]);
    exit(json_encode(['error' => 'Bütün seçimləri doldurun']));
}

if ((!empty($budget_max) || !empty($budget_min)) && $budget_max <= $budget_min) {
    exit(json_encode(['error' => 'Maksimum büdcə minimumdan böyük olmalıdır']));
}



try {

    $pdo->beginTransaction();

    /* tender yoxla */

    $stmt = $pdo->prepare("
        SELECT id
        FROM tenders
        WHERE id=? AND customer_id=?
        LIMIT 1
    ");

    $stmt->execute([$tid, $customer_id]);

    if (!$stmt->fetch()) {
        throw new Exception("Tender tapılmadı");
    }


    function slugify($text)
    {
        $map = [
            'ə' => 'e',
            'ş' => 's',
            'ç' => 'c',
            'ü' => 'u',
            'ö' => 'o',
            'ğ' => 'g',
            'ı' => 'i'
        ];

        $text = mb_strtolower($text, 'UTF-8');
        $text = strtr($text, $map);
        $text = preg_replace('/[^a-z0-9]+/u', '-', $text);

        return trim($text, '-');
    }

    $slug = slugify($title);

    /* update tender */

    $stmt = $pdo->prepare("
        UPDATE tenders
        SET
        title=?,
        description=?,
        slug=?,
        contact_number=?,
        contact_email=?,
        currency=?,
        budget_min=?,
        budget_max=?,
        start_date=?,
        end_date=?,
        status=?
        WHERE id=? AND customer_id=?
    ");

    $stmt->execute([
        $title,
        $desc,
        $slug,
        $contact_number,
        $contact_email,
        $currency,
        $budget_min,
        $budget_max,
        $start_date,
        $end_date,
        $status,
        $tid,
        $customer_id
    ]);

  
   

    $pdo->commit();

    echo json_encode([
        'success' => true
    ]);

} catch (Exception $e) {

    $pdo->rollBack();

    echo json_encode([
        "error" => $e->getMessage()
    ]);
}