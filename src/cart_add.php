<?php
session_start();
header('Content-Type: application/json');

$listing_id = isset($_POST['listing_id']) ? (int)$_POST['listing_id'] : 0;
$quantity = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;

if (!$listing_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid listing.']);
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
if (!isset($_SESSION['cart'][$listing_id])) {
    $_SESSION['cart'][$listing_id] = 0;
}
$_SESSION['cart'][$listing_id] += $quantity;

echo json_encode(['success' => true, 'cart_count' => array_sum($_SESSION['cart'])]);
exit;
