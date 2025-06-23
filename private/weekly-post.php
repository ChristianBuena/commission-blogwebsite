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

// Handle form submission for adding a new post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_post'])) {
    $title = $db->real_escape_string($_POST['post_title']);
    $excerpt = $db->real_escape_string($_POST['post_excerpt']);
    $link = $db->real_escape_string($_POST['post_link']);
    $imagePath = '';

    // Handle image upload
    if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = dirname(__DIR__) . '/uploads/'; 
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileName = uniqid('weekly_', true) . '_' . basename($_FILES['post_image']['name']);
        $targetFile = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES['post_image']['tmp_name'], $targetFile)) {
            $imagePath = $fileName; // Save only the filename in DB
        }
    }

    $db->query("INSERT INTO posts (title, excerpt, link, image, is_weekly, status, created_at) VALUES ('$title', '$excerpt', '$link', '$imagePath', 0, 'Draft', NOW())");
    header("Location: weekly-post.php?success=1");
    exit;
}

// Handle edit post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_post'])) {
    $id = (int)$_POST['edit_id'];
    $title = $db->real_escape_string($_POST['post_title']);
    $excerpt = $db->real_escape_string($_POST['post_excerpt']);
    $link = $db->real_escape_string($_POST['post_link']);
    $imagePath = $_POST['current_image'] ?? '';

    // Handle image upload
    if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = dirname(__DIR__) . '/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileName = uniqid('weekly_', true) . '_' . basename($_FILES['post_image']['name']);
        $targetFile = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES['post_image']['tmp_name'], $targetFile)) {
            $imagePath = $fileName; // Save only the filename in DB
        }
    }

    $db->query("UPDATE posts SET title='$title', excerpt='$excerpt', link='$link', image='$imagePath' WHERE id=$id");
    header("Location: weekly-post.php?success=1");
    exit;
}

// Handle delete post
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $db->query("DELETE FROM posts WHERE id=$id");
    header("Location: weekly-post.php?deleted=1");
    exit;
}

// Handle publish post
if (isset($_GET['publish']) && is_numeric($_GET['publish'])) {
    $id = (int)$_GET['publish'];
    $db->query("UPDATE posts SET is_weekly=1, status='Published' WHERE id=$id");
    header("Location: weekly-post.php?published=1");
    exit;
}

// Fetch all weekly posts (not just the current one)
$weeklyPosts = $db->query("SELECT * FROM posts ORDER BY created_at DESC");

// For edit form
$editPost = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editPost = $db->query("SELECT * FROM posts WHERE id=" . (int)$_GET['edit'])->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Post | Let's Get Unstuck Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/weekly-post.css">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    // Real-time image preview
    function previewImage(input) {
        const full = document.getElementById('imagePreviewFull');
        const thumb = document.getElementById('imagePreviewThumb');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                full.src = e.target.result;
                thumb.src = e.target.result;
                full.classList.remove('hidden');
                thumb.classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>
</head>
<body>
    <div class="flex min-h-screen">
        <!-- Sidebar (identical to admin.php) -->
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
                            <a href="weekly-post.php" class="sidebar-link active flex items-center px-4 py-3 rounded-lg text-muted-coral font-semibold transition">
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
                            <a href="admin-coaches.php" class="sidebar-link flex items-center px-4 py-3 rounded-lg hover:bg-soft-pink hover:bg-opacity-10 transition">
                                <svg class="doodle-icon w-5 h-5 mr-3" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="12" cy="12" r="10"/>
                                    <text x="12" y="16" text-anchor="middle" font-size="10" fill="#fff" font-family="Arial" dy=".3em">C</text>
                                </svg>
                                <span class="sidebar-link-text">Customize Coaches</span>
                            </a>
                        </li>
                        <li>
                            <a href="../blog.php" target="_blank" class="sidebar-link flex items-center px-4 py-3 rounded-lg hover:bg-sage-green hover:bg-opacity-10 transition font-semibold text-sage-green">
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
        <main class="flex-1 ml-64 bg-gray-50 min-h-screen">
            <div class="max-w-4xl mx-auto py-12 px-4">
                <header class="mb-10">
                    <h1 class="text-3xl font-bold text-[#FF5E78] mb-2" style="font-family: 'Pacifico', cursive;">Weekly Post Editor</h1>
                    <p class="text-gray-500">Add, edit, or delete your weekly featured posts here.</p>
                </header>
                 <?php if (isset($_GET['success']) || isset($_GET['deleted'])): ?>
                    <div id="toast-notification" class="fixed top-8 right-8 z-50 flex items-center px-6 py-4 rounded-lg shadow-lg text-white font-semibold transition-all duration-500
                        <?php echo isset($_GET['success']) ? 'bg-[#2EC4B6]' : 'bg-[#FF5E78]'; ?>">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <?php if (isset($_GET['success'])): ?>
                                <circle cx="12" cy="12" r="10" stroke="#fff" stroke-width="2" fill="none"/>
                                <path d="M9 12l2 2l4-4" stroke="#fff" stroke-width="2" fill="none"/>
                            <?php else: ?>
                                <circle cx="12" cy="12" r="10" stroke="#fff" stroke-width="2" fill="none"/>
                                <path d="M15 9l-6 6M9 9l6 6" stroke="#fff" stroke-width="2" fill="none"/>
                            <?php endif; ?>
                        </svg>
                        <span>
                            <?php if (isset($_GET['success'])): ?>
                                Weekly post saved successfully!
                            <?php else: ?>
                                Weekly post deleted.
                            <?php endif; ?>
                        </span>
                        <button onclick="document.getElementById('toast-notification').style.display='none'" class="ml-4 text-white hover:text-gray-200 focus:outline-none text-xl">&times;</button>
                    </div>
                    <script>
                        setTimeout(function() {
                            var toast = document.getElementById('toast-notification');
                            if (toast) toast.style.display = 'none';
                        }, 3500);
                    </script>
                <?php endif; ?>

                <!-- Add/Edit Form -->
                <form method="POST" action="weekly-post.php" enctype="multipart/form-data" class="bg-white rounded-xl shadow-lg p-8 mb-12">
                    <?php if ($editPost): ?>
                        <input type="hidden" name="edit_post" value="1">
                        <input type="hidden" name="edit_id" value="<?php echo $editPost['id']; ?>">
                    <?php else: ?>
                        <input type="hidden" name="add_post" value="1">
                    <?php endif; ?>
                    <div class="mb-6">
                        <label for="post-title" class="block text-sm font-medium text-gray-700 mb-2">Post Title</label>
                        <input type="text" id="post-title" name="post_title" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF5E78]" placeholder="Enter an inspiring title..." value="<?php echo htmlspecialchars($editPost['title'] ?? ''); ?>" required>
                    </div>
                    <!-- Featured Image Field -->
                    <div class="mb-6">
                      <label for="post-image" class="block text-sm font-medium text-gray-700 mb-2">
                        Featured Image
                      </label>
                      <input
                        type="file"
                        id="post-image"
                        name="post_image"
                        accept="image/*"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#FF5E78] file:text-white hover:file:bg-[#e04d67]"
                        onchange="previewImage(this)"
                      >
                      <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($editPost['image'] ?? ''); ?>">

                      <!-- Full-size Preview -->
                      <img
                        id="imagePreviewFull"
                        src="<?php echo !empty($editPost['image']) ? '../uploads/' . htmlspecialchars($editPost['image']) : ''; ?>"
                        alt="Current Image"
                        class="w-64 mt-3 rounded shadow <?php echo empty($editPost['image']) ? 'hidden' : ''; ?>"
                      >

                      <!-- Thumbnail Preview -->
                      <img
                        id="imagePreviewThumb"
                        src="<?php echo !empty($editPost['image']) ? '../uploads/' . htmlspecialchars($editPost['image']) : ''; ?>"
                        alt="Thumbnail"
                        class="w-16 h-16 mt-3 rounded shadow <?php echo empty($editPost['image']) ? 'hidden' : ''; ?>"
                      >
                    </div>

                    <div class="mb-6">
                        <label for="post-excerpt" class="block text-sm font-medium text-gray-700 mb-2">Short Description</label>
                        <textarea id="post-excerpt" name="post_excerpt" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF5E78]" rows="3" placeholder="Write a short, engaging excerpt..." required><?php echo htmlspecialchars($editPost['excerpt'] ?? ''); ?></textarea>
                    </div>
                    <div class="mb-6">
                        <label for="post-link" class="block text-sm font-medium text-gray-700 mb-2">CTA Link (e.g., Read Post)</label>
                        <input type="url" id="post-link" name="post_link" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF5E78]" placeholder="https://your-link.com" value="<?php echo htmlspecialchars($editPost['link'] ?? ''); ?>" required>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-[#FF5E78] text-white px-6 py-3 rounded-lg font-semibold shadow hover:bg-[#e04d67] transition">
                            <?php echo $editPost ? 'Update Post' : 'Add Weekly Post'; ?>
                        </button>
                        <?php if ($editPost): ?>
                            <a href="weekly-post.php" class="ml-4 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold shadow hover:bg-gray-300 transition">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>

                <!-- All Weekly Posts Preview with Edit/Delete -->
                <div class="mt-12">
                    <h2 class="text-xl font-bold mb-4 text-[#2EC4B6]">All Weekly Posts</h2>
                    <div class="space-y-6">
                        <?php while ($post = $weeklyPosts->fetch_assoc()): ?>
                        <div class="bg-[#FFF6F8] rounded-lg shadow p-6 flex flex-col md:flex-row items-center gap-6">
                            <?php if (!empty($post['image'])): ?>
                                <img src="../uploads/<?php echo htmlspecialchars($post['image']); ?>" alt="Weekly Post Image" class="w-40 h-40 object-cover rounded-lg shadow">
                            <?php endif; ?>
                            <div class="flex-1">
                                <h3 class="text-2xl font-bold mb-2 flex items-center gap-2" style="font-family: 'Pacifico', cursive;">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                    <?php if ($post['is_weekly'] == 1 && strtolower($post['status']) == 'published'): ?>
                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-[#2EC4B6] text-white"
                                              style="font-family: 'Quicksand', 'Lato', 'Open Sans', Arial, sans-serif; font-size: 1rem;">
                                            Published
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-[#E9C46A] text-white"
                                              style="font-family: 'Quicksand', 'Lato', 'Open Sans', Arial, sans-serif; font-size: 1rem;">
                                            Draft
                                        </span>
                                    <?php endif; ?>
                                </h3>
                                <p class="mb-4 text-gray-700"><?php echo htmlspecialchars($post['excerpt']); ?></p>
                                <div class="flex flex-wrap items-center gap-2">
                                    <?php if (!empty($post['link'])): ?>
                                        <a href="<?php echo htmlspecialchars($post['link']); ?>" class="inline-block px-6 py-3 bg-[#2EC4B6] text-white rounded font-semibold shadow-md hover:bg-[#1fa89c] transition" target="_blank">Read Full Post</a>
                                    <?php endif; ?>
                                    <a href="weekly-post.php?edit=<?php echo $post['id']; ?>" class="px-4 py-2 bg-[#FFD6E0] text-[#FF5E78] rounded font-semibold shadow hover:bg-[#FFB6C1] transition">Edit</a>
                                    <a href="weekly-post.php?delete=<?php echo $post['id']; ?>" onclick="return confirm('Delete this post?')" class="px-4 py-2 bg-[#FF5E78] text-white rounded font-semibold shadow hover:bg-[#e04d67] transition">Delete</a>
                                    <?php if ($post['is_weekly'] == 1 && strtolower($post['status']) == 'published'): ?>
                                        <span class="px-4 py-2 bg-[#2EC4B6] text-white rounded font-semibold shadow text-sm flex items-center cursor-not-allowed opacity-70" title="Currently Published">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
                                            Published
                                        </span>
                                    <?php else: ?>
                                        <a href="weekly-post.php?publish=<?php echo $post['id']; ?>" class="px-4 py-2 bg-[#E9C46A] text-white rounded font-semibold shadow hover:bg-[#d4a94a] transition text-sm flex items-center" title="Publish as This Week's Post">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
                                            Publish
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <?php if (isset($_GET['published'])): ?>
        <div id="toast-notification" class="fixed top-8 right-8 z-50 flex items-center px-6 py-4 rounded-lg shadow-lg text-white font-semibold transition-all duration-500 bg-[#E9C46A]">
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke="#fff" stroke-width="2" fill="none"/>
                <path d="M9 12l2 2l4-4" stroke="#fff" stroke-width="2" fill="none"/>
            </svg>
            <span>Post published as this week's featured post!</span>
            <button onclick="document.getElementById('toast-notification').style.display='none'" class="ml-4 text-white hover:text-gray-200 focus:outline-none text-xl">&times;</button>
        </div>
        <script>
            setTimeout(function() {
                var toast = document.getElementById('toast-notification');
                if (toast) toast.style.display = 'none';
            }, 3500);
        </script>
    <?php endif; ?>
</body>
</html>