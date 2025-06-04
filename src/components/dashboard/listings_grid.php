<?php
?>

<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
    <?php foreach ($listings as $listing): ?>
        <?php $images = $listing['images'] ? explode(',', $listing['images']) : []; ?>
        <div class="bg-white rounded shadow hover:shadow-md transition overflow-hidden">
            <?php if (!empty($images[0])): ?>
                <div class="grid grid-cols-2 gap-1 p-1">
                    <?php foreach ($images as $img): ?>
                        <img src="<?= htmlspecialchars($img) ?>" alt="Image" class="w-full h-32 object-cover rounded">
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="w-full h-48 flex items-center justify-center ">
                    No Images
                </div>
            <?php endif; ?>

            <div class="p-4 grid gap-1">
                <h2 class="text-lg font-semibold truncate"><?= htmlspecialchars($listing['title']) ?></h2>
                <p class="text-green-600 font-bold">R<?= number_format($listing['price'], 2) ?></p>
                <p class="text-sm text-gray-500 truncate"><?= htmlspecialchars($listing['location'] ?? 'Unknown location') ?></p>
                <p class="text-sm text-gray-700">Quantity: <?= (int)$listing['quantity'] ?></p>
                <div class="text-right text-sm text-gray-400">
                    <?= date('M d, Y', strtotime($listing['created_at'])) ?>
                </div>
                <div class="flex justify-end mt-2 gap-2">
                    <a href="edit_listing.php?id=<?= $listing['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                    <form method="POST" onsubmit="return confirm('Are you sure you want to delete this listing?');">
                        <input type="hidden" name="delete_listing" value="<?= $listing['id'] ?>">
                        <button type="submit" class="btn btn-error btn-sm">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>