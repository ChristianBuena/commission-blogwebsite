<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit;
}

$db = new mysqli('localhost', 'root', '', 'getunstuck');
if ($db->connect_error) {
    log_error('DB Connection failed: ' . $db->connect_error);
    die('Sorry, we are experiencing technical difficulties. Please try again later.');
}

// Handle new task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task'])) {
    $task = $db->real_escape_string($_POST['task']);
    $due = $db->real_escape_string($_POST['due']);
    $priority = $db->real_escape_string($_POST['priority']);
    $db->query("INSERT INTO tasks (task, due, priority) VALUES ('$task', '$due', '$priority')");
}
// Handle delete task
if (isset($_GET['delete_task'])) {
    $id = (int)$_GET['delete_task'];
    $db->query("DELETE FROM tasks WHERE id=$id");
}
// Fetch tasks
$tasks = $db->query("SELECT * FROM tasks ORDER BY due ASC, priority DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Admin Dashboard for Let's Get Unstuck blog - Manage posts, podcasts, and reader submissions">
    <title>Admin Dashboard | Let's Get Unstuck by Traci Edwards</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/weekly-post.css">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Quicksand:wght@300;400;500;600;700&family=Lato:wght@300;400;700&family=Open+Sans:wght@300;400;600;700&family=Shadows+Into+Light&family=Patrick+Hand&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="flex min-h-screen">
        <!-- Sidebar Navigation -->
        <aside class="sidebar w-64 min-h-screen fixed left-0 top-0 z-10 bg-white shadow-lg border-r border-gray-100">
            <div class="sidebar-content flex flex-col h-full p-4">
                <!-- Logo -->
                <div class="mb-8 px-4 py-2">
                    <h1 class="font-bold text-2xl" style="font-family: 'Pacifico', cursive; color: #FF5E78;">Let's Get Unstuck</h1>
                    <p class="text-sm text-gray-400">Admin Dashboard</p>
                </div>
                
                <!-- Navigation Links -->
                <nav class="flex-grow">
                    <ul class="space-y-2">
                        <li>
                            <a href="admin.php" class="sidebar-link active flex items-center px-4 py-3 rounded-lg text-muted-coral font-semibold transition">
                                <svg class="doodle-icon w-5 h-5 mr-3" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                </svg>
                                <span class="sidebar-link-text">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="weekly-post.php" class="sidebar-link flex items-center px-4 py-3 rounded-lg hover:bg-soft-pink hover:bg-opacity-10 transition">
                                <svg class="doodle-icon w-5 h-5 mr-3" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10 9 9 9 8 9"></polyline>
                                </svg>
                                <span class="sidebar-link-text">Weekly Posts</span>
                            </a>
                        </li>
                        <li>
                            <a href="podcast.php" class="sidebar-link flex items-center px-4 py-3 rounded-lg hover:bg-light-lavender hover:bg-opacity-10 transition">
                                <svg class="doodle-icon w-5 h-5 mr-3" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <line x1="12" y1="1" x2="12" y2="3"></line>
                                    <line x1="12" y1="21" x2="12" y2="23"></line>
                                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                                    <line x1="1" y1="12" x2="3" y2="12"></line>
                                    <line x1="21" y1="12" x2="23" y2="12"></line>
                                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                                </svg>
                                <span class="sidebar-link-text" >Podcast Segments</span>
                            </a>
                        </li>
                        <li>
                        <a href="reader-submission.php" class="sidebar-link flex items-center px-4 py-3 rounded-lg relative">
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
                            <a href="../index.php" target="_blank" class="sidebar-link flex items-center px-4 py-3 rounded-lg hover:bg-sage-green hover:bg-opacity-10 transition font-semibold text-sage-green">
                                <svg class="doodle-icon w-5 h-5 mr-3" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke="currentColor" stroke-width="1.5" d="M15 3h4.2A1.8 1.8 0 0 1 21 4.8v14.4a1.8 1.8 0 0 1-1.8 1.8H5.8A1.8 1.8 0 0 1 4 19.2V15"></path>
                                    <path stroke="currentColor" stroke-width="1.5" d="M10 14l7-7m0 0h-4m4 0v4"></path>
                                </svg>
                                <span class="sidebar-link-text">Preview Site</span>
                            </a>
                        </li>

                    </ul>
                </nav>
                
                <!-- User Profile -->
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
        <main id="main-content" class="flex-grow ml-64 p-8 bg-gray-50 min-h-screen">
            <!-- Header -->
            <header class="mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold mb-1 text-muted-coral" style="font-family: 'Pacifico', cursive;color: #FF5E78;">Dashboard</h1>
                        <p class="text-gray-500">Welcome back, Traci! Here's what's happening with your blog.</p>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <input type="text" placeholder="Search..." class="search-input w-64">
                        </div>
                        
                        <div class="dropdown">
                            <button class="p-2 rounded-full hover:bg-gray-100 relative">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                <?php
                                $notifCountResult = $db->query("SELECT COUNT(*) as unread FROM notifications WHERE is_read=0");
                                $notifCount = $notifCountResult ? ($notifCountResult->fetch_assoc()['unread'] ?? 0) : 0;
                                ?>
                                <span class="notification-badge"><?php echo $notifCount; ?></span>
                            </button>
                            <div class="dropdown-menu">
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <h3 class="text-sm font-semibold">Notifications</h3>
                                </div>
                                <?php
                                $notifications = $db->query("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 5");
                                while ($note = $notifications->fetch_assoc()):
                                ?>
                                    <div class="px-4 py-2 border-b border-gray-100">
                                        <a href="<?php echo htmlspecialchars($note['link']); ?>" class="text-xs text-muted-coral hover:underline">
                                            <?php echo htmlspecialchars($note['message']); ?>
                                        </a>
                                    </div>
                                <?php endwhile; ?>
                                <div class="px-4 py-2 border-t border-gray-100">
                                    <a href="notifications.php" class="text-xs text-muted-coral hover:underline">View all notifications</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Stats Overview -->
            <section class="mb-8 opacity-0 fade-in">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Stat Card 1 -->
                    <div class="stats-card p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-gray-500 text-sm">Total Visitors</h3>
                                <p class="text-3xl font-bold">
                                    <?php
                                    // Example: Fetch from analytics table
                                    require_once 'includes/db.php';
                                    $result = $db->query("SELECT COUNT(DISTINCT user_id) as total FROM visitors");
$row = $result->fetch_assoc();
echo $row['total'] ?? 0;
                                    ?>
                                </p>
                            </div>
                            <div class="p-2 rounded-lg bg-soft-pink">
                                <svg class="w-6 h-6 text-muted-coral" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <span class="text-xs text-gray-500">The number of users that visit your blog</span>
                        </div>
                    </div>
                    
                    <!-- Stat Card 2 -->
                    <div class="stats-card p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-gray-500 text-sm">New Submissions</h3>
                                <p class="text-3xl font-bold">
                                    <?php
                                    $newSubs = $db->query("SELECT COUNT(*) as total FROM submissions WHERE DATE(created_at) = CURDATE()");
                                    $row = $newSubs->fetch_assoc();
                                    echo $row['total'] ?? 0;
                                    ?>
                                </p>
                            </div>
                            <div class="p-2 rounded-lg bg-dusty-blue bg-opacity-20">
                                <svg class="w-6 h-6 text-dusty-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <span class="text-xs text-gray-500">The number of the users posts</span>
                        </div>
                    </div>
                    
                    <!-- Stat Card 3 -->
                    <div class="stats-card p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-gray-500 text-sm">Published Posts</h3>
                                <p class="text-3xl font-bold">
                                    <?php
                                    $count = $db->query("SELECT COUNT(*) as total FROM posts WHERE status='Published'");
                                    $row = $count->fetch_assoc();
                                    echo $row['total'] ?? 0;
                                    ?>
                                </p>
                            </div>
                            <div class="p-2 rounded-lg bg-sage-green bg-opacity-20">
                                <svg class="w-6 h-6 text-sage-green" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19v2m0 0h-3m3 0h3m-3-2a4 4 0 004-4V7a4 4 0 10-8 0v6a4 4 0 004 4z" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <span class="text-xs text-gray-500">The number of author's posts</span>
                        </div>
                    </div>
                    
                    <!-- Stat Card 4 -->
                    <div class="stats-card p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-gray-500 text-sm">Podcast Episodes</h3>
                                <p class="text-3xl font-bold">
                                    <?php
                                    $podcasts = $db->query("SELECT COUNT(*) as total FROM podcasts");
                                    $row = $podcasts->fetch_assoc();
                                    echo $row['total'] ?? 0;
                                    ?>
                                </p>
                            </div>
                            <div class="p-2 rounded-lg bg-light-lavender bg-opacity-30">
                                <svg class="w-6 h-6 text-light-lavender" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <span class="text-xs text-gray-500">The number of podcast segment</span>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Quick Actions -->
            <section class="mb-8 opacity-0 fade-in delay-100">
                <h2 class="text-xl font-bold mb-4">Quick Actions</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="weekly-post.php" class="dashboard-card p-6 flex items-center hover:bg-soft-pink hover:bg-opacity-10 transition-colors">
                        <div class="p-3 rounded-full bg-soft-pink mr-4">
                            <svg class="w-6 h-6 text-muted-coral" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold">Create New Post</h3>
                            <p class="text-sm text-gray-500">Share your latest thoughts</p>
                        </div>
                    </a>
                    
                    <a href="reader-submission.php" class="dashboard-card p-6 flex items-center hover:bg-dusty-blue hover:bg-opacity-10 transition-colors">
                        <div class="p-3 rounded-full bg-dusty-blue bg-opacity-20 mr-4">
                            <svg class="w-6 h-6 text-dusty-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold">Review Submissions</h3>
                            <p class="text-sm text-gray-500">Review your follower's story</p>
                        </div>
                    </a>
                    
                    <a href="podcast.php" class="dashboard-card p-6 flex items-center hover:bg-sage-green hover:bg-opacity-10 transition-colors">
                        <div class="p-3 rounded-full bg-sage-green bg-opacity-20 mr-4">
                            <svg class="w-6 h-6 text-sage-green" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19v2m0 0h-3m3 0h3m-3-2a4 4 0 004-4V7a4 4 0 10-8 0v6a4 4 0 004 4z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold">Schedule Podcast</h3>
                            <p class="text-sm text-gray-500">Plan your next recording</p>
                        </div>
                    </a>
                </div>
            </section>
            
            <!-- Recent Posts -->
            <section class="mb-8 opacity-0 fade-in delay-200">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold">Recent Posts</h2>
                    <a href="weekly-post.php" class="text-sm text-muted-coral hover:underline">View All Posts</a>
                </div>
                <div class="table-container bg-white">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
<?php
$posts = $db->query("SELECT title, created_at, status, is_weekly FROM posts ORDER BY created_at DESC LIMIT 4");
while ($post = $posts->fetch_assoc()):
?>
<tr>
    <td class="font-medium"><?php echo htmlspecialchars($post['title']); ?></td>
    <td><?php echo date('M d, Y', strtotime($post['created_at'])); ?></td>
    <td>
        <?php if ($post['is_weekly'] == 1 && strtolower($post['status']) == 'published'): ?>
            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-[#2EC4B6] text-white">Published</span>
        <?php else: ?>
            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-[#E9C46A] text-white">Draft</span>
        <?php endif; ?>
    </td>
    <td>
        <div class="flex space-x-2">
            <!-- Action buttons (Edit/View/Delete) -->
        </div>
    </td>
</tr>
<?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>
    
    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Submissions -->
        <section class="opacity-0 fade-in delay-300">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Recent Submissions</h2>
                <a href="reader-submission.php" class="text-sm text-muted-coral hover:underline">View All</a>
            </div>
            
            <div class="dashboard-card p-6">
<?php
$submissions = $db->query("SELECT id, name, excerpt, created_at FROM submissions ORDER BY created_at DESC LIMIT 3");
while ($submission = $submissions->fetch_assoc()):
?>
    <div class="mb-6 pb-6 border-b border-gray-100">
        <div class="flex justify-between items-start mb-2">
            <h3 class="font-semibold"><?php echo htmlspecialchars($submission['name']); ?></h3>
            <span class="text-xs text-gray-500"><?php echo date('M d, Y', strtotime($submission['created_at'])); ?></span>
        </div>
        <p class="text-gray-600 mb-3"><?php echo htmlspecialchars($submission['excerpt']); ?></p>
        <div class="flex justify-between items-center">
            <div class="flex space-x-2">
                <form method="POST" action="includes/approve_submission.php">
                    <input type="hidden" name="submission_id" value="<?php echo $submission['id']; ?>">
                    <button type="submit" class="px-3 py-1 text-xs rounded-full bg-soft-pink text-muted-coral hover:bg-muted-coral hover:text-white transition-colors">Approve</button>
                </form>
                <button class="px-3 py-1 text-xs rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">Decline</button>
            </div>
            <button class="text-xs text-dusty-blue hover:underline">Read Full</button>
        </div>
    </div>
<?php endwhile; ?>
</div>
        </section>
        
        <!-- Upcoming Tasks -->
        <section class="opacity-0 fade-in delay-400">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Upcoming Tasks</h2>
            </div>
            <div class="bg-white rounded-xl shadow p-6">
                <table class="dashboard-table w-full mb-6">
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th>Due</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($tasks as $task): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($task['task']); ?></td>
                            <td><?php echo htmlspecialchars($task['due']); ?></td>
                            <td>
                                <span class="badge badge-<?php echo strtolower($task['priority']); ?>">
                                    <?php echo htmlspecialchars($task['priority']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($task['status']); ?></td>
                            <td>
                                <a href="?delete_task=<?php echo $task['id']; ?>" onclick="return confirm('Delete this task?')" class="text-xs text-red-500 hover:underline">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <!-- Add Task Form -->
                <form method="post" class="add-task-form pt-4 border-t border-gray-100">
                    <input type="text" name="task" placeholder="New Task" required class="form-input">
                    <input type="date" name="due" required class="form-input">
                    <select name="priority" class="form-input">
                        <option>High</option>
                        <option>Medium</option>
                        <option>Low</option>
                    </select>
                    <button type="submit" name="add_task" class="block mt-4 px-4 py-2 rounded-lg bg-[#FF5E78] text-white text-center font-semibold hover:bg-[#FF6F61] transition">Add</button>
                </form>
            </div>
        </section>
    </div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animate elements on page load
        const fadeElements = document.querySelectorAll('.fade-in');
        fadeElements.forEach(element => {
            element.style.opacity = '1';
        });
        
        // Custom checkbox functionality
        const checkboxes = document.querySelectorAll('.custom-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('click', function() {
                this.classList.toggle('checked');
            });
        });
        
        // Mobile sidebar toggle
        const mediaQuery = window.matchMedia('(max-width: 768px)');
        function handleScreenChange(e) {
            if (e.matches) {
                // Mobile view
                document.querySelector('.main-content').style.marginLeft = '0';
                document.querySelector('.main-content').style.marginBottom = '70px';
            } else {
                // Desktop view
                document.querySelector('.main-content').style.marginLeft = '16rem';
                document.querySelector('.main-content').style.marginBottom = '0';
            }
        }
        
        // Initial check
        handleScreenChange(mediaQuery);
        
        // Add listener for changes
        mediaQuery.addEventListener('change', handleScreenChange);
    });
</script>
<script>
function updateUnreadBadge() {
    fetch('includes/unread_count.php')
        .then(response => response.text())
        .then(count => {
            document.querySelectorAll('.notification-badge').forEach(el => el.textContent = count || '0');
        });
}
updateUnreadBadge(); // Set badge to 0 on load if no unread
setInterval(updateUnreadBadge, 10000); // Update every 10 seconds
</script>
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'94e801fae797f8f0',t:'MTc0OTcxNzI1My4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script>
</body>
</html>
