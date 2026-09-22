<?php
declare(strict_types=1);

require_once __DIR__ . "/inc/config.php";
require_once __DIR__ . "/api/_helpers.php";

require_login($pdo);

$paymentId = (int) ($_GET['payment_id'] ?? 0);

if ($paymentId <= 0) {
    header("Location: " . $base_url . "/payment.php");
    exit;
}

$stmt = $pdo->prepare("
    SELECT
        p.id,
        p.cust_id,
        p.order_id,
        p.amount,
        p.currency,
        p.status AS payment_status,
        p.paid_at,
        p.sub_id,
        o.status AS order_status,
        cs.package_id,
        cs.starts_at,
        cs.ends_at,
        pkg.name AS package_name
    FROM payments p
    INNER JOIN orders o
        ON o.id = p.order_id
    LEFT JOIN customer_subs cs
        ON cs.id = p.sub_id
    LEFT JOIN packages pkg
        ON pkg.id = cs.package_id
    WHERE p.id = ?
      AND p.cust_id = ?
    LIMIT 1
");

$stmt->execute([
    $paymentId,
    (int) $_SESSION['customer_id']
]);

$payment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$payment) {
    header("Location: " . $base_url . "/payment.php");
    exit;
}

$isSuccess = $payment['payment_status'] === 'paid';
$isFailed = $payment['payment_status'] === 'failed';

$title = $isSuccess
    ? 'Ödəniş uğurla tamamlandı'
    : ($isFailed ? 'Ödəniş uğursuz oldu' : 'Ödəniş emal olunur');

$message = $isSuccess
    ? 'Ödəniş təsdiqləndi və abunəliyiniz sistemdə yeniləndi.'
    : ($isFailed
        ? 'Ödəniş təsdiqlənmədi. Yenidən cəhd edə bilərsiniz.'
        : 'Ödənişin son statusu hələ tamamlanmayıb.');
?>
<!DOCTYPE html>
<html lang="az">
<head>
    <?php require_once __DIR__ . "/inc/head.php"; ?>
</head>
<body>
<div class="main-wrapper home-nine">
    <?php require_once __DIR__ . "/inc/header.php"; ?>

    <div class="breadcrumb-bar">
        <div class="container">
            <div class="row align-items-center text-center">
                <div class="col-md-12 col-12">
                    <h2 class="breadcrumb-title">Ödəniş</h2>
                </div>
            </div>
        </div>
    </div>

    <section class="section py-5">
        <div class="container">
            <div class="card mx-auto" style="max-width: 620px;">
                <div class="card-body text-center p-5">
                    <div class="mb-3" style="font-size: 48px;">
                        <?= $isSuccess ? '✓' : ($isFailed ? '×' : '…') ?>
                    </div>

                    <h3><?= htmlspecialchars($title) ?></h3>

                    <p class="text-muted mt-3">
                        <?= htmlspecialchars($message) ?>
                    </p>

                    <div class="mt-4 text-start">
                        <div><b>Sifariş:</b> #<?= (int) $payment['order_id'] ?></div>
                        <div>
                            <b>Məbləğ:</b>
                            <?= htmlspecialchars(number_format(
                                (float) $payment['amount'],
                                2,
                                '.',
                                ''
                            )) ?>
                            <?= htmlspecialchars($payment['currency']) ?>
                        </div>

                        <?php if ($isSuccess && $payment['package_name']): ?>
                            <div>
                                <b>Paket:</b>
                                <?= htmlspecialchars($payment['package_name']) ?>
                            </div>
                            <div>
                                <b>Abunəlik bitir:</b>
                                <?= htmlspecialchars($payment['ends_at'] ?? '') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <a
                        href="<?= htmlspecialchars($base_url) ?>/payment.php"
                        class="btn btn-primary mt-4"
                    >
                        Ödəniş səhifəsinə qayıt
                    </a>
                </div>
            </div>
        </div>
    </section>

    <?php require_once __DIR__ . "/inc/footer.php"; ?>
</div>
</body>
</html>
