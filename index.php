<?php

session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: public-login.php');
    exit;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// If APCu isn’t installed, define dummy functions so calls won’t error:
if (!function_exists('apcu_exists')) {
    function apcu_exists(string $key): bool { return false; }
}
if (!function_exists('apcu_fetch')) {
    function apcu_fetch(string $key) { return null; }
}
if (!function_exists('apcu_store')) {
    function apcu_store(string $key, $var, int $ttl = 0): bool { return false; }
}

require_once __DIR__ . '/private/includes/meta.php';
require_once __DIR__ . '/private/includes/db.php';

if (!$db) {
    log_error('DB Connection failed: ' . mysqli_connect_error());
    $errors['db'] = "Sorry, we couldn't connect to the database. Please try again later.";
}

// Fetch ONLY the published weekly post (is_weekly=1 AND status='Published'), LIMIT 1
$weeklyPost = $db->query("SELECT * FROM posts WHERE is_weekly=1 AND status='Published' ORDER BY created_at DESC LIMIT 1")->fetch_assoc();

// Fetch this week's podcast segment (latest published or scheduled)
$weeklySegment = $db->query("SELECT * FROM podcasts WHERE status IN ('Published','Scheduled') ORDER BY publish_date DESC, created_at DESC LIMIT 1")->fetch_assoc();

// Fetch random wisdom feed (6 random posts or quotes) with APCu cache
$cacheKey = 'wisdom_feed';
$wisdomFeedArr = [];
if (
    extension_loaded('apcu') &&
    function_exists('apcu_exists') &&
    function_exists('apcu_fetch')
) {
    if (apcu_exists($cacheKey)) {
        $wisdomFeedArr = apcu_fetch($cacheKey);
    } else {
        $wisdomFeed = $db->query("SELECT * FROM posts WHERE status='Published' ORDER BY RAND() LIMIT 6");
        if ($wisdomFeed) {
            while ($row = $wisdomFeed->fetch_assoc()) {
                $wisdomFeedArr[] = $row;
            }
        }
        if (function_exists('apcu_store')) {
            apcu_store($cacheKey, $wisdomFeedArr, 300); // 5 min
        }
    }
} else {
    $wisdomFeed = $db->query("SELECT * FROM posts WHERE status='Published' ORDER BY RAND() LIMIT 6");
    if ($wisdomFeed) {
        while ($row = $wisdomFeed->fetch_assoc()) {
            $wisdomFeedArr[] = $row;
        }
    }
}

$pageTitle = 'Lets Get Unstuck by Traci Edwards'; // Customize per page
$pageDescription = 'Weekly reflections and empowerment from Traci Edwards.'; // Customize per page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php generateMetaTags($pageTitle, $pageDescription); ?>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/heart-behind-this.css">
    <?php require_once __DIR__ . '/private/includes/header.php'; ?>
    <!-- Main Content Area -->
    <main id="main-content" class="page-container notebook relative p-10 rounded-lg shadow-lg mb-16 bg-white max-w-3xl mx-auto">
        <!-- Hero Section -->
        <section class="mb-16">
            <p class="hero-subtitle typewriter text-xl md:text-2xl text-gray-700 mb-8">Stories, voices, and moments that moved me — maybe they'll move you too.</p>
            <div class="flex justify-center mb-8">
                <img src="assets/thumb_no bg edit.png" alt="traci edwards" class="rounded-lg shadow-lg w-40 h-40 object-cover border-4 border-[#f3f3f3]">
            </div>
            <blockquote class="quote-card bg-[#2EC4B6] text-white rounded-lg p-6 text-center mx-auto max-w-xl">
                <p class="handwritten-casual text-lg">"Sometimes all it takes is one voice to change everything."</p>
            </blockquote>
        </section>

        <!-- SVG Divider -->
        <div class="divider">
        </div>

        <!-- Featured This Week Section -->
        <section class="mb-16">
            <h2 class="handwritten-alt text-3xl text-[#FF6F61] mb-6">This Week's Post</h2>
            <?php if ($weeklyPost): ?>
            <div class="sticky-note bg-[#FF5E78] text-white mb-12 max-w-lg mx-auto rotate-in">
                <h3 class="handwritten text-xl mb-2"><?php echo htmlspecialchars($weeklyPost['title']); ?></h3>
                <p class="typewriter mb-4"><?php echo htmlspecialchars($weeklyPost['excerpt']); ?></p>
                <?php if (!empty($weeklyPost['link'])): ?>
                <a href="<?php echo htmlspecialchars($weeklyPost['link']); ?>" target="_blank" rel="noopener" class="bg-white text-[#FF5E78] px-4 py-2 rounded-md handwritten">Read</a>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="sticky-note no-dark bg-[#FF5E78] text-white mb-12 max-w-lg mx-auto rotate-in">
                <p class="text-white">No featured post this week yet. Check back soon!</p>
            </div>
            <?php endif; ?>

            <div class="flex flex-col items-center">
                <div class="bg-[#83A2C3] p-4 rounded-md shadow mb-2 w-full max-w-md">
                    <h3 class="handwritten-alt text-2xl text-white mb-2">This Week's Segment</h3>
                    <?php if ($weeklySegment): ?>
                    <div class="podcast-player bg-white p-4 rounded-md mb-4 flex items-center">
                        <button class="custom-play-btn mr-4" id="playPauseBtn" aria-label="Play/Pause Preview">
                            <svg id="playIcon" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <polygon points="6,4 16,10 6,16" />
                            </svg>
                            <svg id="pauseIcon" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20" style="display:none;">
                                <rect x="5" y="4" width="4" height="12"/>
                                <rect x="11" y="4" width="4" height="12"/>
                            </svg>
                        </button>
                        <audio id="segmentAudio" src="../private/<?php echo htmlspecialchars($weeklySegment['audio']); ?>"></audio>
                        <div>
                            <h4 class="handwritten text-lg text-[#83A2C3]"><?php echo htmlspecialchars($weeklySegment['title']); ?></h4>
                            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($weeklySegment['coach']); ?></p>
                        </div>
                    </div>
                    <a href="<?php echo htmlspecialchars($weeklySegment['link']); ?>" target="_blank" rel="noopener" class="bg-white text-[#83A2C3] px-4 py-2 rounded-md shadow-md handwritten">Listen Now</a>
                    <?php else: ?>
                    <div class="text-white">No segment for this week yet.</div>
                    <?php endif; ?>
                </div>
                <?php if ($weeklySegment): ?>
                <p class="handwritten text-center mt-2 text-[#83A2C3]"><?php echo htmlspecialchars($weeklySegment['note']); ?></p>
                <?php endif; ?>
            </div>
        </section>

        <!-- SVG Divider -->
        <div class="divider">
        </div>
        <!-- Random Wisdom Feed section removed -->
    </main>
    <?php
    require_once __DIR__ . '/private/includes/footer.php';
    ?>
<script>
    // Play/Pause preview for this week's segment
    const playPauseBtn = document.getElementById('playPauseBtn');
    const segmentAudio = document.getElementById('segmentAudio');
    const playIcon = document.getElementById('playIcon');
    const pauseIcon = document.getElementById('pauseIcon');
    if (playPauseBtn && segmentAudio && playIcon && pauseIcon) {
        playPauseBtn.addEventListener('click', function() {
            if (segmentAudio.paused) {
                segmentAudio.play();
                playIcon.style.display = 'none';
                pauseIcon.style.display = 'inline';
            } else {
                segmentAudio.pause();
                playIcon.style.display = 'inline';
                pauseIcon.style.display = 'none';
            }
        });
        segmentAudio.addEventListener('ended', function() {
            playIcon.style.display = 'inline';
            pauseIcon.style.display = 'none';
        });
    }
</script>
</html>