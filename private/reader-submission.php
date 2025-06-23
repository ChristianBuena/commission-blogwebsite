<?php
require_once 'includes/db.php';
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit;
}

require_once __DIR__ . '/../private/includes/db.php';

if (!$db) {
    log_error('DB Connection failed: ' . mysqli_connect_error());
    $errors['db'] = "Sorry, we couldn't connect to the database. Please try again later.";
}

// Handle actions (approve, decline, feature, mark as read)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['submission_id'])) {
    $id = (int)$_POST['submission_id'];
    if ($_POST['action'] === 'approve') {
        $db->query("UPDATE submissions SET status='approved', featured=0 WHERE id=$id");
    } elseif ($_POST['action'] === 'decline') {
        $db->query("DELETE FROM submissions WHERE id=$id");
    } elseif ($_POST['action'] === 'feature') {
        $db->query("UPDATE submissions SET status='approved', featured=1 WHERE id=$id");
    } elseif ($_POST['action'] === 'mark_read') {
        $db->query("UPDATE submissions SET status='read' WHERE id=$id");
    }
}

// Fetch submissions
$submissions = $db->query("SELECT * FROM submissions ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Admin Dashboard for Let's Get Unstuck blog - Manage posts, podcasts, and reader submissions">
    <title>Reader Submission | Let's Get Unstuck Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/weekly-post.css">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Quicksand:wght@300;400;500;600;700&family=Lato:wght@300;400;700&family=Open+Sans:wght@300;400;600;700&family=Shadows+Into+Light&family=Patrick+Hand&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>   
<div class="flex min-h-screen">
    <!-- Sidebar (same as admin.php) -->
    <aside class="sidebar w-64 min-h-screen fixed left-0 top-0 z-10 bg-white shadow-lg border-r border-gray-100">
        <div class="sidebar-content flex flex-col h-full p-4">
            <div class="mb-8 px-4 py-2">
                <h1 class="font-bold text-2xl" style="font-family: 'Pacifico', cursive; color: #FF5E78;">Let's Get Unstuck</h1>
                <p class="text-sm text-gray-400">Admin Dashboard</p>
            </div>
            <nav class="flex-grow">
                <ul class="space-y-2">
                    <li>
                        <a href="admin.php" class="sidebar-link flex items-center px-4 py-3 rounded-lg hover:bg-soft-pink hover:bg-opacity-10 transition">
                            <svg class="doodle-icon w-5 h-5 mr-3" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                            <span class="sidebar-link-text">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="weekly-post.php" class="sidebar-link flex items-center px-4 py-3 rounded-lg hover:bg-soft-pink hover:bg-opacity-10 transition">
                            <svg class="doodle-icon w-5 h-5 mr-3" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                            <span class="sidebar-link-text">Weekly Posts</span>
                        </a>
                    </li>
                        <li>
                            <a href="podcast.php" class="sidebar-link flex items-center px-4 py-3 rounded-lg hover:bg-light-lavender hover:bg-opacity-10 transition">
                                <svg class="doodle-icon w-5 h-5 mr-3" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="3"></circle></svg>
                                <span class="sidebar-link-text">Podcast Segments</span>
                            </a>
                        </li>
                    <li>
                    <a href="reader-submission.php" class="sidebar-link active flex items-center px-4 py-3 rounded-lg text-muted-coral font-semibold transition">
                        <svg class="doodle-icon w-5 h-5 mr-3" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                        <span class="sidebar-link-text">Reader Submissions</span>
                        <?php
                        $unread = $db->query("SELECT COUNT(*) as unread FROM submissions WHERE status='pending' OR status='unread'");
                        $unreadCount = $unread->fetch_assoc()['unread'] ?? 0;
                        ?>
                        <span class="notification-badge"><?php echo $unreadCount; ?></span>
                    </a>
                    </li>
                        <li>
                            <a href="admin-coaches.php" class="sidebar-link flex items-center px-4 py-3 rounded-lg hover:bg-soft-pink hover:bg-opacity-10 transition">
                                <svg class="doodle-icon w-5 h-5 mr-3" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="12" cy="12" r="10"/>
                                    <text x="12" y="16" text-anchor="middle" font-size="10" fill="#fff" font-family="Arial" dy=".3em">C</text>
                                </svg>
                                <span class="sidebar-link-text">Customize Coaches</span>
                            </a>
                        </li>
                    <li>
                        <a href="../share-story.php" target="_blank" class="sidebar-link flex items-center px-4 py-3 rounded-lg hover:bg-sage-green hover:bg-opacity-10 transition font-semibold text-sage-green">
                            <svg class="doodle-icon w-5 h-5 mr-3" viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="1.5" d="M15 3h4.2A1.8 1.8 0 0 1 21 4.8v14.4a1.8 1.8 0 0 1-1.8 1.8H5.8A1.8 1.8 0 0 1 4 19.2V15"></path><path stroke="currentColor" stroke-width="1.5" d="M10 14l7-7m0 0h-4m4 0v4"></path></svg>
                            <span class="sidebar-link-text">Preview Site</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="mt-auto pt-4 border-t border-gray-200">
                <div class="flex items-center px-4 py-2">
                    <div class="flex-shrink-0">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100' width='40' height='40'%3E%3Ccircle cx='50' cy='50' r='50' fill='%23FFD6E0'/%3E%3Ccircle cx='50' cy='40' r='20' fill='%23FF8C94'/%3E%3Cpath d='M25,85 Q50,65 75,85' stroke='%23FF8C94' stroke-width='4' fill='none'/%3E%3C/svg%3E" alt="Traci Edwards" class="avatar">
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">Traci Edwards</p>
                        <p class="text-xs text-gray-500">Admin</p>
                    </div>
                </div>
                <a href="logout.php" class="block mt-4 px-4 py-2 rounded-lg bg-[#FF5E78] text-white text-center font-semibold hover:bg-[#FF6F61] transition">
                Logout
                </a>
            </div>
        </div>
    </aside>
        
        <!-- Main Content -->
        <div class="flex-1 ml-64">
            <!-- Reader Submissions View -->
            <div id="submissions-view" class="p-8">
                <header class="mb-8">
                    <div class="flex justify-between items-center mb-2">
                        <h1 class="text-2xl font-bold" style="font-family: 'Pacifico', cursive;color: #FF5E78;">Reader Submissions</h1>
                    </div>
                    <p class="text-gray-500">Manage reader stories and submissions</p>
                </header>
                <div class="card p-6 mb-8 bg-white rounded-xl shadow-lg">
                    <?php while ($row = $submissions->fetch_assoc()): ?>
                        <div class="dashboard-card p-6 mb-6 rounded-lg shadow flex flex-col gap-3 <?php echo $row['status'] === 'unread' ? 'border-l-4 border-[#FF8C94]' : ''; ?>">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex items-center">
                                    <div class="avatar h-10 w-10 rounded-full flex items-center justify-center mr-3 bg-[#FFD6E0] text-[#FF8C94] font-bold text-lg">
                                        <?php echo strtoupper(substr($row['name'], 0, 2)); ?>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-base"><?php echo htmlspecialchars($row['name']); ?></h3>
                                        <p class="text-sm text-gray-500"><?php echo htmlspecialchars($row['email'] ?? ''); ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <?php if (!empty($row['featured'])): ?>
                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-[#FFD6E0] text-[#FF5E78]" style="font-family: 'Quicksand', 'Lato', 'Open Sans', Arial, sans-serif;">Featured</span>
                                    <?php elseif ($row['status'] === 'unread'): ?>
                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-[#FFF6F8] text-[#FF8C94]" style="font-family: 'Quicksand', 'Lato', 'Open Sans', Arial, sans-serif;">Unread</span>
                                    <?php else: ?>
                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-[#E9C46A] text-white" style="font-family: 'Quicksand', 'Lato', 'Open Sans', Arial, sans-serif;">Read</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="mb-2">
                                <h4 class="font-medium mb-1"><?php echo htmlspecialchars($row['title'] ?? ''); ?></h4>
                                <p class="text-sm text-gray-700"><?php echo nl2br(htmlspecialchars($row['content'] ?? '')); ?></p>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="text-xs text-gray-500">
                                    Received: <?php echo date('F d, Y • h:i A', strtotime($row['created_at'])); ?>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" class="btn-outline text-sm px-3 py-1 rounded-md font-semibold submission-action" data-id="<?php echo $row['id']; ?>" data-action="approve">Approve</button>
                                    <button type="button" class="btn-outline text-sm px-3 py-1 rounded-md font-semibold submission-action" data-id="<?php echo $row['id']; ?>" data-action="decline">Decline</button>
                                    <button type="button" class="btn-outline text-sm px-3 py-1 rounded-md font-semibold submission-action" data-id="<?php echo $row['id']; ?>" data-action="feature">Feature</button>
                                    <?php if ($row['status'] === 'unread'): ?>
                                        <button type="button" class="btn-outline text-sm px-3 py-1 rounded-md font-semibold submission-action" data-id="<?php echo $row['id']; ?>" data-action="mark_read">Mark as Read</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.submission-action').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const action = this.getAttribute('data-action');
            const card = this.closest('.dashboard-card');
            fetch('ajax/submission-action.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `submission_id=${id}&action=${action}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Optionally, update card status visually
                    if (action === 'approve') {
                        card.querySelector('.inline-block').textContent = 'Read';
                        card.querySelector('.inline-block').className = 'inline-block px-3 py-1 rounded-full text-xs font-semibold bg-[#E9C46A] text-white';
                    }
                    if (action === 'decline') {
                        card.remove();
                    }
                    if (action === 'feature') {
                        card.querySelector('.inline-block').textContent = 'Featured';
                        card.querySelector('.inline-block').className = 'inline-block px-3 py-1 rounded-full text-xs font-semibold bg-[#FFD6E0] text-[#FF5E78]';
                    }
                    if (action === 'mark_read') {
                        card.querySelector('.inline-block').textContent = 'Read';
                        card.querySelector('.inline-block').className = 'inline-block px-3 py-1 rounded-full text-xs font-semibold bg-[#E9C46A] text-white';
                    }
                    // Update badge
                    document.querySelector('.notification-badge').textContent = data.unread;
                } else {
                    alert(data.error || 'Action failed');
                }
            });
        });
    });
});
</script>