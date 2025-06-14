<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
global $pdo;

$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

$statement = $pdo->prepare("SELECT * FROM listings WHERE id = :id AND user_id = :user_id");
$statement->execute(['id' => $id, 'user_id' => $_SESSION['user_id']]);
$listing = $statement->fetch(PDO::FETCH_ASSOC);

if (!$listing) {
    die("Listing not found or you do not have permission to edit it.");
}

$errors = [];

$images = $pdo->prepare("SELECT id, image_path FROM listing_images WHERE listing_id = :listing_id");
$images->execute(['listing_id' => $id]);
$current_images = $images->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_image'])) {
    $img_id = (int)$_POST['delete_image'];
    $img_statement = $pdo->prepare("SELECT image_path FROM listing_images WHERE id = :id AND listing_id = :listing_id");
    $img_statement->execute(['id' => $img_id, 'listing_id' => $id]);
    $img = $img_statement->fetch(PDO::FETCH_ASSOC);
    if ($img) {
        @unlink(__DIR__ . '/../' . $img['image_path']);
        $pdo->prepare("DELETE FROM listing_images WHERE id = :id")->execute(['id' => $img_id]);
    }
    header("Location: edit_listing.php?id=$id");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $category_id = intval($_POST['category_id'] ?? 0);
    $location = trim($_POST['location'] ?? '');
    $quantity = intval($_POST['quantity'] ?? 1);

    if ($title === '' || $price <= 0) {
        $errors[] = "Title and price are required.";
    }

    if (empty($errors)) {
        $statement = $pdo->prepare("UPDATE listings SET title = :title, description = :description, price = :price, category_id = :category_id, location = :location, quantity = :quantity WHERE id = :id AND user_id = :user_id");
        $statement->execute([
            'title' => $title,
            'description' => $description,
            'price' => $price,
            'category_id' => $category_id ?: null,
            'location' => $location,
            'quantity' => $quantity,
            'id' => $id,
            'user_id' => $_SESSION['user_id']
        ]);

        if (!empty($_FILES['images']['name'][0])) {
            $upload_dir = 'uploads/';
            if (!is_dir(__DIR__ . '/' . $upload_dir)) {
                mkdir(__DIR__ . '/' . $upload_dir, 0777, true);
            }
            foreach ($_FILES['images']['tmp_name'] as $idx => $tmp_name) {
                if ($_FILES['images']['error'][$idx] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($_FILES['images']['name'][$idx], PATHINFO_EXTENSION);
                    $filename = uniqid() . '.' . $ext;
                    $target = $upload_dir . $filename;
                    if (move_uploaded_file($tmp_name, __DIR__ . '/' . $target)) {
                        $pdo->prepare("INSERT INTO listing_images (listing_id, image_path) VALUES (:listing_id, :image_path)")
                            ->execute(['listing_id' => $id, 'image_path' => $target]);
                    }
                }
            }
        }
        header("Location: dashboard.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="silk">

<?php include __DIR__ . '/components/head.php'; ?>

<body>
    <?php
    $center = "Edit Listing";
    $showBack = false;
    include __DIR__ . '/components/navbar.php';
    ?>
    <div class="container mx-auto max-w min-h-screen p-4">
        <div class="max-w-2xl mx-auto p-6">
            <fieldset class="fieldset bg-base-200 border-base-300 rounded-box w-full border p-6">
                <legend class="fieldset-legend text-lg font-semibold mb-4">Edit Listing</legend>
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error mb-4">
                        <ul class="list-disc ml-5">
                            <?php foreach ($errors as $e): ?>
                                <li><?= htmlspecialchars($e) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                <form method="POST" enctype="multipart/form-data" class="grid gap-4">
                    <label class="label" for="title">Title</label>
                    <input type="text" id="title" name="title" class="input input-bordered w-full" required value="<?= htmlspecialchars($_POST['title'] ?? $listing['title']) ?>">

                    <label class="label" for="description">Description</label>
                    <textarea id="description" name="description" class="textarea textarea-bordered w-full"><?= htmlspecialchars($_POST['description'] ?? $listing['description']) ?></textarea>

                    <label class="label" for="price">Price</label>
                    <input type="number" step="0.01" id="price" name="price" class="input input-bordered w-full" required value="<?= htmlspecialchars($_POST['price'] ?? $listing['price']) ?>">

                    <label class="label" for="location">Location</label>
                    <input type="text" id="location" name="location" class="input input-bordered w-full" value="<?= htmlspecialchars($_POST['location'] ?? $listing['location']) ?>">

                    <label class="label" for="category_id">Category</label>
                    <select id="category_id" name="category_id" class="select select-bordered w-full">
                        <option value="">-- Select Category --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= (($_POST['category_id'] ?? $listing['category_id']) == $cat['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label class="label" for="quantity">Quantity</label>
                    <input
                        type="number"
                        id="quantity"
                        name="quantity"
                        class="input input-bordered w-full"
                        min="1"
                        value="<?= htmlspecialchars($_POST['quantity'] ?? $listing['quantity']) ?>"
                        required>

                    <label class="label">Current Images</label>
                    <div class="flex flex-wrap gap-2 mb-2">
                        <?php foreach ($current_images as $img): ?>
                            <div class="flex flex-col items-center">
                                <img src="/<?= htmlspecialchars($img['image_path']) ?>" class="w-24 h-24 object-cover rounded border mb-1" />
                                <button
                                    type="button"
                                    class="btn btn-xs btn-error mt-1 delete-image-btn"
                                    data-image-id="<?= $img['id'] ?>"
                                    title="Delete">
                                    &times; Remove
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <label class="label" for="images">Add Images</label>
                    <input type="file" id="images" name="images[]" multiple class="file-input file-input-primary w-full" />

                    <button type="submit" class="btn btn-success mt-4">Update Listing</button>
                </form>
            </fieldset>
        </div>
    </div>
    <script>
        document.querySelectorAll('.delete-image-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (!confirm('Delete this image?')) return;
                const imgId = this.dataset.imageId;
                fetch(window.location.pathname + '?id=<?= $id ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'delete_image=' + encodeURIComponent(imgId)
                    })
                    .then(res => res.ok ? location.reload() : alert('Failed to delete image'));
            });
        });
    </script>
</body>

</html>