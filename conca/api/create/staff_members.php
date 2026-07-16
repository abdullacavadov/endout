<?php

require_once("../../inc/config.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Sorğu metodu yanlışdır.');
}


$full_name = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$role = trim($_POST['role'] ?? '');
$password = trim($_POST['password'] ?? '');


if (empty($full_name)) {
    exit('Zəhmət olmasa, tam adı qeyd edin.');
}

if (empty($email)) {
    exit('Zəhmət olmasa, email ünvanını qeyd edin.');
}

if (empty($phone)) {
    exit('Zəhmət olmasa, telefon nömrəsini qeyd edin.');
}

if (empty($role)) {
    exit('Zəhmət olmasa, vəzifəni seçin.');
}
if (empty($password)) {
    exit('Zəhmət olmasa, şifrəni qeyd edin.');
}

// Şifrəni hash-lə
$password_hash = password_hash($password, PASSWORD_DEFAULT);




try {
    // Duplicate yoxlaması
    $check = $pdo->prepare("SELECT email FROM tbl_user WHERE email = ?");
    $check->execute([$email]);

    if ($check->rowCount() > 0) {
        exit('Bu email ünvanı artıq istifadə olunub.');
    }

    // Insert
    $statement = $pdo->prepare("
        INSERT INTO tbl_user (full_name, email, phone, role, password)
        VALUES (?, ?, ?, ?, ?)
    ");

    $statement->execute([$full_name, $email, $phone, $role, $password_hash]);

    //photo upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['photo']['tmp_name'];
        $fileName = $_FILES['photo']['name'];
        $fileSize = $_FILES['photo']['size'];
        $fileType = $_FILES['photo']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // İzin verilen dosya türleri
        $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Dosyayı kaydet
            $uploadFileDir = '../../assets/img/avatar/';
            $hashedFileName = 'avatar_' . $full_name . '.' . $fileExtension;
            $dest_path = $uploadFileDir . $hashedFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                // Dosya başarıyla yüklendi
                $st = $pdo->prepare("UPDATE tbl_user SET photo = ? WHERE email = ?");
                $st->execute([$hashedFileName, $email]);
            } else {
                exit('Fayl yüklənərkən xəta baş verdi.');
            }
        } else {
            exit('Yalnız JPG, JPEG, PNG ve WEBP formatlarında fayllara icazə verilir.');
        }
    } else {
        exit('Foto yüklənmədi və ya xəta baş verdi.');
    }

    echo 'success';

} catch (PDOException $e) {

    // Productionda bunu istifadəçiyə göstərmə
    echo 'Server xətası baş verdi.';

    // Debug üçün:
    echo $e->getMessage();

}