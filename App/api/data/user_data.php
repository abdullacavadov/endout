<?php

if (isset($_SESSION['customer_id']) && is_numeric($_SESSION['customer_id'])) {

  $stmt = $pdo->prepare("
        SELECT *
        FROM customers
        WHERE id = ?
        LIMIT 1
    ");

  $stmt->execute([$_SESSION['customer_id']]);
  $customer = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($customer) {
    $customerId = (int) $customer['id'];
    $cust_name = $customer['full_name'] ?? 'N/A';
    $cust_email = $customer['email'] ?? 'N/A';
    $cust_phone = $customer['phone'] ?? 'N/A';
    $cust_phone_verified = $customer['phone_verified'] ?? 0;
    $cust_email_verified = $customer['email_verified'] ?? 0;
    $cust_type = $customer['type'] ?? 'N/A';
    
  } else {
    // session saxtadırsa təmizlə
    unset($_SESSION['customer_id']);
  }
}