<?php
require_once __DIR__ . '/../private/includes/db.php';

// At top of each admin page
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit;
}

// Handle form submissions for add/edit/delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add or Edit Segment
    if (isset($_POST['action']) && ($_POST['action'] === 'add' || $_POST['action'] === 'edit')) {
        $title = $db->real_escape_string($_POST['segment_title']);
        $coach = $db->real_escape_string($_POST['coach_name']);
        $link = $db->real_escape_string($_POST['episode_link']);
        $note = $db->real_escape_string($_POST['segment_note']);
        $publish_date = $db->real_escape_string($_POST['publish_date']);
        $status = $db->real_escape_string($_POST['status']);
        $audioPath = '';

        // Handle audio upload
        if (isset($_FILES['audio_file']) && $_FILES['audio_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/podcast/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileName = uniqid('audio_', true) . '_' . basename($_FILES['audio_file']['name']);
            $targetFile = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['audio_file']['tmp_name'], $targetFile)) {
                $audioPath = $targetFile;
            }
        } else if (!empty($_POST['existing_audio'])) {
            $audioPath = $db->real_escape_string($_POST['existing_audio']);
        }

        if ($_POST['action'] === 'add') {
            $db->query("INSERT INTO podcasts (title, coach, link, audio, note, publish_date, status, created_at) VALUES ('$title', '$coach', '$link', '$audioPath', '$note', '$publish_date', '$status', NOW())");
        } else if ($_POST['action'] === 'edit' && isset($_POST['segment_id'])) {
            $id = (int)$_POST['segment_id'];
            $audioUpdate = $audioPath ? ", audio='$audioPath'" : "";
            $db->query("UPDATE podcasts SET title='$title', coach='$coach', link='$link', note='$note', publish_date='$publish_date', status='$status' $audioUpdate WHERE id=$id");
        }
        header("Location: podcast.php?success=1");
        exit;
    }

    // Delete Segment
    if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['segment_id'])) {
        $id = (int)$_POST['segment_id'];
        $db->query("DELETE FROM podcasts WHERE id=$id");
        header("Location: podcast.php?deleted=1");
        exit;
    }
}

// Fetch all podcast segments
$segments = $db->query("SELECT * FROM podcasts ORDER BY publish_date DESC, created_at DESC");

// For edit/preview
$editSegment = null;
$previewSegment = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editSegment = $db->query("SELECT * FROM podcasts WHERE id=" . (int)$_GET['edit'])->fetch_assoc();
}
if (isset($_GET['preview']) && is_numeric($_GET['preview'])) {
    $previewSegment = $db->query("SELECT * FROM podcasts WHERE id=" . (int)$_GET['preview'])->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Podcast Segment | Let's Get Unstuck Admin Dashboard</title>
    <link rel="stylesheet" href="css/podcast.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/weekly-post.css">
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
                        <a href="podcast.php" class="sidebar-link active flex items-center px-4 py-3 rounded-lg text-muted-coral font-semibold transition">
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
                        <a href="../index.php" target="_blank" class="sidebar-link flex items-center px-4 py-3 rounded-lg hover:bg-sage-green hover:bg-opacity-10 transition font-semibold text-sage-green">
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
        <div class="max-w-5xl mx-auto py-12 px-4">
            <header class="mb-8">
                <div class="flex justify-between items-center mb-2">
                    <h1 class="text-2xl font-bold" style="font-family: 'Pacifico', cursive;color: #FF5E78;">Podcast Segments</h1>
                    <a href="podcast.php?add=1" class="block mt-4 px-4 py-2 rounded-lg bg-[#FF5E78] text-white text-center font-semibold hover:bg-[#FF6F61] transition">
                        Add New Segment
                    </a>
                </div>
                <p class="text-gray-500">Manage your podcast segments and episodes</p>
            </header>

            <?php if (isset($_GET['add']) || $editSegment): ?>
                <!-- Add/Edit Segment Form -->
                <div class="card p-6 mb-8">
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="<?php echo $editSegment ? 'edit' : 'add'; ?>">
                        <?php if ($editSegment): ?>
                            <input type="hidden" name="segment_id" value="<?php echo $editSegment['id']; ?>">
                        <?php endif; ?>
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Segment Title</label>
                            <input type="text" name="segment_title" class="w-full px-4 py-3 border border-gray-200 rounded-lg" required value="<?php echo htmlspecialchars($editSegment['title'] ?? ''); ?>">
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Coach Name</label>
                            <input type="text" name="coach_name" class="w-full px-4 py-3 border border-gray-200 rounded-lg" required value="<?php echo htmlspecialchars($editSegment['coach'] ?? ''); ?>">
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Episode Link (URL)</label>
                            <input type="url" name="episode_link" class="w-full px-4 py-3 border border-gray-200 rounded-lg" required value="<?php echo htmlspecialchars($editSegment['link'] ?? ''); ?>">
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Audio Preview</label>
                            <?php if (!empty($editSegment['audio'])): ?>
                                <audio controls class="mb-2 w-full">
                                    <source src="<?php echo htmlspecialchars($editSegment['audio']); ?>">
                                    Your browser does not support the audio element.
                                </audio>
                                <input type="hidden" name="existing_audio" value="<?php echo htmlspecialchars($editSegment['audio']); ?>">
                            <?php endif; ?>
                            <input type="file" name="audio_file" accept="audio/*" class="block w-full text-sm text-gray-500">
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Short Note: Why This Matters</label>
                            <textarea name="segment_note" class="w-full px-4 py-3 border border-gray-200 rounded-lg" rows="3" required><?php echo htmlspecialchars($editSegment['note'] ?? ''); ?></textarea>
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Publish Date</label>
                            <input type="date" name="publish_date" class="w-full px-4 py-3 border border-gray-200 rounded-lg" required value="<?php echo htmlspecialchars($editSegment['publish_date'] ?? ''); ?>">
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" class="w-full px-4 py-3 border border-gray-200 rounded-lg">
                                <option value="Draft" <?php if (($editSegment['status'] ?? '') === 'Draft') echo 'selected'; ?>>Draft</option>
                                <option value="Scheduled" <?php if (($editSegment['status'] ?? '') === 'Scheduled') echo 'selected'; ?>>Scheduled</option>
                                <option value="Published" <?php if (($editSegment['status'] ?? '') === 'Published') echo 'selected'; ?>>Published</option>
                            </select>
                        </div>
                        <div class="flex justify-end space-x-2">
                            <button type="submit" name="status" value="Draft" class="btn-outline px-4 py-2 rounded-md">Save as Draft</button>
                            <button type="submit" name="status" value="Scheduled" class="btn-secondary px-4 py-2 rounded-md">Schedule</button>
                            <button type="submit" name="status" value="Published" class="btn-primary px-4 py-2 rounded-md">Publish Now</button>
                        </div>
                    </form>
                </div>
                <a href="podcast.php" class="text-muted-coral hover:underline">&larr; Back to Podcast Segments</a>
            <?php elseif ($previewSegment): ?>
                <!-- Preview Segment -->
                <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
                    <h1 class="text-3xl font-bold mb-4" style="font-family: 'Pacifico', cursive; color: #FF5E78;"><?php echo htmlspecialchars($previewSegment['title']); ?></h1>
                    <div class="flex items-center text-sm text-gray-500 mb-4">
                        <span class="mr-4"><i class="fa fa-user mr-1"></i><?php echo htmlspecialchars($previewSegment['coach']); ?></span>
                        <span class="mr-4"><i class="fa fa-calendar mr-1"></i><?php echo htmlspecialchars($previewSegment['publish_date']); ?></span>
                        <span class="badge badge-<?php echo strtolower($previewSegment['status']); ?> ml-2"><?php echo htmlspecialchars($previewSegment['status']); ?></span>
                    </div>
                    <?php if (!empty($previewSegment['audio'])): ?>
                        <audio controls class="mb-4 w-full">
                            <source src="<?php echo htmlspecialchars($previewSegment['audio']); ?>">
                            Your browser does not support the audio element.
                        </audio>
                    <?php endif; ?>
                    <div class="mb-4">
                        <h4 class="font-medium mb-2">Why This Matters:</h4>
                        <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($previewSegment['note'])); ?></p>
                    </div>
                    <div class="mb-4">
                        <h4 class="font-medium mb-2">Episode Link:</h4>
                        <a href="<?php echo htmlspecialchars($previewSegment['link']); ?>" class="text-blue-600 hover:underline flex items-center" target="_blank">
                            <i class="fa fa-link mr-1"></i>
                            <?php echo htmlspecialchars($previewSegment['link']); ?>
                        </a>
                    </div>
                    <a href="podcast.php" class="text-muted-coral hover:underline">&larr; Back to Podcast Segments</a>
                </div>
            <?php else: ?>
                <!-- Podcast Segments Table -->
                <div class="card p-6 mb-8">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs uppercase tracking-wider">Segment Title</th>
                                    <th class="px-6 py-3 text-left text-xs uppercase tracking-wider">Coach Name</th>
                                    <th class="px-6 py-3 text-left text-xs uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                <?php while ($row = $segments->fetch_assoc()): ?>
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium"><?php echo htmlspecialchars($row['title']); ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm"><?php echo htmlspecialchars($row['coach']); ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($row['publish_date']); ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="badge badge-<?php echo strtolower($row['status']); ?>"><?php echo htmlspecialchars($row['status']); ?></span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium">
                                        <a href="podcast.php?edit=<?php echo $row['id']; ?>" class="text-gray-500 hover:text-gray-700 mr-3">Edit</a>
                                        <form method="post" action="podcast.php" style="display:inline;" onsubmit="return confirm('Delete this segment?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="segment_id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" class="text-gray-500 hover:text-red-600 mr-3" style="background:none;border:none;padding:0;cursor:pointer;">Delete</button>
                                        </form>
                                        <a href="podcast.php?preview=<?php echo $row['id']; ?>" class="text-gray-500 hover:text-gray-700">Preview</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>
