<?php
session_start();
require_once __DIR__ . '/../private/includes/meta.php';
$pageTitle = 'Admin Login';
$pageDescription = 'Login to access the admin area.';
require_once __DIR__ . '/../private/includes/db.php';

// Handle login logic
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $validUser = 'admin';
    $validPass = 'password123'; // Change this in production!
    if ($username === $validUser && $password === $validPass) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php generateMetaTags($pageTitle, $pageDescription); ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Fonts and Tailwind for consistency -->
    <link rel="stylesheet" href="../public/css/index.css">
    <link rel="stylesheet" href="css/admin-login.css">
    <link href="https://fonts.googleapis.com/css2?family=Shadows+Into+Light&family=Pacifico&family=Gloria+Hallelujah&family=Patrick+Hand&family=Lora&family=Open+Sans&family=Quicksand:wght@400;600&family=Special+Elite&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
</head>
<body class="login-bg min-h-screen flex flex-col justify-center items-center relative">

    <!-- Dark Mode Toggle -->
    <button id="darkModeToggle" class="dark-toggle-btn" aria-label="Toggle dark mode">🌙</button>

    <!-- Login Logo and Subtitle -->
    <div style="margin-top: 2rem;">
        <h1 class="login-logo">Let's Get Unstuck</h1>
        <div class="login-sub">by Traci Edwards</div>
    </div>

    <!-- Login Form Card -->
    <form class="login-form" method="post" autocomplete="off">
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required autocomplete="username">

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required autocomplete="current-password">

        <button type="submit">Login</button>
    </form>

    <script>
        // Dark mode toggle logic
        const darkModeToggle = document.getElementById('darkModeToggle');
        const body = document.body;
        if (localStorage.getItem('darkMode') === 'enabled') {
            body.classList.add('dark-mode');
            darkModeToggle.textContent = '☀️';
        }
        darkModeToggle.addEventListener('click', function() {
            body.classList.toggle('dark-mode');
            if (body.classList.contains('dark-mode')) {
                localStorage.setItem('darkMode', 'enabled');
                darkModeToggle.textContent = '☀️';
            } else {
                localStorage.setItem('darkMode', 'disabled');
                darkModeToggle.textContent = '🌙';
            }
        });
    </script>
</body>
</html>
