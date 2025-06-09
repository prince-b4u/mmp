<?php
?>

<script>
    function openOrderModal(el) {
        document.getElementById('orderModalTitle').textContent = el.dataset.title;
        document.getElementById('orderModalPrice').textContent = el.dataset.price;
        document.getElementById('orderModalLocation').textContent = el.dataset.location;
        document.getElementById('orderModalQuantity').textContent = el.dataset.quantity;
        document.getElementById('orderModalPartyLabel').textContent = el.dataset.partyLabel;
        document.getElementById('orderModalPartyName').textContent = el.dataset.partyName;
        document.getElementById('orderModalDate').textContent = el.dataset.date;
        document.getElementById('orderModalLink').href = '/listing.php?id=' + el.dataset.listingId;
        document.getElementById('orderModal').showModal();
    }
</script>

<dialog id="orderModal" class="modal">
    <form method="dialog" class="modal-box">
        <h3 class="font-bold text-lg" id="orderModalTitle"></h3>
        <p class="py-1"><span class="font-semibold">Price:</span> R<span id="orderModalPrice"></span></p>
        <p class="py-1"><span class="font-semibold">Location:</span> <span id="orderModalLocation"></span></p>
        <p class="py-1"><span class="font-semibold">Quantity:</span> <span id="orderModalQuantity"></span></p>
        <p class="py-1"><span class="font-semibold" id="orderModalPartyLabel"></span>: <span id="orderModalPartyName"></span></p>
        <p class="py-1"><span class="font-semibold">Date:</span> <span id="orderModalDate"></span></p>
        <div class="modal-action">
            <a id="orderModalLink" href="#" class="btn btn-primary" target="_blank">View Listing</a>
            <button class="btn">Close</button>
        </div>
    </form>
</dialog>

<div class="tabs tabs-lift mt-8">
    <label class="tab">
        <input type="radio" name="orders_tabs" checked="checked" />
        <span class="icon-[material-symbols--credit-card] text-2xl"></span>
        Purchases
    </label>
    <div class="tab-content bg-base-100 border-base-300 p-6">
        <h3 class="font-bold mb-2">Purchases</h3>
        <?php if (empty($purchases)): ?>
            <p>You have not purchased any items yet.</p>
        <?php else: ?>
            <div class="flex flex-wrap gap-2">
                <?php foreach ($purchases as $purchase): ?>
                    <div class="flex items-center gap-1">
                        <div
                            class="badge badge-success cursor-pointer"
                            data-title="<?= htmlspecialchars($purchase['title'], ENT_QUOTES) ?>"
                            data-price="<?= number_format($purchase['price'], 2) ?>"
                            data-location="<?= htmlspecialchars($purchase['location'] ?? 'Unknown location', ENT_QUOTES) ?>"
                            data-quantity="<?= (int)($purchase['purchase_quantity'] ?? $purchase['quantity']) ?>"
                            data-party-label="Seller"
                            data-party-name="<?= htmlspecialchars($purchase['seller_name'], ENT_QUOTES) ?>"
                            data-date="<?= date('M d, Y', strtotime($purchase['purchased_at'])) ?>"
                            data-listing-id="<?= (int)$purchase['listing_id'] ?>"
                            onclick="openOrderModal(this)"
                            title="Click for details">
                            <span class="icon-[material-symbols--shopping-bag] text-xl"></span>
                            <?= htmlspecialchars($purchase['title']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <label class="tab">
        <input type="radio" name="orders_tabs" />
        <span class="icon-[material-symbols--delivery-truck-speed-outline] text-2xl"></span>
        Sales
    </label>
    <div class="tab-content bg-base-100 border-base-300 p-6">
        <h3 class="font-bold mb-2">Sales Orders</h3>
        <?php if (empty($sales)): ?>
            <p>You have not sold any items yet.</p>
        <?php else: ?>
            <div class="flex flex-wrap gap-2">
                <?php foreach ($sales as $sale): ?>
                    <div class="flex items-center gap-1">
                        <div
                            class="badge badge-info cursor-pointer"
                            data-title="<?= htmlspecialchars($sale['title'], ENT_QUOTES) ?>"
                            data-price="<?= number_format($sale['price'], 2) ?>"
                            data-location="<?= htmlspecialchars($sale['location'] ?? 'Unknown location', ENT_QUOTES) ?>"
                            data-quantity="<?= (int)($sale['sale_quantity'] ?? $sale['quantity']) ?>"
                            data-party-label="Buyer"
                            data-party-name="<?= htmlspecialchars($sale['buyer_name'], ENT_QUOTES) ?>"
                            data-date="<?= date('M d, Y', strtotime($sale['purchased_at'])) ?>"
                            data-listing-id="<?= (int)$sale['listing_id'] ?>"
                            onclick="openOrderModal(this)"
                            title="Click for details">
                            <span class="icon-[material-symbols--sell] text-2xl"></span>
                            <?= htmlspecialchars($sale['title']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>