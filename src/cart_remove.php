<?php
session_start();

$listing_id = isset($_POST['listing_id']) ? intval($_POST['listing_id']) : 0;
$success = false;

if ($listing_id && isset($_SESSION['cart'][$listing_id])) {
    unset($_SESSION['cart'][$listing_id]);
    $success = true;
}

$is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($is_ajax) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'cart_count' => isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0
    ]);
    exit;
} else {
    header('Location: cart.php');
    exit;
}
