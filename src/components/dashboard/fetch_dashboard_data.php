<?php

$statement = $pdo->prepare("
    SELECT 
        l.*, 
        GROUP_CONCAT(li.image_path) AS images
    FROM listings l
    LEFT JOIN listing_images li ON li.listing_id = l.id
    WHERE l.user_id = :user_id
    GROUP BY l.id
    ORDER BY l.created_at DESC
");
$statement->execute(['user_id' => $user_id]);
$listings = $statement->fetchAll(PDO::FETCH_ASSOC);

$purchases_statement = $pdo->prepare("
    SELECT 
        p.*, 
        l.title, l.price, l.location, l.created_at, 
        p.quantity AS purchase_quantity, -- use this in your badges
        GROUP_CONCAT(li.image_path) AS images,
        u.username AS seller_name
    FROM purchases p
    JOIN listings l ON p.listing_id = l.id
    LEFT JOIN listing_images li ON li.listing_id = l.id
    JOIN users u ON l.user_id = u.id
    WHERE p.buyer_id = :user_id
    GROUP BY p.id
    ORDER BY p.purchased_at DESC
");
$purchases_statement->execute(['user_id' => $user_id]);
$purchases = $purchases_statement->fetchAll(PDO::FETCH_ASSOC);

$sales_statement = $pdo->prepare("
    SELECT 
        p.*, 
        l.title, l.price, l.location, l.created_at, 
        p.quantity AS sale_quantity, -- use this in your badges
        GROUP_CONCAT(li.image_path) AS images,
        u.username AS buyer_name
    FROM purchases p
    JOIN listings l ON p.listing_id = l.id
    LEFT JOIN listing_images li ON li.listing_id = l.id
    JOIN users u ON p.buyer_id = u.id
    WHERE l.user_id = :user_id
    GROUP BY p.id
    ORDER BY p.purchased_at DESC
");
$sales_statement->execute(['user_id' => $user_id]);
$sales = $sales_statement->fetchAll(PDO::FETCH_ASSOC);
