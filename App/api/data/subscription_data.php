<?php
// Aktiv abunəliyi tap (cust_id BIGINT UNSIGNED)
$stmt = $pdo->prepare("
  SELECT cs.*, p.name AS package_name, p.slug AS package_slug
  FROM customer_subs cs
  JOIN packages p ON p.id = cs.package_id
  WHERE cs.cust_id = ?
    AND cs.status = 'active'
    AND cs.starts_at <= NOW()
    AND cs.ends_at >= NOW()
  ORDER BY cs.ends_at DESC
  LIMIT 1
");
$stmt->execute([$_SESSION['customer_id']]);

$activeSub = $stmt->fetch(PDO::FETCH_ASSOC);
$hasActiveSub = !empty($activeSub);


$sub_package_name = $activeSub['package_name'] ?? '';
$sub_ends_at      = $activeSub['ends_at'] ?? '';