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