<?php
require 'config.php';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';


    $statement = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = :username OR email = :email");
    $statement->execute([
        'username' => $usernameOrEmail,
        'email' => $usernameOrEmail
    ]);
    $user = $statement->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="silk">

<?php include __DIR__ . '/components/head.php'; ?>

<body class="min-h-screen flex items-center justify-center bg-base-200 px-2 sm:px-0">
    <div class="w-full max-w-xs mx-auto">
        <fieldset class="fieldset bg-base-200 border-base-300 rounded-box w-full border p-6">
            <legend class="fieldset-legend  font-semibold mb-2 text-center text-3xl">Login</legend>
            <?php if ($error): ?>
                <div role="alert" class="alert alert-error w-full text-center mb-2">
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" class="grid gap-3 w-full">
                <label class="label" for="username">Username or Email</label>
                <input type="text" id="username" name="username" placeholder="Username or Email" class="input input-bordered w-full" />

                <label class="label" for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Password" class="input input-bordered w-full" />

                <button type="submit" class="btn btn-success w-full mt-2">Login</button>
            </form>
            <div class="mt-4 text-center w-full">
                <span class="text-sm">Don't have an account?</span>
                <a href="register.php" class="btn btn-outline btn-sm w-full mt-2">Register</a>
            </div>
        </fieldset>
    </div>
</body>

</html>