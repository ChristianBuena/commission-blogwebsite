<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: public-login.php');
    exit;
}

require_once __DIR__ . '/private/includes/meta.php';
require_once __DIR__ . '/private/includes/db.php';

$pageTitle = 'Lets Get Unstuck by Traci Edwards';
$pageDescription = 'Weekly reflections and empowerment from Traci Edwards.';
$coaches = $db->query("SELECT * FROM coaches ORDER BY sort_order ASC, id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php generateMetaTags($pageTitle, $pageDescription); ?>
    <link rel="stylesheet" href="css/the-coaches.css">
    <?php require_once __DIR__ . '/private/includes/header.php'; ?>
    <!-- Main Content Area -->
    <main class="page-container">
        <!-- Coaches Page Content -->
        <div class="relative bg-white p-8 overflow-hidden">
            <div class="tape tape-top-left bg-[#2EC4B6] opacity-50"></div>
            <div class="tape tape-bottom-right bg-[#FF5E78] opacity-50"></div>
            
            <h1 class="hero-title handwritten-bold text-5xl md:text-6xl text-[#FF5E78] mb-6">The Coaches</h1>
            <p class="hero-subtitle handwritten-casual text-xl md:text-2xl text-[#2EC4B6] mb-12">These are the voices that helped me get unstuck. Their words became my compass when I felt lost. Maybe they'll speak to you too.</p>
            
            <!-- Introduction Note -->
            <div class="sticky-note bg-[#E9C46A] text-white mb-12 max-w-lg mx-auto rotate-in">
                <p class="handwritten-alt text-xl">"I've collected these coaches like treasures. Each one offered something I needed exactly when I needed it. I hope you find what you need here too."</p>
                <p class="handwritten text-right mt-2">- Traci</p>
                <div class="doodle-heart"></div>
            </div>
            
            <!-- Coaches Gallery -->
            <div class="staggered-layout">
                <?php while ($coach = $coaches->fetch_assoc()): ?>
                <div class="coach-card float-in" style="animation-delay: 0.1s;">
                    <div class="flex flex-col md:flex-row gap-6">
                        <!-- Coach Image -->
                        <div class="md:w-1/3">
                            <div class="polaroid-frame" style="background:<?php echo htmlspecialchars($coach['color']); ?>;">
                                <div class="polaroid-image" aria-label="Polaroid of coach <?php echo htmlspecialchars($coach['name']); ?>">
                                    <?php
    $imageFile = htmlspecialchars($coach['image']);
    $imagePath = __DIR__ . "/coaches-uploads/" . $imageFile;
    $imageUrl = "coaches-uploads/" . $imageFile; // Remove leading slash
    $defaultImage = "assets/default-coach.png";
?>
<img 
    src="<?php echo file_exists($imagePath) && !empty($imageFile) ? $imageUrl : $defaultImage; ?>" 
    alt="<?php echo htmlspecialchars($coach['name']); ?>" 
    class="w-24 h-24 object-cover rounded shadow" 
/>
                                </div>
                                <div class="polaroid-caption">
                                    <p class="handwritten"><?php echo htmlspecialchars($coach['name']); ?></p>
                                </div>
                            </div>
                        </div>
                        <!-- Coach Details -->
                        <div class="md:w-2/3">
                            <div class="note-paper">
                                <div class="note-paper-content">
                                    <h2 class="handwritten-alt text-3xl mb-3" style="color:<?php echo htmlspecialchars($coach['color']); ?>;"><?php echo htmlspecialchars($coach['name']); ?></h2>
                                    <?php if ($coach['episode_title']): ?>
                                        <h3 class="typewriter text-xl text-gray-700 mb-4"><?php echo htmlspecialchars($coach['episode_title']); ?></h3>
                                    <?php endif; ?>
                                    <div class="mb-6">
                                        <p class="serif text-lg mb-4"><?php echo nl2br(htmlspecialchars($coach['description'])); ?></p>
                                        <?php if ($coach['highlight']): ?>
                                            <p class="handwritten-casual text-xl mt-6" style="color:<?php echo htmlspecialchars($coach['color']); ?>;"><?php echo htmlspecialchars($coach['highlight']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($coach['episode_link']): ?>
                                    <div class="flex justify-start mt-6">
                                        <a href="<?php echo htmlspecialchars($coach['episode_link']); ?>" target="_blank" class="btn-listen text-white shadow-md" style="background:<?php echo htmlspecialchars($coach['color']); ?>;">
                                            <span class="flex items-center">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Listen to Segment
                                            </span>
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            
            <!-- Final Note -->
            <div class="sticky-note bg-[#FF5E78] text-white mb-12 max-w-lg mx-auto mt-16 rotate-in">
                <p class="handwritten-alt text-xl">"These coaches didn't just give me advice—they gave me permission to think differently about my struggles. Sometimes that's all we need to get unstuck."</p>
                <p class="handwritten text-right mt-2">- Traci</p>
                <div class="doodle-heart"></div>
            </div>
            
            <!-- Decorative Elements -->
            <div class="absolute top-10 right-10">
                <div class="doodle-star"></div>
            </div>
            <div class="absolute bottom-10 left-10">
                <div class="doodle-heart"></div>
            </div>
        </div>
        
        <!-- Call to Action -->
        <div class="sticky-note bg-[#2EC4B6] text-white max-w-2xl mx-auto mb-16">
            <h2 class="handwritten-alt text-3xl mb-4">Share Your Favorite Coach</h2>
            <p class="typewriter mb-6">Who's helped you get unstuck? I'd love to hear about the voices that have guided you through tough times.</p>
            <div class="flex justify-center">
                <a href="share-story.php" class="bg-white text-[#2EC4B6] px-6 py-3 rounded-md shadow-md handwritten-casual text-xl">Share Your Story</a>
        </div>
    </main>
    <?php
    require_once __DIR__ . '/private/includes/footer.php';
    ?>

    <script>
        // Animation for elements when they come into view
        document.addEventListener('DOMContentLoaded', function() {
            // Function to check if element is in viewport
            function isInViewport(element) {
                const rect = element.getBoundingClientRect();
                return (
                    rect.top >= 0 &&
                    rect.left >= 0 &&
                    rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                    rect.right <= (window.innerWidth || document.documentElement.clientWidth)
                );
            }
            
            // Get all elements to animate
            const animateElements = document.querySelectorAll('.float-in, .rotate-in');
            
            // Initial check for elements in viewport
            animateElements.forEach(element => {
                if (isInViewport(element)) {
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }
            });
            
            // Check on scroll
            window.addEventListener('scroll', function() {
                animateElements.forEach(element => {
                    if (isInViewport(element) && element.style.opacity !== '1') {
                        element.style.opacity = '1';
                        element.style.transform = 'translateY(0)';
                    }
                });
            });
            
            // Add hover effects to coach cards
            const coachCards = document.querySelectorAll('.coach-card');
            coachCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px) rotate(0)';
                });
                
                card.addEventListener('mouseleave', function() {
                    if (this.classList.contains('coach-card:nth-child(odd)')) {
                        this.style.transform = 'rotate(1deg)';
                    } else {
                        this.style.transform = 'rotate(-1deg)';
                    }
                });
            });
        });
    </script>
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'94bec8c5456cd98b',t:'MTc0OTI4NDk5My4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script>
<script>
    const darkModeToggle = document.getElementById('darkModeToggle');
    const body = document.body;

    // Check localStorage for dark mode preference
    if (localStorage.getItem('darkMode') === 'enabled') {
        body.classList.add('dark-mode');
        darkModeToggle.textContent = '☀️';
    }

    // Toggle dark mode on click
    darkModeToggle.addEventListener('click', function () {
        body.classList.toggle('dark-mode');
        if (body.classList.contains('dark-mode')) {
            localStorage.setItem('darkMode', 'enabled');
            darkModeToggle.textContent = '☀️';
        } else {
            localStorage.setItem('darkMode', 'disabled');
            darkModeToggle.textContent = '🌙';
        }
    });
</script>
</body>
</html>
