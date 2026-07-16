<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . "/../../inc/config.php";
require_once __DIR__ . "/../_helpers.php";

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/* ---------------- AUTH ---------------- */
if (!isset($_SESSION['customer_id'])) {
    http_response_code(401);
    exit(json_encode(['error' => 'Unauthorized']));
}

$customer_id = (int) $_SESSION['customer_id'];



/* ---------------- INPUT ---------------- */
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$tender_country_id = (int) ($_POST['country_id'] ?? 0);
$start_date = trim($_POST['start_date'] ?? '');
$end_date = trim($_POST['end_date'] ?? '');
$currency = trim($_POST['currency'] ?? '');
$budget_min = (float) ($_POST['budget_min'] ?? 0);
$budget_max = (float) ($_POST['budget_max'] ?? 0);
$contact_number = trim($_POST['contact_number'] ?? '');
$contact_email = trim($_POST['contact_email'] ?? '');

$countries = $_POST['countries'] ?? [];
$tags = $_POST['tags'] ?? [];

/* ---------------- VALIDATION ---------------- */
if (!$title || !$description || !$start_date || !$end_date || !$contact_number || !$contact_email || !$tender_country_id) {
    exit(json_encode(['error' => 'Bütün seçimləri doldurun']));
}


if ((!empty($budget_max) || !empty($budget_min)) && $budget_max <= $budget_min) {
    exit(json_encode(['error' => 'Maksimum büdcə minimumdan böyük olmalıdır']));
}


/* COUNTRIES CHECK */
if (empty($countries) || !is_array($countries)) {
    exit(json_encode(['error' => 'Ölkə seçilməyib']));
}

/* TAGS CHECK */
if (empty($tags) || !is_array($tags)) {
    exit(json_encode(['error' => 'Etiketlər seçilməyib']));
}


/* ---------------- FK VALIDATION ---------------- */
// countries
$stmt = $pdo->prepare("SELECT id FROM countries WHERE id = ?");
foreach ($countries as $c) {
    $stmt->execute([(int) $c]);
    if (!$stmt->fetch()) {
        exit(json_encode(['error' => 'Yanlış ölkə ID: ' . $c]));
    }
}


/* ---------------- SLUG ---------------- */
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


function generateUniqueSlug(PDO $pdo, string $title): string
{
    $baseSlug = slugify($title);

    $baseSlug = slugify($title);

    if (empty($baseSlug)) {
        $baseSlug = 'tender';
    }

    $slug = $baseSlug;
    $i = 1;

    while (true) {

        $stmt = $pdo->prepare("
            SELECT id
            FROM tenders
            WHERE slug = ?
            LIMIT 1
        ");

        $stmt->execute([$slug]);

        if (!$stmt->fetch()) {
            return $slug;
        }

        $slug = $baseSlug . '-' . $i;
        $i++;
    }
}

$slug = generateUniqueSlug($pdo, $title);


/* ---------------- TRANSACTION ---------------- */
$pdo->beginTransaction();


/* ---------- LISTING INSERT ---------- */
$stmt = $pdo->prepare("
        INSERT INTO tenders
        (customer_id, 
         title, 
         slug, 
         description, 
         currency, 
         budget_min, 
         budget_max, 
         contact_number, 
         contact_email, 
         country_id,
         start_date, 
         end_date, 
         status)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)
    ");

$stmt->execute([
    $customer_id,
    $title,
    $slug,
    $description,
    $currency,
    $budget_min,
    $budget_max,
    $contact_number,
    $contact_email,
    $tender_country_id,
    $start_date,
    $end_date,
    'pending'
]);

$tender_id = $pdo->lastInsertId();

if (!$tender_id) {
    throw new Exception("Tender insert failed");
}



try {

    /* ---------- COUNTRIES ---------- */
    $stmtCountry = $pdo->prepare("
        INSERT INTO tender_countries (tender_id, country_id)
        VALUES (?,?)
    ");

    foreach ($countries as $c) {
        $stmtCountry->execute([$tender_id, (int) $c]);
    }



    /* ---------- TAGS ---------- */

    $stmtFindTagById = $pdo->prepare("
    SELECT id
    FROM tags
    WHERE id = ?
    LIMIT 1
");

    $stmtFindTagByName = $pdo->prepare("
    SELECT id
    FROM tags
    WHERE keyword = ?
    LIMIT 1
");

    $stmtInsertTag = $pdo->prepare("
    INSERT INTO tags (keyword)
    VALUES (?)
");

    $stmtTenderTag = $pdo->prepare("
    INSERT INTO tender_tags (tender_id, tag_id)
    VALUES (?, ?)
");

    foreach ($tags as $t) {

        $tag_id = null;

        /*
        |--------------------------------------------------------------------------
        | CASE 1 — Numeric ID
        |--------------------------------------------------------------------------
        */
        if (is_numeric($t)) {

            $stmtFindTagById->execute([(int) $t]);

            $existing = $stmtFindTagById->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                $tag_id = (int) $existing['id'];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | CASE 2 — Text Tag
        |--------------------------------------------------------------------------
        */ else {

            $tag_name = trim((string) $t);

            if ($tag_name === '') {
                continue;
            }

            // tag varsa
            $stmtFindTagByName->execute([$tag_name]);

            $existing = $stmtFindTagByName->fetch(PDO::FETCH_ASSOC);

            if ($existing) {

                $tag_id = (int) $existing['id'];

            } else {

                // yoxdursa insert
                $stmtInsertTag->execute([$tag_name]);

                $tag_id = (int) $pdo->lastInsertId();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | INSERT RELATION
        |--------------------------------------------------------------------------
        */
        if ($tag_id) {

            $stmtTenderTag->execute([
                $tender_id,
                $tag_id
            ]);
        }
    }

    /* ---------- DONE ---------- */
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'tender_id' => $tender_id
    ]);

} catch (Throwable $e) {


    $pdo->rollBack();

    http_response_code(500);

    echo json_encode([
        'error' => $e->getMessage()
    ]);
}