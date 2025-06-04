<?php
require 'config.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];


include __DIR__ . '/components/dashboard/delete_listing.php';


include __DIR__ . '/components/dashboard/fetch_dashboard_data.php';
?>
<!DOCTYPE html>
<html lang="en" data-theme="silk">
<?php include __DIR__ . '/components/head.php'; ?>

<body>
    <?php
    $center = "Dashboard";
    $showBack = false;
    $hideDashboard = true;
    include __DIR__ . '/components/navbar.php';
    ?>
    <div class="container mx-auto max-w min-h-screen p-4">
        <?php include __DIR__ . '/components/dashboard/orders_tabs.php'; ?>
        <div class="flex justify-center mb-6 pt-3">
            <a href="/create_listing.php" class="btn btn-primary">
                <span class="icon-[material-symbols--shoppingmode-outline] text-xl"></span>
                Add New Listing
            </a>
        </div>
        <?php include __DIR__ . '/components/dashboard/listings_grid.php'; ?>
    </div>
</body>

</html>