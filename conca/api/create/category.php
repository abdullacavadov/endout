<?php

require_once("../../inc/config.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Sorğu metodu yanlışdır.');
}


$parent_id = intval($_POST['parent_id'] ?? '');
$depth = (int) $_POST['depth'];
$name = trim($_POST['name'] ?? '');


if (empty($name)) {
    exit('Zəhmət olmasa, kateqoriya adını qeyd edin.');
}

if (!isset($_POST['depth']) || $_POST['depth'] === '') {
    exit('Zəhmət olmasa, dərəcəni qeyd edin.');
}

$parent_id = null;

if ($depth > 0) {

    if (!isset($_POST['parent_id']) || $_POST['parent_id'] === '') {
        exit('Zəhmət olmasa, üst kateqoriyanı seçin.');
    }

    $parent_id = (int) $_POST['parent_id'];
}



// slug generator
function slugify($text)
{
    $map = [
        'Ə' => 'E',
        'ə' => 'e',
        'Ö' => 'O',
        'ö' => 'o',
        'Ü' => 'U',
        'ü' => 'u',
        'İ' => 'I',
        'ı' => 'i',
        'Ş' => 'S',
        'ş' => 's',
        'Ç' => 'C',
        'ç' => 'c',
        'Ğ' => 'G',
        'ğ' => 'g'
    ];

    $text = strtr($text, $map);

    $text = mb_strtolower($text, 'UTF-8');

    $text = preg_replace('/[^a-z0-9]+/u', '-', $text);

    $text = trim($text, '-');

    return $text;
}


$slug = slugify($name);

$parent_slug = null;

if ($depth > 0 && !empty($parent_id)) {

    $stmt = $pdo->prepare("
        SELECT slug
        FROM categories
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->execute([$parent_id]);

    $category = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$category) {
        exit('Üst kateqoriya tapılmadı.');
    }

    $parent_slug = slugify($category['slug']);

    $slug_path = $parent_slug . '/' . $slug;

} else {

    $slug_path = $slug;
}



try {
    // Duplicate yoxlaması
    $check = $pdo->prepare("SELECT slug FROM categories WHERE slug = ?");
    $check->execute([$slug]);

    if ($check->rowCount() > 0) {
        // Slug artıq mövcuddur, yeni slug yarat
        $slug .= '-' . time();
    }

    // Insert
    $statement = $pdo->prepare("
        INSERT INTO categories (parent_id, depth, name, slug, slug_path)
        VALUES (?, ?, ?, ?, ?)
    ");

    $statement->execute([$parent_id, $depth, $name, $slug, $slug_path]);

    if ($depth === 0) {

        //photo upload
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['photo']['tmp_name'];
            $fileName = $_FILES['photo']['name'];
            $fileSize = $_FILES['photo']['size'];
            $fileType = $_FILES['photo']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            // İzin verilen dosya türleri
            $allowedfileExtensions = ['png'];

            if (in_array($fileExtension, $allowedfileExtensions)) {
                // Dosyayı kaydet
                $uploadFileDir = dirname(__DIR__, 3) . '/App/assets/img/category/';
                $hashedFileName = $slug . '.' . $fileExtension;
                $dest_path = $uploadFileDir . $hashedFileName;

                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    // Dosya başarıyla yüklendi
                    $st = $pdo->prepare("UPDATE categories SET icon = ? WHERE slug = ?");
                    $st->execute([$hashedFileName, $slug]);
                } else {
                    exit('Fayl yüklənərkən xəta baş verdi.');
                }
            } else {
                exit('Yalnız PNG formatında fayllara icazə verilir.');
            }
        } else {
            exit('Foto yüklənmədi və ya xəta baş verdi.');
        }
    }


    echo 'success';

} catch (PDOException $e) {

    // Productionda bunu istifadəçiyə göstərmə
    echo 'Server xətası baş verdi.';

    // Debug üçün:
    echo $e->getMessage();

}