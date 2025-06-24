<?php
require 'config.php';
session_start();

$cart = $_SESSION['cart'] ?? [];

$cart = array_filter($cart, function ($qty, $id) {
    return intval($id) > 0 && intval($qty) > 0;
}, ARRAY_FILTER_USE_BOTH);

$listings = [];
if (!empty($cart)) {
    $placeholders = implode(',', array_fill(0, count($cart), '?'));
    $statement = $pdo->prepare("SELECT * FROM listings WHERE id IN ($placeholders)");
    $statement->execute(array_keys($cart));
    $listings = $statement->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="silk">

<?php include __DIR__ . '/components/head.php'; ?>

<body>
    <?php
    $center = "Your Cart";
    $showBack = false;
    include __DIR__ . '/components/navbar.php';
    ?>
    <div class="container mx-auto max-w min-h-screen p-2 sm:p-4">
        <?php if ($listings): ?>
            <div class="border border-base-300 rounded-lg p-2 sm:p-4 mb-6 bg-base-100 max-h-[70vh] sm:max-h-[420px] overflow-y-auto">
                <div class="grid grid-cols-1 xs:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                    <?php foreach ($listings as $listing):
                        $qty = (int)($cart[$listing['id']] ?? 1);
                        $total = $qty * $listing['price'];
                        $img_statement = $pdo->prepare("SELECT image_path FROM listing_images WHERE listing_id = ? LIMIT 1");
                        $img_statement->execute([$listing['id']]);
                        $img_row = $img_statement->fetch(PDO::FETCH_ASSOC);
                        $img_source = $img_row ? htmlspecialchars($img_row['image_path']) : null;
                    ?>
                        <div class="card w-full bg-base-100 shadow-sm min-h-[180px]">
                            <?php if ($img_source): ?>
                                <figure>
                                    <img src="<?= $img_source ?>" alt="Listing image" class="w-full h-32 sm:h-40 object-cover" />
                                </figure>
                            <?php endif; ?>
                            <div class="card-body flex-1">
                                <div class="font-semibold text-lg mb-2"><?= htmlspecialchars($listing['title']) ?></div>
                                <div class="text-primary font-bold mb-2">
                                    R<?= number_format($listing['price'], 2) ?>
                                    <span class="text-xs text-gray-500">each</span>
                                </div>
                                <div class="mb-2">Quantity: <span class="font-semibold"><?= $qty ?></span></div>
                                <div class="mb-4">Total: <span class="font-bold">R<?= number_format($total, 2) ?></span></div>
                                <div class="card-actions mt-2 sm:mt-auto">
                                    <form action="cart_remove.php" method="POST" class="w-full">
                                        <input type="hidden" name="listing_id" value="<?= $listing['id'] ?>">
                                        <button type="submit" class="btn btn-error btn-sm gap-1 w-full sm:w-auto">
                                            <span class="icon-[material-symbols--delete-sweep-outline] text-lg"></span>
                                            Remove
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="mb-8 max-w-full sm:max-w-xl mx-auto">
                <form id="address-form">
                    <div class="font-bold text-lg mb-2">Delivery Address</div>
                    <div class="form-control mb-2">
                        <label class="label">
                            <span class="label-text">Street Address</span>
                        </label>
                        <input type="text" class="input input-bordered w-full" name="street_address" id="street_address"
                            placeholder="123 Main St" value="Kaapzicht Building, 9 Rogers St" required>
                    </div>
                    <div class="form-control mb-2">
                        <label class="label">
                            <span class="label-text">City</span>
                        </label>
                        <input type="text" class="input input-bordered w-full" name="city" id="city"
                            placeholder="City" value="Tyger Valley, Cape Town" required>
                    </div>
                    <div class="form-control mb-2">
                        <label class="label">
                            <span class="label-text">Postal Code</span>
                        </label>
                        <input type="text" class="input input-bordered w-full" name="postal_code" id="postal_code"
                            placeholder="Postal Code" value="7530" required>
                    </div>
                </form>
            </div>

            <div class="mb-8 max-w-full sm:max-w-xl mx-auto">
                <form id="payment-method-form">
                    <div class="font-bold text-lg mb-2">Choose Payment Method</div>
                    <div class="space-y-2">
                        <div tabindex="0" class="collapse collapse-plus bg-base-100 border-base-300 border">
                            <div class="collapse-title font-semibold flex items-center gap-2">
                                <input type="radio" name="payment_method" value="credit_card" class="radio radio-primary" checked required>
                                Credit Card
                            </div>
                            <div class="collapse-content text-sm space-y-2">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Card Number</span>
                                    </label>
                                    <input type="text" class="input input-bordered w-full" value="4242 4242 4242 4242" readonly>
                                </div>
                                <div class="form-control flex flex-row gap-2">
                                    <div class="flex-1">
                                        <label class="label">
                                            <span class="label-text">Expiry</span>
                                        </label>
                                        <input type="text" class="input input-bordered w-full" value="12/34" readonly>
                                    </div>
                                    <div class="flex-1">
                                        <label class="label">
                                            <span class="label-text">CVC</span>
                                        </label>
                                        <input type="text" class="input input-bordered w-full" value="123" readonly>
                                    </div>
                                </div>
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Name on Card</span>
                                    </label>
                                    <input type="text" class="input input-bordered w-full" value="John Doe" readonly>
                                </div>
                            </div>
                        </div>
                        <div tabindex="0" class="collapse collapse-plus bg-base-100 border-base-300 border">
                            <div class="collapse-title font-semibold flex items-center gap-2">
                                <input type="radio" name="payment_method" value="eft" class="radio radio-primary" required>
                                EFT (Electronic Funds Transfer)
                            </div>
                            <div class="collapse-content text-sm">
                                <p>Bank: Example Bank<br>
                                    Account Number: 123456789<br>
                                    Branch Code: 12345</p>
                            </div>
                        </div>
                        <div tabindex="0" class="collapse collapse-plus bg-base-100 border-base-300 border">
                            <div class="collapse-title font-semibold flex items-center gap-2">
                                <input type="radio" name="payment_method" value="cod" class="radio radio-primary" required>
                                Cash on Delivery
                            </div>
                            <div class="collapse-content text-sm">
                                <p>Pay with cash when your item is delivered.</p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <form action="checkout.php" method="POST" class="fixed bottom-4 right-4 z-50">
                <span class="relative">
                    <button id="checkout-btn" type="submit" class="btn btn-circle  btn-xl bg-success text-white hover:bg-success-focus shadow-lg" disabled>
                        <span class="icon-[material-symbols--credit-card-outline] text-3xl"></span>
                        <span class="sr-only">Checkout</span>
                    </button>
                </span>
            </form>
            <script>
                function validateCheckout() {
                    const paymentChecked = document.querySelector('input[name="payment_method"]:checked');
                    const street = document.getElementById('street_address').value.trim();
                    const city = document.getElementById('city').value.trim();
                    const postal = document.getElementById('postal_code').value.trim();
                    const valid = paymentChecked && street && city && postal;
                    document.getElementById('checkout-btn').disabled = !valid;
                }
                document.querySelectorAll('input[name="payment_method"]').forEach(el => {
                    el.addEventListener('change', validateCheckout);
                });
                ['street_address', 'city', 'postal_code'].forEach(id => {
                    document.getElementById(id).addEventListener('input', validateCheckout);
                });
                window.addEventListener('DOMContentLoaded', validateCheckout);
            </script>
        <?php else: ?>
            <div class="alert alert-info gap-2 justify-center items-center">
                <span class="icon-[material-symbols--shopping-cart] text-3xl"></span>
                <span>Your cart is empty.</span>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>