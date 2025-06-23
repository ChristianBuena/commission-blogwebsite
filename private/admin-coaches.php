<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit;
}
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/meta.php';

// Handle add/edit/delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $db->real_escape_string($_POST['name']);
    $subtitle = $db->real_escape_string($_POST['subtitle']);
    $description = $db->real_escape_string($_POST['description']);
    $highlight = $db->real_escape_string($_POST['highlight']);
    $color = $db->real_escape_string($_POST['color']);
    $episode_title = $db->real_escape_string($_POST['episode_title']);
    $episode_link = $db->real_escape_string($_POST['episode_link']);
    $sort_order = (int)($_POST['sort_order'] ?? 0);

    // Handle image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Change upload directory to root level coaches-uploads
        $uploadDir = __DIR__ . '/../coaches-uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $originalName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', basename($_FILES['image']['name']));
        $filename = uniqid() . '_' . $originalName;
        $targetFile = $uploadDir . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $image = $filename;
        }
    }

    if (isset($_POST['edit_id'])) {
        // Update
        $id = (int)$_POST['edit_id'];
        if ($image) {
            $imgSql = ", image='$image'";
        } else {
            // Keep the old image
            $imgSql = "";
        }
        $db->query("UPDATE coaches SET name='$name', subtitle='$subtitle', description='$description', highlight='$highlight', color='$color', episode_title='$episode_title', episode_link='$episode_link', sort_order=$sort_order $imgSql WHERE id=$id");
    } else {
        // Insert
        $db->query("INSERT INTO coaches (name, subtitle, description, highlight, image, color, episode_title, episode_link, sort_order) VALUES ('$name', '$subtitle', '$description', '$highlight', '$image', '$color', '$episode_title', '$episode_link', $sort_order)");
    }
    header('Location: admin-coaches.php');
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $db->query("DELETE FROM coaches WHERE id=$id");
    header('Location: admin-coaches.php');
    exit;
}

// Fetch all coaches
$coaches = $db->query("SELECT * FROM coaches ORDER BY sort_order ASC, id ASC");
$editCoach = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $editCoach = $db->query("SELECT * FROM coaches WHERE id=$id")->fetch_assoc();
}

if (!$db) {
    log_error('DB Connection failed: ' . mysqli_connect_error());
    $errors['db'] = "Sorry, we couldn't connect to the database. Please try again later.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coaches Customization | Let's Get Unstuck Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin.css">
        <link rel="stylesheet" href="css/admin-coaches.css">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                            <a href="admin-coaches.php" class="sidebar-link active flex items-center px-4 py-3 rounded-lg text-muted-coral font-semibold transition">
                                <svg class="doodle-icon w-5 h-5 mr-3" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="12" cy="12" r="10"/>
                                    <text x="12" y="16" text-anchor="middle" font-size="10" fill="#fff" font-family="Arial" dy=".3em">C</text>
                                </svg>
                                <span class="sidebar-link-text">Customize Coaches</span>
                            </a>
                    </li>
                    <li>
                        <a href="../the-coaches.php" target="_blank" class="sidebar-link flex items-center px-4 py-3 rounded-lg hover:bg-sage-green hover:bg-opacity-10 transition font-semibold text-sage-green">
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
    
        <!-- Main Content Area -->
        <main id="main-content" class="flex-grow ml-64 p-8 bg-gray-50 min-h-screen">
            <h1 class="handwritten-bold text-4xl mb-8 text-[#FF5E78] text-center">Customize Coaches</h1>
            <div class="dashboard-card p-8 mb-12 max-w-2xl mx-auto relative">
                <form method="post" enctype="multipart/form-data">
                    <?php if ($editCoach): ?>
                        <input type="hidden" name="edit_id" value="<?php echo $editCoach['id']; ?>">
                    <?php endif; ?>
                    <div class="mb-4">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-input w-full" required value="<?php echo htmlspecialchars($editCoach['name'] ?? ''); ?>">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Subtitle</label>
                        <input type="text" name="subtitle" class="form-input w-full" value="<?php echo htmlspecialchars($editCoach['subtitle'] ?? ''); ?>">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-input w-full" rows="3"><?php echo htmlspecialchars($editCoach['description'] ?? ''); ?></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Highlight (handwritten quote)</label>
                        <input type="text" name="highlight" class="form-input w-full" value="<?php echo htmlspecialchars($editCoach['highlight'] ?? ''); ?>">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Episode Title</label>
                        <input type="text" name="episode_title" class="form-input w-full" value="<?php echo htmlspecialchars($editCoach['episode_title'] ?? ''); ?>">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Episode Link</label>
                        <input type="url" name="episode_link" class="form-input w-full" value="<?php echo htmlspecialchars($editCoach['episode_link'] ?? ''); ?>">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Accent Color (hex, e.g. #FF5E78)</label>
                        <input type="text" name="color" class="form-input w-full" value="<?php echo htmlspecialchars($editCoach['color'] ?? ''); ?>">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-input w-full" value="<?php echo htmlspecialchars($editCoach['sort_order'] ?? '0'); ?>">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Photo</label>
                        <input type="file" name="image" class="form-input w-full">
                        <?php if (!empty($editCoach['image'])): ?>
                            <img src="../coaches-uploads/<?php echo htmlspecialchars($editCoach['image']); ?>" alt="Coach Image" class="w-20 h-20 mt-2 rounded shadow">
                        <?php endif; ?>
                    </div>
                    <div class="flex gap-4 mt-6">
                        <button type="submit" class="btn-primary handwritten-bold px-6 py-2 rounded"><?php echo $editCoach ? 'Update Coach' : 'Add Coach'; ?></button>
                        <a href="admin-coaches.php" class="btn-secondary px-6 py-2 rounded handwritten-casual">Cancel</a>
                    </div>
                </form>
            </div>

            <h2 class="handwritten-alt text-2xl mb-6 text-[#2EC4B6] text-center">All Coaches</h2>
            <div class="grid md:grid-cols-2 gap-8">
                <?php while ($coach = $coaches->fetch_assoc()): ?>
                    <div class="dashboard-card flex items-center gap-4 p-4 relative mb-4">
    <div class="polaroid bg-white p-2 rounded shadow mr-2" style="min-width:70px;">
        <img src="../coaches-uploads/<?php echo htmlspecialchars($coach['image']); ?>" alt="<?php echo htmlspecialchars($coach['name']); ?>" class="w-24 h-24 object-cover rounded shadow" />
        <div class="polaroid-caption handwritten-alt"><?php echo htmlspecialchars($coach['name']); ?></div>
    </div>
    <div class="flex-1">
        <div class="font-bold text-lg handwritten-bold" style="color:<?php echo htmlspecialchars($coach['color']); ?>"><?php echo htmlspecialchars($coach['name']); ?></div>
        <div class="text-sm text-gray-500 handwritten-casual"><?php echo htmlspecialchars($coach['subtitle']); ?></div>
    </div>
    <a href="?edit=<?php echo $coach['id']; ?>" class="btn-secondary px-4 py-2 rounded handwritten-casual mr-2">Edit</a>
    <a href="?delete=<?php echo $coach['id']; ?>" class="btn-primary px-4 py-2 rounded handwritten-casual" onclick="return confirm('Delete this coach?')">Delete</a>
</div>
                <?php endwhile; ?>
            </div>
        </main>
    </div>
</body>
</html>