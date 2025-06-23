<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: public-login.php');
    exit;
}

require_once __DIR__ . '/private/includes/meta.php';
require_once __DIR__ . '/private/includes/db.php';

$pageTitle = 'Lets Get Unstuck by Traci Edwards'; // Customize per page
$pageDescription = 'Weekly reflections and empowerment from Traci Edwards.'; // Customize per page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php generateMetaTags($pageTitle, $pageDescription); ?>
    <link rel="stylesheet" href="css/heart-behind-this.css">
    <?php require_once __DIR__ . '/private/includes/header.php'; ?>
    <!-- Main Content Area -->
    <main class="page-container shadow-lg relative p-10 rounded-lg mb-16 bg-white max-w-3xl mx-auto">
    <!-- About Page Content -->
    <div class="relative bg-white p-12 rounded-lg mb-20 max-w-3xl mx-auto">
            <!-- Remove tapes and overlapping elements -->
            <h1 class="hero-title handwritten-bold text-5xl md:text-6xl text-[#FF5E78] mb-6">The Heart Behind This</h1>
            
            <!-- Main Content Section with Photo and Journal -->
            <div class="flex flex-col md:flex-row gap-12 mb-16">
                <!-- Photo Section -->
                <div class="md:w-1/3 flex flex-col items-center">
                    <div class="photo-frame mb-6">
                        <img src="assets/client p1.jpg" alt="traci edwards" class="rounded-lg shadow-lg w-48 h-48 object-cover">
                    </div>
                    <div class="bg-[#E9C46A] text-white rounded-lg p-4 mt-4">
                        <p class="handwritten-casual text-lg text-center">"The journey to getting unstuck starts with one small step."</p>
                    </div>
                </div>
                <!-- Journal Section -->
                <div class="md:w-2/3">
                    <h2 class="handwritten-alt text-3xl text-[#2EC4B6] mb-6">My Story</h2>
                    <p class="serif text-lg mb-6">I was stuck. Then I heard a podcast with <span class="marker-underline">Mel Robbins</span> and <span class="marker-underline marker-underline-teal">Jay Shetty</span> and I had an epiphany. I knew I had to create a blog where I could process my struggles — and share what helped me.</p>
                    <p class="serif text-lg mb-6">For years, I felt like I was just going through the motions. Wake up, work, scroll through social media, sleep, repeat. I was living, but not really <span class="marker-underline marker-underline-mustard">alive</span>.</p>
                    <p class="serif text-lg mb-6">Then one day, stuck in traffic and feeling particularly lost, I randomly played a podcast that changed everything. The voices of these coaches spoke directly to what I was feeling, and for the first time in years, I felt <span class="marker-underline">understood</span>.</p>
                    <p class="handwritten-casual text-xl text-[#FF5E78]">This space is my way of processing what I learn and sharing it with others who might be feeling just as stuck as I was.</p>
                </div>
            </div>
            
            <!-- Sticky Note Quote Section -->
            <div class="sticky-note bg-[#FF5E78] text-white mb-12 max-w-lg mx-auto rotate-in">
                <p class="handwritten-alt text-xl">"I created this space to remind myself — and maybe you too — that we're never really stuck. We just haven't found the right voice to guide us yet."</p>
                <p class="handwritten text-right mt-2">- Traci</p>
                <div class="doodle-heart"></div>
            </div>
            
            <!-- Core Values Section -->
            <h2 class="handwritten-alt text-3xl text-[#2EC4B6] mb-8">What Matters Most</h2>
<div class="grid grid-cols-1 md:grid-cols-3 gap-10 mb-16">
    <!-- Value 1: Empathy -->
    <div class="value-card bg-[#FF5E78] text-white rounded-lg p-6 flex flex-col items-center">
        <h3 class="handwritten-alt text-2xl mb-3">Empathy</h3>
        <p class="typewriter mb-4 text-center">Understanding that everyone's journey is different, but our struggles often connect us.</p>
        <svg class="w-12 h-12 text-white" viewBox="0 0 100 100">
            <path fill="none" stroke="currentColor" stroke-width="4" d="M30,50 C30,30 70,30 70,50 C70,70 30,70 30,50 Z"/>
        </svg>
    </div>
    <!-- Value 2: Vulnerability -->
    <div class="value-card bg-[#2EC4B6] text-white rounded-lg p-6 flex flex-col items-center">
        <h3 class="handwritten-alt text-2xl mb-3">Vulnerability</h3>
        <p class="typewriter mb-4 text-center">Sharing the real stuff, even when it's scary, because that's where growth happens.</p>
        <svg class="w-12 h-12 text-white" viewBox="0 0 100 100">
            <path fill="none" stroke="currentColor" stroke-width="4" d="M50,20 C70,20 80,40 80,50 C80,70 60,80 50,80 C40,80 20,70 20,50 C20,40 30,20 50,20 Z"/>
        </svg>
    </div>
    <!-- Value 3: Realness -->
    <div class="value-card bg-[#E9C46A] text-white rounded-lg p-6 flex flex-col items-center">
        <h3 class="handwritten-alt text-2xl mb-3">Realness</h3>
        <p class="typewriter mb-4 text-center">No filters, no perfect answers—just honest experiences and what I'm learning along the way.</p>
        <svg class="w-12 h-12 text-white" viewBox="0 0 100 100">
            <path fill="none" stroke="currentColor" stroke-width="4" d="M30,70 L30,30 L70,30 L70,70 Z"/>
        </svg>
    </div>
</div>
            
            <!-- Personal Journey Section -->
            <div class=" bg-white p-8 mb-12 rotate-in">
                <h2 class="handwritten-alt text-3xl text-[#83A2C3] mb-6">The Journey So Far</h2>
                
                <div class="flex flex-col md:flex-row gap-8">
                    <div class="md:w-1/2">
                        <div class="polaroid transform rotate-2">
                            <div class="bg-[#83A2C3] p-4 rounded-md">
                                <h3 class="handwritten text-xl text-white mb-2">Where I Started</h3>
                                <p class="typewriter text-white text-sm mb-3">Feeling stuck in a loop of self-doubt and uncertainty, not knowing how to move forward.</p>
                            </div>
                            <p class="handwritten text-center mt-2">The beginning</p>
                        </div>
                    </div>
                    
                    <div class="md:w-1/2">
                        <div class="polaroid transform -rotate-2">
                            <div class="bg-[#FF6F61] p-4 rounded-md">
                                <h3 class="handwritten text-xl text-white mb-2">Where I'm Going</h3>
                                <p class="typewriter text-white text-sm mb-3">Building a community of people who share their stories and help each other get unstuck.</p>
                            </div>
                            <p class="handwritten text-center mt-2">The vision</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Final Quote -->
            <div class="quote-card bg-[#83A2C3] text-white rounded-lg p-8 my-16 max-w-2xl mx-auto text-center">
                <p class="handwritten-casual text-xl">The most powerful thing we can do is share our stories. They remind us we're not alone in our struggles—and that getting unstuck is always possible.</p>
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
            <h2 class="handwritten-alt text-3xl mb-4">Join Me On This Journey</h2>
            <p class="typewriter mb-6">Every week, I share a new story and a piece of wisdom that helped me through a tough spot. Maybe it'll help you too.</p>
            <div class="flex justify-center">
                <a href="blog.php" class="bg-white text-[#2EC4B6] px-6 py-3 rounded-md shadow-md handwritten-casual text-xl">Read the Latest Post</a>
            </div>
        </div>
    </div>
</main>
    <?php
    require_once __DIR__ . '/private/includes/footer.php';
    ?>

    <script>
        // Simple animation for elements when they come into view
        document.addEventListener('DOMContentLoaded', function() {
            // Add animation classes with delays for staggered effect
            const animateElements = document.querySelectorAll('.float-in, .rotate-in');
            
            animateElements.forEach((element, index) => {
                if (!element.style.animationDelay) {
                    element.style.animationDelay = `${index * 0.2}s`;
                }
            });
        });
    </script>
    <script>
    // Dark mode toggle logic
    const darkModeToggle = document.getElementById('darkModeToggle');
    const body = document.body;

    // Load preference
    if (localStorage.getItem('darkMode') === 'enabled') {
        body.classList.add('dark-mode');
        darkModeToggle.textContent = '☀️';
    }

    darkModeToggle.addEventListener('click', function() {
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
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'94beb950340755fc',t:'MTc0OTI4NDM2MC4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>
