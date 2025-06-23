<?php
session_start();
require_once __DIR__ . '/../private/includes/meta.php';
$pageTitle = 'User Sign Up | Get Unstuck';
$pageDescription = 'Login to access the user area.';
require_once __DIR__ . '/../private/includes/db.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    // Validation
    if (!$username) $errors['username'] = "Username is required.";
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = "Valid email required.";
    if (!$password || strlen($password) < 8) $errors['password'] = "Password must be at least 8 characters.";
    if ($password !== $confirm) $errors['confirm'] = "Passwords do not match.";

    // Check uniqueness
    if (!$errors) {
        $stmt = $db->prepare("SELECT id FROM users WHERE email=? OR username=?");
        $stmt->bind_param('ss', $email, $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) $errors['unique'] = "Email or username already taken.";
        $stmt->close();
    }

    // Register
    if (!$errors) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $username, $email, $hash);
        if ($stmt->execute()) {
            $success = true;
            $_SESSION['newly_registered_username'] = $username;
            header("Location: welcome.php");
            exit;
        } else {
            $errors['db'] = "Registration failed. Try again.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php generateMetaTags($pageTitle, $pageDescription); ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/index.css">
    <link rel="stylesheet" href="css/signup.css">
    <link href="https://fonts.googleapis.com/css2?family=Shadows+Into+Light&family=Pacifico&family=Gloria+Hallelujah&family=Patrick+Hand&family=Lora&family=Open+Sans&family=Quicksand:wght@400;600&family=Special+Elite&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
</head>
<body class="signup-bg min-h-screen flex flex-col justify-center items-center relative">

    <!-- Dark Mode Toggle -->
    <button id="darkModeToggle" class="dark-toggle-btn" aria-label="Toggle dark mode">🌙</button>

    <!-- Login Logo and Subtitle -->
    <div style="margin-top: 2rem;">
        <h1 class="signup-logo">Let's Get Unstuck</h1>
        <div class="signup-sub">by Traci Edwards</div>
    </div>

    <form class="signup-form" method="post" autocomplete="off">
        <?php if ($errors): ?>
            <div class="error">
                <?php foreach ($errors as $e) echo "<div>$e</div>"; ?>
            </div>
        <?php endif; ?>
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required autocomplete="username" value="<?=htmlspecialchars($_POST['username'] ?? '')?>">

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required autocomplete="email" value="<?=htmlspecialchars($_POST['email'] ?? '')?>">

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required autocomplete="new-password">

        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required autocomplete="new-password">

        <button type="submit">Sign Up</button>

        <div class="mt-4 text-center">
            Already have an account?
            <a href="public-login.php" class="text-blue-600 hover:underline ml-1">Login</a>
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