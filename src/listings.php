<?php
require 'config.php';
session_start();

$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

$category = isset($_GET['category']) ? (int)$_GET['category'] : null;

if ($category) {
    $statement = $pdo->prepare("SELECT l.*, u.username, GROUP_CONCAT(li.image_path) AS images
        FROM listings l
        JOIN users u ON l.user_id = u.id
        LEFT JOIN listing_images li ON l.id = li.listing_id
        WHERE l.category_id = :category
        GROUP BY l.id
        ORDER BY l.id DESC");
    $statement->execute(['category' => $category]);
} else {
    $statement = $pdo->query("SELECT l.*, u.username, GROUP_CONCAT(li.image_path) AS images
        FROM listings l
        JOIN users u ON l.user_id = u.id
        LEFT JOIN listing_images li ON l.id = li.listing_id
        GROUP BY l.id
        ORDER BY l.id DESC");
}
$listings = $statement->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en" data-theme="silk">

<?php include __DIR__ . '/components/head.php'; ?>

<body>
    <?php
    $center = "Listings";
    $showBack = false;
    include __DIR__ . '/components/navbar.php';
    ?>
    <div class="container mx-auto max-w min-h-screen p-4">
        <div class="mb-6 flex flex-wrap gap-2 justify-center">
            <a href="#" class="btn filter-btn <?= !$category ? 'btn-primary' : 'btn-outline' ?>" data-category="all">All</a>
            <?php foreach ($categories as $cat): ?>
                <a href="#" class="btn filter-btn <?= ($category == $cat['id']) ? 'btn-primary' : 'btn-outline' ?>"
                    data-category="<?= $cat['id'] ?>">
                    <?= htmlspecialchars($cat['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php foreach ($listings as $listing): ?>
                <?php
                $images = $listing['images'] ? explode(',', $listing['images']) : [];
                $is_owner = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $listing['user_id'];
                ?>
                <div class="bg-white rounded shadow hover:shadow-md transition overflow-hidden listing-card cursor-pointer"
                    data-category="<?= htmlspecialchars($listing['category_id']) ?>"
                    data-listing='<?= htmlspecialchars(json_encode($listing), ENT_QUOTES, "UTF-8") ?>'
                    <?php if (!empty($images)): ?>
                    data-images='<?= htmlspecialchars(json_encode($images), ENT_QUOTES, "UTF-8") ?>'
                    <?php endif; ?>>
                    <?php if (!empty($images)): ?>
                        <div class="carousel w-96">
                            <?php foreach ($images as $img): ?>
                                <div class="carousel-item w-full">
                                    <img src="/<?= htmlspecialchars($img) ?>" class="w-full object-cover h-48" alt="Listing image" />
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="w-full h-48 flex items-center justify-center bg-gray-100 text-gray-500">
                            No Images
                        </div>
                    <?php endif; ?>

                    <div class="p-4 grid gap-1">
                        <h2 class="text-lg font-semibold truncate"><?= htmlspecialchars($listing['title']) ?></h2>
                        <p class="text-green-600 font-bold">R<?= number_format($listing['price'], 2) ?></p>
                        <p class="text-sm truncate"><?= htmlspecialchars($listing['location'] ?? 'Unknown location') ?></p>
                        <p class="text-xs">By <?= htmlspecialchars($listing['username']) ?></p>
                        <p class="text-sm text-info">Quantity: <?= (int)$listing['quantity'] ?></p>

                        <?php if (isset($_SESSION['user_id']) && !$is_owner): ?>
                            <form
                                action="/cart_add.php"
                                method="POST"
                                class="mt-2 add-to-cart-form flex gap-2 items-center"
                                data-listing-id="<?= $listing['id'] ?>">
                                <input type="hidden" name="listing_id" value="<?= $listing['id'] ?>">
                                <input
                                    type="number"
                                    name="quantity"
                                    min="1"
                                    max="<?= (int)$listing['quantity'] ?>"
                                    value="1"
                                    class="input input-bordered input-xs h-10 w-20"
                                    required>
                                <button
                                    type="submit"
                                    class="btn btn-primary">
                                    <span class="icon-[material-symbols--shopping-cart] text-3xl"></span>
                                    Add to Cart
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php
    $cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
    ?>
    <a href="/cart.php"
        class="fixed bottom-6 right-6 z-50">
        <span class="relative">
            <button class="btn btn-circle btn-xl btn-dash bg-accent text-white hover:bg-accent-focus">
                <span class="icon-[material-symbols--shopping-cart] text-3xl"></span>
            </button>
            <?php if ($cart_count > 0): ?>
                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full px-2 py-0.5"
                    id="cart-badge"
                    style="<?= $cart_count > 0 ? '' : 'display:none;' ?>">
                    <?= $cart_count ?>
                </span>
            <?php endif; ?>
        </span>
    </a>


    <dialog id="listing-modal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box" id="listing-modal-content">
        </div>
    </dialog>
    <script>
        window.isLoggedIn = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;
    </script>
    <script src="/js/listings.js"></script>
</body>

</html>