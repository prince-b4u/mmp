<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$isLoggedIn = isset($_SESSION['user_id']);
?>
<nav class="navbar shadow-sm bg-accent text-primary-content mb-6">
    <div class="navbar-start">
        <a href="/listings.php" class="btn btn-secondary">
            <span class="icon-[material-symbols--garage-home] text-2xl"></span>
            Shop
        </a>
    </div>
    <div class="navbar-center">
        <span class="btn btn-ghost btn-primary text-xl"><?= $center ?? '' ?></span>
    </div>
    <div class="navbar-end flex flex-wrap gap-2
        sm:flex-row sm:items-center
        flex-col items-stretch">
        <?php if ($isLoggedIn): ?>
            <?php if (empty($hideDashboard)): ?>
                <a href="/dashboard.php" class="btn btn-outline">
                    <span class="icon-[mdi-light--account] text-lg"></span>
                    Dashboard
                </a>
            <?php endif; ?>
            <a href="/logout.php" class="btn btn-info btn-outline">
                <span class="icon-[mdi-light--logout] text-lg"></span>
                Logout
            </a>
        <?php else: ?>
            <a href="/login.php" class="btn btn-accent">
                <span class="icon-[mdi-light--login] text-lg"></span>
                Login
            </a>
        <?php endif; ?>
        <?php if (!empty($showBack)): ?>
            <a href="/listings.php" class="btn btn-outline flex items-center gap-1">
                <span class="icon-[mdi-light--arrow-left] text-lg"></span>
                Back to Shop
            </a>
        <?php endif; ?>
    </div>
</nav>