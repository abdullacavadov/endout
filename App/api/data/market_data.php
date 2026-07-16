<?php

$stmt = $pdo->prepare("
  SELECT *
  FROM markets
  WHERE customer_id = ?
  LIMIT 1
");
$stmt->execute([$_SESSION['customer_id']]);
$market = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$market && $_SESSION['cust_type'] === 'partner') {
  header('Location: ./signup-market');
}

if ($market) {
  $market_id = $market['id'] ?? 0;
  $market_name = $market['name'] ?? '';
  $market_address = $market['address'] ?? '';
  $market_phone = $market['phone_number'] ?? '';
  $market_email = $market['email'] ?? '';
  $market_country = $market['country_id'] ?? '';
  $market_city = $market['city_id'] ?? '';
  $market_type = $market['type'] ?? '';
  $market_logo = $market['logo'] ?? '';
  $market_url = $market['url'] ?? '';
  $market_description = $market['description'] ?? '';
  $market_views = $market['views'] ?? 0;
  $market_subscriptions = $market['subscriptions'] ?? 0;
  $market_created_at = $market['created_at'] ?? '';

  $market_promo_code = $market['promocode'] ?? '';
  $market_promo_discount = $market['promodiscount'] ?? '';


  //market sosial media məlumatlarını əldə etmək üçün API endpointi
  $stmt = $pdo->prepare("
  SELECT ms.id, ms.social_name, ms.social_url, ms.social_icon
  FROM markets_socials ms
  WHERE ms.market_id = ?
  ORDER BY ms.id DESC
");
  $stmt->execute([$market['id']]);
  $market_socials = $stmt->fetchAll(PDO::FETCH_ASSOC);



  //market ölkə məlumatlarını əldə etmək üçün API endpointi
  $stmt = $pdo->prepare("
  SELECT *
  FROM countries
  WHERE id = ?
");
  $stmt->execute([$market['country_id']]);

  $market_country_data = $stmt->fetch(PDO::FETCH_ASSOC);
  $market_country_name = $market_country_data['name'] ?? '';



  //market şəhər məlumatlarını əldə etmək üçün API endpointi
  $stmt = $pdo->prepare("
  SELECT *
  FROM cities
  WHERE id = ?
");
  $stmt->execute([$market['city_id']]);

  $market_city_data = $stmt->fetch(PDO::FETCH_ASSOC);
  $market_city_name = $market_city_data['name'] ?? '';


  //market elanların göstərildiyi ölkələr məlumatlarını əldə etmək üçün API endpointi
  $stmt = $pdo->prepare("
  SELECT t2.name
  FROM market_ad_countries t1
  LEFT JOIN countries t2 ON t1.country_id = t2.id
  WHERE t1.market_id = ?
");
  $stmt->execute([$market['id']]);

  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $countryNames = [];

  foreach ($rows as $row) {
    if (!empty($row['name'])) {
      $countryNames[] = $row['name'];
    }
  }

  $countryList = !empty($countryNames)
    ? implode(', ', $countryNames)
    : '';

}

?>