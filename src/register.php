<?php
require 'config.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';


    if ($username === '' || $email === '' || $password === '') {
        $errors[] = "All fields are required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        $statement = $pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
        $statement->execute([
            'username' => $username,
            'email' => $email
        ]);
        if ($statement->fetch()) {
            $errors[] = "Username or email already taken.";
        }
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $statement = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :hash)");
        $statement->execute([
            'username' => $username,
            'email' => $email,
            'hash' => $hash
        ]);

        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['username'] = $username;
        header("Location: dashboard.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="silk">

<?php include __DIR__ . '/components/head.php'; ?>

<body class="min-h-screen flex items-center justify-center bg-base-200 px-2 sm:px-0">
    <div class="card bg-base-100 w-full max-w-md shadow-sm mx-auto">
        <div class="card-body flex flex-col items-center justify-center w-full">
            <h2 class="text-2xl mb-4 text-center card-title">Create an Account</h2>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error w-full text-center mb-4">
                    <ul class="list-disc ml-5 text-left">
                        <?php foreach ($errors as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" class="grid gap-4 w-full max-w-xs mx-auto">
                <input type="text" name="username" placeholder="Username" required class="input input-bordered w-full" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                <input type="email" name="email" placeholder="Email" required class="input input-bordered w-full" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                <input type="password" name="password" placeholder="Password" required class="input input-bordered w-full">
                <input type="password" name="confirm" placeholder="Confirm Password" required class="input input-bordered w-full">
                <button type="submit" class="btn btn-success w-full">Register</button>
            </form>

            <div class="mt-4 text-center w-full">
                <span class="text-sm">Already have an account?</span>
                <a href="login.php" class="btn btn-outline btn-sm w-full mt-2">Log in</a>
            </div>
        </div>
    </div>
</body>

</html>