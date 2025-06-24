<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

$errors = [];

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
        $statement = $pdo->prepare("INSERT INTO listings (user_id, category_id, title, description, price, location, quantity) 
                               VALUES (:user_id, :category_id, :title, :description, :price, :location, :quantity)");
        $statement->execute([
            'user_id' => $_SESSION['user_id'],
            'category_id' => $category_id ?: null,
            'title' => $title,
            'description' => $description,
            'price' => $price,
            'location' => $location,
            'quantity' => $quantity
        ]);

        $listing_id = $pdo->lastInsertId();

        if (!empty($_FILES['images']['name'][0])) {
            $upload_dir = __DIR__ . '/uploads/';
            foreach ($_FILES['images']['tmp_name'] as $index => $tmpName) {
                $original_name = $_FILES['images']['name'][$index];
                $error = $_FILES['images']['error'][$index];

                if ($error === UPLOAD_ERR_OK && is_uploaded_file($tmpName)) {
                    $ext = pathinfo($original_name, PATHINFO_EXTENSION);
                    $safeName = uniqid() . '.' . strtolower($ext);
                    $targetPath = $upload_dir . $safeName;

                    if (move_uploaded_file($tmpName, $targetPath)) {
                        $statement = $pdo->prepare("INSERT INTO listing_images (listing_id, image_path) VALUES (?, ?)");
                        $statement->execute([$listing_id, 'uploads/' . $safeName]);
                    }
                }
            }
        }

        header("Location: listings.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="silk">

<?php include __DIR__ . '/components/head.php'; ?>

<body>
    <?php
    $center = "Shop Listings";
    $showBack = false;
    include __DIR__ . '/components/navbar.php';
    ?>

    <div class="container mx-auto max-w min-h-screen p-4">

        <div class="max-w-2xl mx-auto p-6">
            <fieldset class="fieldset bg-base-200 border-base-300 rounded-box w-full border p-6">
                <legend class="fieldset-legend text-lg font-semibold mb-4">Post a New Listing</legend>

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
                    <input type="text" id="title" name="title" placeholder="Title" required class="input input-bordered w-full"
                        value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">

                    <label class="label" for="description">Description</label>
                    <textarea id="description" name="description" placeholder="Description" class="textarea textarea-bordered w-full"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>

                    <label class="label" for="price">Price</label>
                    <input type="number" step="0.01" id="price" name="price" placeholder="Price" required class="input input-bordered w-full"
                        value="<?= htmlspecialchars($_POST['price'] ?? '') ?>">

                    <label class="label" for="location">Location</label>
                    <input type="text" id="location" name="location" placeholder="Location" class="input input-bordered w-full"
                        value="<?= htmlspecialchars($_POST['location'] ?? '') ?>">

                    <label class="label" for="category_id">Category</label>
                    <select id="category_id" name="category_id" class="select select-bordered w-full">
                        <option value="">-- Select Category --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= isset($_POST['category_id']) && $_POST['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <div class="form-control mb-4">
                        <label for="quantity" class="label">Quantity</label>
                        <input
                            type="number"
                            id="quantity"
                            name="quantity"
                            class="input input-bordered w-full"
                            min="1"
                            value="<?= isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1 ?>"
                            required>
                    </div>

                    <label class="label" for="images">Images</label>
                    <input type="file" id="images" name="images[]" multiple class="file-input file-input-primary w-full" />

                    <button type="submit" class="btn btn-success mt-4">Create Listing</button>
                </form>
            </fieldset>
        </div>

    </div>
</body>

</html>