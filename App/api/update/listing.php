<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . "/../../inc/config.php";

require_login_api($pdo);
csrf_verify($_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null));

$customerId = (int) $_SESSION['customer_id'];
$lid = (int) ($_POST['lid'] ?? 0);

if ($lid <= 0) {
    json_out(['ok' => false, 'error' => 'Listing ID boşdur'], 422);
}

$title = trim((string) ($_POST['title'] ?? ''));
$desc = trim((string) ($_POST['description'] ?? ''));
$price = (float) ($_POST['price'] ?? 0);
$oldPrice = (float) ($_POST['old_price'] ?? 0);
$currency = trim((string) ($_POST['currency'] ?? ''));
$listingType = (int) ($_POST['listing_type'] ?? 0);
$saleModeId = (int) ($_POST['sale_mode_id'] ?? 0);
$categoryId = (int) ($_POST['sub_category'] ?? 0);
$countries = $_POST['countries'] ?? [];

if ($listingType === 3) {
    $oldPrice = 0.00;
}

if ($title === '' || $listingType <= 0 || $saleModeId <= 0 || $categoryId <= 0 || $price <= 0 || $desc === '' || $currency === '') {
    json_out(['ok' => false, 'error' => 'Bütün seçimləri doldurun'], 422);
}

if (($listingType === 1 || $listingType === 2) && $oldPrice <= 0) {
    json_out(['ok' => false, 'error' => 'Köhnə qiymət daxil edilməyib'], 422);
}

if ($listingType === 2 && $saleModeId === 1) {
    json_out(['ok' => false, 'error' => 'Outlet elanlarında yalnız "Pərakəndə" satış rejimi seçilə bilər'], 422);
}

if (($listingType === 1 || $listingType === 2) && $oldPrice <= $price) {
    json_out(['ok' => false, 'error' => 'Köhnə qiymət hazırkı qiymətdən çox olmalıdır'], 422);
}

if (empty($countries) || !is_array($countries)) {
    json_out(['ok' => false, 'error' => 'Ölkə seçilməyib'], 422);
}

$hasImage = false;
if (isset($_FILES['images']['tmp_name']) && is_array($_FILES['images']['tmp_name'])) {
    foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
        if (!empty($tmp) && ($_FILES['images']['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $hasImage = true;
            break;
        }
    }
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM listing_images WHERE listing_id = ?");
$stmt->execute([$lid]);
$existingCount = (int) $stmt->fetchColumn();

if (!$hasImage && $existingCount === 0) {
    json_out(['ok' => false, 'error' => 'Ən azı 1 şəkil əlavə edilməlidir.'], 422);
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        SELECT id
        FROM listings
        WHERE id = ? AND customer_id = ?
        LIMIT 1
        FOR UPDATE
    ");
    $stmt->execute([$lid, $customerId]);

    if (!$stmt->fetch()) {
        throw new RuntimeException('Listing tapılmadı');
    }

    /*
     * Moderasiya/status state-i client tərəfindən dəyişdirilə bilməz.
     * Elan redaktə edildikdə mövcud status qorunur.
     */
    $stmt = $pdo->prepare("
        UPDATE listings
        SET
            title = ?,
            description = ?,
            price = ?,
            old_price = ?,
            currency = ?,
            category_id = ?,
            type_id = ?,
            sale_mode_id = ?
        WHERE id = ? AND customer_id = ?
    ");
    $stmt->execute([
        $title,
        $desc,
        $price,
        $oldPrice,
        $currency,
        $categoryId,
        $listingType,
        $saleModeId,
        $lid,
        $customerId
    ]);

    $pdo->prepare("DELETE FROM listing_countries WHERE listing_id = ?")
        ->execute([$lid]);

    $countryStmt = $pdo->prepare("
        INSERT INTO listing_countries (listing_id, country_id)
        VALUES (?, ?)
    ");

    foreach ($countries as $countryId) {
        $countryId = (int) $countryId;
        if ($countryId > 0) {
            $countryStmt->execute([$lid, $countryId]);
        }
    }

    if (!empty($_FILES['video_path']['name'])) {
        if (($_FILES['video_path']['size'] ?? 0) > 30 * 1024 * 1024) {
            throw new RuntimeException("Video maksimum 30MB ola bilər");
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES['video_path']['tmp_name']);
        finfo_close($finfo);

        $videoExt = [
            'video/mp4' => 'mp4',
            'video/webm' => 'webm',
            'video/quicktime' => 'mov',
        ];

        if (!isset($videoExt[$mime])) {
            throw new RuntimeException("Yalnız MP4, WEBM və MOV video icazəlidir");
        }

        $stmt = $pdo->prepare("
            SELECT id, video_path
            FROM listing_videos
            WHERE listing_id = ?
            LIMIT 1
            FOR UPDATE
        ");
        $stmt->execute([$lid]);
        $oldVideo = $stmt->fetch(PDO::FETCH_ASSOC);

        $videoDir = __DIR__ . "/../../assets/video/uploads/listings/";
        if (!is_dir($videoDir) && !mkdir($videoDir, 0775, true) && !is_dir($videoDir)) {
            throw new RuntimeException('Video qovluğu yaradıla bilmədi.');
        }

        if ($oldVideo) {
            $oldFile = $videoDir . basename((string) $oldVideo['video_path']);
            if (is_file($oldFile)) {
                @unlink($oldFile);
            }

            $pdo->prepare("DELETE FROM listing_videos WHERE id = ?")
                ->execute([(int) $oldVideo['id']]);
        }

        $fileName = bin2hex(random_bytes(16)) . '.' . $videoExt[$mime];

        if (!move_uploaded_file($_FILES['video_path']['tmp_name'], $videoDir . $fileName)) {
            throw new RuntimeException('Video yüklənə bilmədi.');
        }

        $stmt = $pdo->prepare("
            INSERT INTO listing_videos (listing_id, video_path)
            VALUES (?, ?)
        ");
        $stmt->execute([$lid, $fileName]);
    }

    $uploadDir = __DIR__ . "/../../assets/img/uploads/listings/";

    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
        throw new RuntimeException('Şəkil qovluğu yaradıla bilmədi.');
    }

    $existingImages = $_POST['existing_images'] ?? [];
    $existingImages = is_array($existingImages) ? $existingImages : [];

    $stmt = $pdo->prepare("
        SELECT id, image_path
        FROM listing_images
        WHERE listing_id = ?
        FOR UPDATE
    ");
    $stmt->execute([$lid]);
    $oldImages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $oldMap = [];
    foreach ($oldImages as $img) {
        $oldMap[(int) $img['id']] = (string) $img['image_path'];
    }

    $pdo->prepare("DELETE FROM listing_images WHERE listing_id = ?")
        ->execute([$lid]);

    $order = 1;
    $imageInsert = $pdo->prepare("
        INSERT INTO listing_images (listing_id, image_path, sort_order)
        VALUES (?, ?, ?)
    ");

    foreach ($existingImages as $imgId) {
        $imgId = (int) $imgId;

        if (!isset($oldMap[$imgId])) {
            continue;
        }

        $imageInsert->execute([$lid, basename($oldMap[$imgId]), $order++]);
        unset($oldMap[$imgId]);
    }

    $allowedImageMimes = ['image/jpeg', 'image/png', 'image/webp'];

    if (isset($_FILES['images']['tmp_name']) && is_array($_FILES['images']['tmp_name'])) {
        foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
            if (
                empty($tmp)
                || ($_FILES['images']['error'][$i] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK
            ) {
                continue;
            }

            if (($_FILES['images']['size'][$i] ?? 0) > 8 * 1024 * 1024) {
                throw new RuntimeException('Şəkil maksimum 8MB ola bilər.');
            }

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $tmp);
            finfo_close($finfo);

            if (!in_array($mime, $allowedImageMimes, true)) {
                throw new RuntimeException('Yalnız JPEG, PNG və WEBP şəkillər icazəlidir.');
            }

            $fileName = bin2hex(random_bytes(16)) . '.webp';
            make_webp($tmp, $uploadDir . $fileName, 82);

            $imageInsert->execute([$lid, $fileName, $order++]);
        }
    }

    foreach ($oldMap as $unused) {
        $file = $uploadDir . basename($unused);
        if (is_file($file)) {
            @unlink($file);
        }
    }

    $pdo->commit();

    json_out(['ok' => true, 'success' => true]);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log('listing update failed: ' . $e->getMessage());

    json_out([
        'ok' => false,
        'error' => $e instanceof RuntimeException
            ? $e->getMessage()
            : 'Elan yenilənərkən xəta baş verdi.'
    ], 400);
}
