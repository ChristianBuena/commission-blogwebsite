<?php
session_start();
$username = $_SESSION['newly_registered_username'] ?? null;
if (!$username) {
    // If accessed directly, redirect to login
    header("Location: public-login.php");
    exit;
}
// Clear the session variable so it doesn't persist
unset($_SESSION['newly_registered_username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Welcome!</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../private/css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Quicksand:wght@300;400;500;600;700&family=Lato:wght@300;400;700&family=Open+Sans:wght@300;400;600;700&family=Shadows+Into+Light&family=Patrick+Hand&display=swap" rel="stylesheet">
</head>
<body style="background: var(--cream);" class="min-h-screen flex flex-col justify-center items-center">
    <div class="dashboard-card p-10 mt-24 max-w-lg w-full text-center" style="box-shadow: var(--card-shadow);">
        <h1 class="font-bold text-3xl mb-2" style="font-family: 'Pacifico', cursive; color: #FF5E78;">Let's Get Unstuck</h1>
        <div class="mb-6 text-lg" style="font-family: 'Patrick Hand', cursive; color: #2EC4B6;">
            Welcome, <?= htmlspecialchars($username) ?>!
        </div>
        <h2 class="text-2xl font-bold mb-4" style="color: var(--muted-coral);">Registration Successful 🎉</h2>
        <p class="mb-6 text-gray-700" style="font-family: 'Quicksand', sans-serif;">
            Thank you for signing up, <span class="font-semibold"><?= htmlspecialchars($username) ?></span>!<br>
            You can now log in to your account and start your journey.
        </p>
        <a href="public-login.php" class="btn-primary px-8 py-3 rounded text-lg font-semibold hover:bg-[#FF6F61] transition" style="text-decoration:none;">
            Go to Login
        </a>
    </div>
</body>
</html>