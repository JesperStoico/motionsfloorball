<?php
session_start();
require_once __DIR__ . '/../config/admin.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';

    if (password_verify($password, ADMIN_PASSWORD_HASH)) {
        $_SESSION['is_admin'] = true;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Incorrect password';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>

<h2>Admin Login</h2>

<?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post">
    <input type="password" name="password" required placeholder="Password">
    <button type="submit">Login</button>
</form>

</body>
</html>
