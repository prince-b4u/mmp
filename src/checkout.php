<?php
session_start();
require 'config.php';

$buyer_id = $_SESSION['user_id'];
$cart = $_SESSION['cart'] ?? [];

foreach ($cart as $listing_id => $quantity) {
    $statement = $pdo->prepare("INSERT INTO purchases (buyer_id, listing_id, quantity, purchased_at) VALUES (?, ?, ?, NOW())");
    $statement->execute([$buyer_id, $listing_id, $quantity]);

    $update = $pdo->prepare("UPDATE listings SET quantity = quantity - ? WHERE id = ? AND quantity >= ?");
    $update->execute([$quantity, $listing_id, $quantity]);
}


unset($_SESSION['cart']);

header('Location: dashboard.php');
exit;
