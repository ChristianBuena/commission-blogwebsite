<?php
ob_start();
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: public-login.php');
    exit;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/private/includes/db.php';
require_once __DIR__ . '/private/includes/meta.php';
$pageTitle = 'Lets Get Unstuck by Traci Edwards';
$pageDescription = 'Weekly reflections and empowerment from Traci Edwards.';

$success = false;
$error = '';

// Handle form submission with POST-Redirect-GET pattern
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['share_story'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $story = trim($_POST['story'] ?? '');

    if ($name && $email && $story) {
        $stmt = $db->prepare("INSERT INTO submissions (name, email, content, status, created_at) VALUES (?, ?, ?, 'unread', NOW())");
        $stmt->bind_param('sss', $name, $email, $story);
        if ($stmt->execute()) {
            // --- Add notification for admin ---
            $notifMsg = "$name submitted a new story.";
            $notifLink = "reader-submission.php";
            $notifStmt = $db->prepare("INSERT INTO notifications (message, link, is_read, created_at) VALUES (?, ?, 0, NOW())");
            $notifStmt->bind_param('ss', $notifMsg, $notifLink);
            $notifStmt->execute();
            $notifStmt->close();
            // --- End notification ---

            $_SESSION['share_story_success'] = true;
            session_write_close();
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        } else {
            $error = "Sorry, there was a problem saving your story. Please try again.";
        }
        $stmt->close();
    } else {
        $error = "Please fill in all fields.";
    }
}

// Check for success message in session (after redirect)
if (isset($_SESSION['share_story_success']) && $_SESSION['share_story_success']) {
    $success = true;
    unset($_SESSION['share_story_success']);
}

// Fetch featured and approved stories only
$featured = $db->query("SELECT * FROM submissions WHERE featured=1 AND status='approved' ORDER BY created_at DESC LIMIT 3");
$recent = $db->query("SELECT * FROM submissions WHERE featured=0 AND status='approved' ORDER BY created_at DESC LIMIT 4");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php generateMetaTags($pageTitle, $pageDescription); ?>
    <link rel="stylesheet" href="css/share-story.css">
    <?php require_once __DIR__ . '/private/includes/header.php'; ?>
    <!-- Main Content Area -->
    <main class="page-container py-8">
        <!-- Pink Sticky Note Call to Action -->
        <div class="sticky-note bg-[#FF5E78] text-white max-w-2xl mx-auto mb-12 shadow-lg" style="transform: rotate(-2deg);">
            <p class="handwritten-alt text-2xl md:text-3xl text-center leading-snug">
                Your voice matters.<br>
                What do you need to release, say, or express to help you <span class="semi-handwritten-bold" style="color:#ffff;">Let's Get Unstuck</span>?
            </p>
        </div>
        <!-- Share Your Story Content -->
        <div class="relative bg-white p-8 rounded-lg shadow-lg mb-16 overflow-hidden">
            <div class="tape tape-top-left bg-[#2EC4B6] opacity-50"></div>
            <div class="tape tape-bottom-right bg-[#FF5E78] opacity-50"></div>
            <p class="hero-subtitle handwritten-casual text-xl md:text-2xl text-[#2EC4B6] mb-12 mt-8 max-w-3xl">Your experiences, discoveries, and breakthroughs might be exactly what someone else needs to hear right now.</p>
            
            <!-- Introduction -->
            <div class="max-w-3xl mx-auto mb-12">
                <div class="polaroid float-in" style="animation-delay: 0.1s;">
                    <div class="relative z-10 p-4 text-center">
                        <p class="handwritten-alt text-xl text-[#FF6F61]">"I want to invite others to submit quotes, episodes, or meditations that moved them. The things that helped you get unstuck might be exactly what someone else needs to hear."</p>
                        <p class="handwritten text-right mt-4">- Traci</p>
                    </div>
                    <div class="polaroid-caption">From my heart to yours</div>
                </div>
            </div>
            
            <!-- Polaroid Stack - Visual Element -->
            <div class="polaroid-stack mb-16 hidden md:block">
                <?php
                // Dynamically show up to 3 featured stories as floating polaroids
                if ($featured && $featured->num_rows > 0):
                    $floatClasses = ['floating', 'floating-alt', 'floating'];
                    $i = 0;
                    while ($row = $featured->fetch_assoc()):
                ?>
                    <div class="polaroid-item <?php echo $floatClasses[$i % count($floatClasses)]; ?>"<?php if($i==2) echo ' style="animation-delay: 1s;"'; ?>>
                        <p class="handwritten-casual text-lg text-[#FF5E78]"><?php echo htmlspecialchars($row['content']); ?></p>
                        <div class="polaroid-caption">- <?php echo htmlspecialchars($row['name']); ?></div>
                    </div>
                <?php
                    $i++;
                    endwhile;
                else:
                ?>
                <div class="col-span-2 text-center text-gray-400">featured wall</div>
                <?php endif; ?>
            </div>
            
            <!-- Form Container -->
            <div class="notebook relative mb-16 rotate-in">
                <!-- Spiral Binding -->
                <div class="spiral-binding">
                    <div class="spiral-loop"></div>
                    <div class="spiral-loop"></div>
                    <div class="spiral-loop"></div>
                    <div class="spiral-loop"></div>
                    <div class="spiral-loop"></div>
                    <div class="spiral-loop"></div>
                    <div class="spiral-loop"></div>
                    <div class="spiral-loop"></div>
                </div>
                
                <!-- Coffee Stain -->
                <div class="coffee-stain coffee-stain-1"></div>
                
                <!-- Doodle -->
                <div class="doodle-1">★</div>
                
                <!-- Form Content -->
                <div class="notebook-content pl-6">
                    <h2 class="handwritten-bold text-3xl text-[#2EC4B6] mb-6">Tell Me Your Story</h2>

                    <?php if ($success): ?>
                        <div id="success-message" class="success-message text-green-600 mb-4 text-lg font-semibold text-center">
                            Thank you for sharing your story! 💖<br>
                            Your submission has been received.
                        </div>
                    <?php else: ?>
                        <?php if ($error): ?>
                            <div class="error-message text-red-500 mb-4"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <form id="share-story-form" class="mb-8" method="POST" action="">
                            <div class="mb-6">
                                <label for="name" class="form-label">Your Name</label>
                                <input type="text" id="name" name="name" class="form-input" placeholder="What should I call you?" required>
                            </div>
                            <div class="mb-6">
                                <label for="email" class="form-label">Your Email</label>
                                <input type="email" id="email" name="email" class="form-input" placeholder="Where can I reach you?" required>
                            </div>
                            <div class="mb-6">
                                <label for="story" class="form-label">Tell me what moved you and why</label>
                                <textarea id="story" name="story" class="form-input form-textarea" placeholder="Was it a quote, podcast episode, meditation, or something else? How did it help you get unstuck?" required></textarea>
                            </div>
                            <div class="flex justify-center">
                                <button type="submit" name="share_story" class="btn-submit bg-[#FF5E78] text-white shadow-md px-8 py-3">
                                    <span class="flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                        </svg>
                                        Send Your Story
                                    </span>
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Sticky Note -->
            <div class="sticky-note bg-[#E9C46A] text-white max-w-lg mx-auto mb-16 float-in" style="animation-delay: 0.3s;">
                <h3 class="handwritten-alt text-2xl mb-4">Your story might help someone else let's get unstuck.</h3>
                <p class="handwritten-casual text-lg">We're all on this journey together. The quote that changed everything for you, the podcast that made you see things differently, or the simple practice that helped you break through—these could be exactly what someone else needs right now.</p>
                <div class="corner-fold"></div>
                <div class="doodle-heart"></div>
            </div>
            
            <!-- Featured Stories -->
            <div class="mb-16">
                <h2 class="handwritten-bold text-3xl text-[#FF6F61] mb-8 text-center">Stories That Moved Us</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <?php
                    // Show featured first, then recent if needed
                    $stories = [];
                    if ($featured->num_rows > 0) {
                        while ($row = $featured->fetch_assoc()) $stories[] = $row;
                    }
                    if (isset($recent) && $recent->num_rows > 0) {
                        while ($row = $recent->fetch_assoc()) $stories[] = $row;
                    }
                    foreach ($stories as $i => $story):
                    ?>
                    <div class="note-paper relative float-in" style="animation-delay: <?php echo 0.4 + $i * 0.1; ?>s;">
                        <div class="note-paper-content">
                            <p class="handwritten-casual text-lg mb-4"><?php echo nl2br(htmlspecialchars($story['content'])); ?></p>
                            <p class="handwritten-neat text-right">- <?php echo htmlspecialchars($story['name']); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
<div class="col-span-2 text-center text-gray-400">No stories have been approved yet. Yours could be the first!</div>                    <?php if (empty($stories)): ?>
                        
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Final Note -->
            <div class="sticky-note bg-[#2EC4B6] text-white max-w-lg mx-auto mb-8 float-in" style="animation-delay: 0.8s;">
                <p class="handwritten-alt text-xl">"Every story shared here is a little beacon of light for someone else who might be feeling stuck in the dark. Thank you for being brave enough to share yours."</p>
                <p class="handwritten text-right mt-2">- Traci</p>
                <div class="doodle-star"></div>
            </div>
            
            <!-- Confetti Container for Success Animation -->
            <div id="confetti-container" class="confetti-container"></div>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                <?php if ($success): ?>
                    if (typeof showConfetti === 'function') showConfetti();
                <?php endif; ?>
            });
            </script>
        </div>
    </main>
    <?php
    require_once __DIR__ . '/private/includes/footer.php';
    ?>

    <script>
        // Confetti function (used after successful submission)
        function showConfetti() {
            var confettiContainer = document.getElementById('confetti-container');
            confettiContainer.style.display = 'block';
            const colors = ['#FF5E78', '#2EC4B6', '#E9C46A', '#83A2C3', '#9D8DF1'];
            for (let i = 0; i < 100; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.width = Math.random() * 10 + 5 + 'px';
                confetti.style.height = Math.random() * 10 + 5 + 'px';
                confetti.style.opacity = Math.random() + 0.5;
                confetti.style.animationDuration = Math.random() * 3 + 2 + 's';
                confettiContainer.appendChild(confetti);
            }
            setTimeout(function() {
                confettiContainer.style.display = 'none';
                confettiContainer.innerHTML = '';
            }, 5000);
        }

        // Animation for elements when they come into view
        document.addEventListener('DOMContentLoaded', function() {
            const animateElements = document.querySelectorAll('.float-in, .rotate-in');
            function isInViewport(element) {
                const rect = element.getBoundingClientRect();
                return (
                    rect.top >= 0 &&
                    rect.left >= 0 &&
                    rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                    rect.right <= (window.innerWidth || document.documentElement.clientWidth)
                );
            }
            animateElements.forEach(element => {
                if (isInViewport(element)) {
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }
            });
            window.addEventListener('scroll', function() {
                animateElements.forEach(element => {
                    if (isInViewport(element) && element.style.opacity !== '1') {
                        element.style.opacity = '1';
                        element.style.transform = 'translateY(0)';
                    }
                });
            });
        });
    </script>
</html>
