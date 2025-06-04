<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_listing'])) {
    $delete_id = (int)$_POST['delete_listing'];
    $imgs = $pdo->prepare("SELECT image_path FROM listing_images WHERE listing_id = :id");
    $imgs->execute(['id' => $delete_id]);
    foreach ($imgs->fetchAll(PDO::FETCH_ASSOC) as $img) {
        @unlink(__DIR__ . '/../../../' . $img['image_path']);
    }
    $pdo->prepare("DELETE FROM listing_images WHERE listing_id = :id")->execute(['id' => $delete_id]);
    $pdo->prepare("DELETE FROM listings WHERE id = :id AND user_id = :user_id")->execute([
        'id' => $delete_id,
        'user_id' => $user_id
    ]);
    header("Location: dashboard.php");
    exit;
}
