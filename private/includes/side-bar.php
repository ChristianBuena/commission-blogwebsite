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
                        $unread = $db->query("SELECT COUNT(*) as unread FROM submissions WHERE status='pending'");
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
                    <a href="../public/index.php" target="_blank" class="sidebar-link flex items-center px-4 py-3 rounded-lg hover:bg-sage-green hover:bg-opacity-10 transition font-semibold text-sage-green">
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