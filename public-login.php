<?php
session_start();
require_once __DIR__ . '/private/includes/meta.php';
$pageTitle = 'User Login | Get Unstuck';
$pageDescription = 'Login to access the user area.';
require_once __DIR__ . '/private/includes/db.php';

// Handle login logic
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Check users table for username or email
    $stmt = $db->prepare("SELECT id, username, password, is_admin FROM users WHERE username=? OR email=? LIMIT 1");
    $stmt->bind_param('ss', $username, $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $db_username, $db_password, $is_admin);
        $stmt->fetch();
        if ($db_password && password_verify($password, $db_password)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $db_username;
            $_SESSION['is_admin'] = (bool)$is_admin; // <-- Store admin flag

            // Record the visit
            $visitStmt = $db->prepare("INSERT INTO visitors (user_id) VALUES (?)");
            $visitStmt->bind_param('i', $user_id);
            $visitStmt->execute();
            $visitStmt->close();

            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    } else {
        $error = 'Invalid username or password.';
    }
    $stmt->close();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php 
    // this already emits <title>…</title>, description, og:… etc.
    generateMetaTags($pageTitle, $pageDescription); 
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- DROP this, it's already in generateMetaTags(): -->
    <!-- <title><?php // echo htmlspecialchars($pageTitle); ?></title> -->

    <!-- make sure these paths match where your CSS files actually live on Hostinger -->
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/public-login.css">
    <link href="https://fonts.googleapis.com/css2?family=Shadows+Into+Light&family=Pacifico&family=Gloria+Hallelujah&family=Patrick+Hand&family=Lora&family=Open+Sans&family=Quicksand:wght@400;600&family=Special+Elite&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
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
        <label for="username">Username or Email</label>
        <input type="text" id="username" name="username" required autocomplete="username">

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required autocomplete="current-password">

        <button type="submit">Login</button>

        <!-- Not registered yet portal -->
        <div class="mt-4 text-center">
            <span>Not registered yet?</span>
            <a href="signup.php" class="text-blue-600 hover:underline ml-1">Create an account</a>
        </div>
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
