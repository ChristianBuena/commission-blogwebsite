<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../private/includes/db.php';

// Mark all unread as read and delete them right after
$db->query("DELETE FROM notifications WHERE is_read=0");

// Fetch notifications (will be empty if all are deleted)
$notifications = $db->query("SELECT * FROM notifications ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Notifications | Get Unstuck Admin</title>
    <link rel="stylesheet" href="css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <div class="max-w-2xl mx-auto py-12 px-4">
        <h1 class="text-3xl font-bold mb-8 text-muted-coral" style="font-family: 'Pacifico', cursive;">All Notifications</h1>
        <div class="bg-white rounded-xl shadow p-6">
            <?php while ($note = $notifications->fetch_assoc()): ?>
                <div class="flex justify-between items-center border-b border-gray-100 py-4">
                    <div>
                        <div class="font-medium text-gray-800"><?php echo htmlspecialchars($note['message']); ?></div>
                        <div class="text-xs text-gray-500"><?php echo date('M d, Y h:i A', strtotime($note['created_at'])); ?></div>
                    </div>
                    <?php if (!empty($note['link'])): ?>
                        <a href="<?php echo htmlspecialchars($note['link']); ?>" class="text-sm text-muted-coral hover:underline">View</a>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
        <a href="admin.php" class="inline-block mt-8 text-muted-coral hover:underline">&larr; Back to Dashboard</a>
    </div>
</body>
</html>