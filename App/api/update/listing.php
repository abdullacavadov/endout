<?php

require_once __DIR__ . "/../../inc/config.php";

header("Content-Type: application/json");

if (!isset($_SESSION['customer_id'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$customer_id = $_SESSION['customer_id'];

$lid = (int) ($_POST['lid'] ?? 0);

if (!$lid) {
    echo json_encode(["error" => "Listing ID boşdur"]);
    exit;
}

$title = trim($_POST['title'] ?? '');
$desc = trim($_POST['description'] ?? '');
$price = (float) ($_POST['price'] ?? 0);
$old_price = (float) ($_POST['old_price'] ?? 0);
$currency = trim($_POST['currency'] ?? '');
$listing_type = (int) ($_POST['listing_type'] ?? 0);
$sale_mode_id = (int) ($_POST['sale_mode_id'] ?? 0);

$category_id = (int) ($_POST['sub_category'] ?? 0);

$status = trim($_POST['status'] ?? '');

$countries = $_POST['countries'] ?? [];


if ($listing_type === 3) {
    $old_price = 0.00;
}

if (!$title || !$listing_type || !$sale_mode_id || !$category_id || !$price || !$desc || !$currency) {
    exit(json_encode(['error' => 'Bütün seçimləri doldurun']));
}

if (($listing_type == 1 || $listing_type == 2) && !$old_price) {
    exit(json_encode(['error' => 'Köhnə qiymət daxil edilməyib']));
}

if ($listing_type == 2 && $sale_mode_id == 1) {
    exit(json_encode(['error' => 'Outlet elanlarında yalnız "Pərakəndə" satış rejimi seçilə bilər']));
}

if (($listing_type == 1 || $listing_type == 2) && $old_price <= $price) {
    exit(json_encode(['error' => 'Köhnə qiymət hazırkı qiymətdən çox olmalıdır']));
}

$hasImage = false;

if (isset($_FILES['images']) && is_array($_FILES['images']['tmp_name'])) {
    foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
        if (!empty($tmp) && $_FILES['images']['error'][$i] === 0) {
            $hasImage = true;
            break;
        }
    }
}

/*
Əgər mövcud şəkil də yoxdursa və yeni də yoxdursa → ERROR
*/
$stmt = $pdo->prepare("SELECT COUNT(*) FROM listing_images WHERE listing_id=?");
$stmt->execute([$lid]);
$existingCount = $stmt->fetchColumn();

if (!$hasImage && $existingCount == 0) {
    echo json_encode(["error" => "Ən azı 1 şəkil əlavə edilməlidir."]);
    exit;
}

if (empty($countries) || !is_array($countries)) {
    echo json_encode(["error" => "Ölkə seçilməyib"]);
    exit;
}

try {

    $pdo->beginTransaction();

    /* listing yoxla */

    $stmt = $pdo->prepare("
        SELECT id
        FROM listings
        WHERE id=? AND customer_id=?
        LIMIT 1
    ");

    $stmt->execute([$lid, $customer_id]);

    if (!$stmt->fetch()) {
        throw new Exception("Listing tapılmadı");
    }

    /* update listing */

    $stmt = $pdo->prepare("
        UPDATE listings
        SET
        title=?,
        description=?,
        price=?,
        old_price=?,
        currency=?,
        category_id=?,
        type_id=?,
        sale_mode_id=?,
        status=?
        WHERE id=? AND customer_id=?
    ");

    $stmt->execute([
        $title,
        $desc,
        $price,
        $old_price,
        $currency,
        $category_id,
        $listing_type,
        $sale_mode_id,
        $status,
        $lid,
        $customer_id
    ]);

    /* countries reset */

    $pdo->prepare("DELETE FROM listing_countries WHERE listing_id=?")
        ->execute([$lid]);

    if ($countries) {

        $stmt = $pdo->prepare("
            INSERT INTO listing_countries
            (listing_id,country_id)
            VALUES (?,?)
        ");

        foreach ($countries as $cid) {
            $stmt->execute([$lid, (int) $cid]);
        }
    }

    /* =========================
       VIDEO UPDATE
    ========================= */

    if (!empty($_FILES['video_path']['name'])) {

        if ($_FILES['video_path']['size'] > 30 * 1024 * 1024) {
            throw new Exception("Video maksimum 30MB ola bilər");
        }

        $allowed = ['video/mp4', 'video/webm', 'video/quicktime'];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES['video_path']['tmp_name']);

        if (!in_array($mime, $allowed)) {
            throw new Exception("Yalnız MP4 və WEBM video icazəlidir");
        }

        $stmt = $pdo->prepare("
            SELECT id, video_path
            FROM listing_videos
            WHERE listing_id=?
            LIMIT 1
        ");

        $stmt->execute([$lid]);
        $oldVideo = $stmt->fetch(PDO::FETCH_ASSOC);

        /* köhnə video sil */

        if ($oldVideo) {

            $oldFile = "../../assets/video/uploads/listings/" . $oldVideo['video_path'];

            if (file_exists($oldFile)) {
                unlink($oldFile);
            }

            $pdo->prepare("DELETE FROM listing_videos WHERE id=?")
                ->execute([$oldVideo['id']]);
        }

        /* yeni video upload */

        $ext = pathinfo($_FILES['video_path']['name'], PATHINFO_EXTENSION);

        $fileName = uniqid() . "." . $ext;

        move_uploaded_file(
            $_FILES['video_path']['tmp_name'],
            "../../assets/video/uploads/listings/" . $fileName
        );

        $stmt = $pdo->prepare("
            INSERT INTO listing_videos
            (listing_id,video_path)
            VALUES (?,?)
        ");

        $stmt->execute([$lid, $fileName]);
    }

    /* =========================
       IMAGE UPLOAD
    ========================= */

    /* =========================
   IMAGE SYNC (SORTABLE)
========================= */

    $uploadDir = "../../assets/img/uploads/listings/";

    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $allowed = ['image/jpeg', 'image/png', 'image/webp'];

    $existingImages = $_POST['existing_images'] ?? [];

    /* köhnə şəkilləri götür */
    $stmt = $pdo->prepare("
    SELECT id, image_path
    FROM listing_images
    WHERE listing_id=?
");
    $stmt->execute([$lid]);
    $oldImages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    /* map */
    $oldMap = [];
    foreach ($oldImages as $img) {
        $oldMap[$img['id']] = $img['image_path'];
    }

    /* HAMISINI SİL */
    $pdo->prepare("DELETE FROM listing_images WHERE listing_id=?")
        ->execute([$lid]);

    $order = 1;

    /* =========================
       1. EXISTING (sıra ilə)
    ========================= */

    foreach ($existingImages as $imgId) {

        if (!isset($oldMap[$imgId]))
            continue;

        $stmt = $pdo->prepare("
        INSERT INTO listing_images
        (listing_id,image_path,sort_order)
        VALUES (?,?,?)
    ");

        $stmt->execute([
            $lid,
            $oldMap[$imgId],
            $order++
        ]);

        unset($oldMap[$imgId]);
    }

    /* =========================
       2. NEW IMAGES (sıra ilə)
    ========================= */

    if (isset($_FILES['images'])) {

        foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {

            if (empty($tmp) || $_FILES['images']['error'][$i] !== 0)
                continue;

            $mime = mime_content_type($tmp);
            if (!in_array($mime, $allowed))
                continue;

            $fileName = uniqid() . ".webp";

            move_uploaded_file($tmp, $uploadDir . $fileName);

            $stmt = $pdo->prepare("
            INSERT INTO listing_images
            (listing_id,image_path,sort_order)
            VALUES (?,?,?)
        ");

            $stmt->execute([
                $lid,
                $fileName,
                $order++
            ]);
        }
    }

    /* =========================
       3. İSTİFADƏ OLUNMAYAN KÖHNƏ FAYLLARI SİL
    ========================= */

    foreach ($oldMap as $unused) {
        $file = $uploadDir . $unused;
        if (file_exists($file))
            unlink($file);
    }

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