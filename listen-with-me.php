<?php
$pageTitle = 'Listen With Me | Get Unstuck';
$pageDescription = 'Curated podcast segments and recommendations from Traci Edwards to help you get unstuck.';
require_once __DIR__ . '/../private/includes/meta.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php generateMetaTags($pageTitle, $pageDescription); ?>
    <link rel="stylesheet" href="css/blog.css">
</head>
<body>
    <main class="page-container">
        <h1 class="hero-title handwritten-bold text-5xl text-[#2EC4B6] mb-6">Listen With Me</h1>
        <p class="handwritten-casual text-xl text-[#FF5E78] mb-8">Podcasts that helped me get unstuck—maybe they’ll help you too.</p>
        <ul class="list-disc pl-8 mb-8">
            <li><a href="https://podcast.example.com/episode1" target="_blank" class="text-[#2EC4B6] underline">Episode 1: Finding Your Voice</a></li>
            <li><a href="https://podcast.example.com/episode2" target="_blank" class="text-[#2EC4B6] underline">Episode 2: Letting Go of Perfection</a></li>
        </ul>
        <div class="sticky-note bg-[#2EC4B6] text-white max-w-lg mx-auto rotate-in">
            <p class="handwritten-alt text-xl">"Sometimes, all you need is to hear the right words at the right time."</p>
            <p class="handwritten text-right mt-2">- Traci</p>
        </div>
    </main>
</body>
</html>