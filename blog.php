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


// Fetch published posts from weekly-post.php admin
$posts = [];
$result = $db->query("SELECT title, excerpt, link, image, published_at FROM posts WHERE status='Published' ORDER BY created_at DESC");
while ($row = $result->fetch_assoc()) {
    $posts[] = [
        'title' => $row['title'],
        'excerpt' => $row['excerpt'],
        'link' => $row['link'],
        'image' => $row['image'],
        'date' => $row['published_at']
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php generateMetaTags($pageTitle, $pageDescription); ?>
    <link rel="stylesheet" href="css/blog.css">
    <?php require_once __DIR__ . '/private/includes/header.php'; ?>
<main class="page-container">
    <div class="relative bg-white p-8 mb-16 overflow-hidden">
        <div class="tape tape-top-left bg-[#2EC4B6] opacity-50"></div>
        <div class="tape tape-bottom-right bg-[#FF5E78] opacity-50"></div>
        <h3 class="hero-title handwritten-bold text-5xl md:text-6xl text-[#FF5E78] mb-6">Weekly Post</h3>
        <p class="promise-statement handwritten-casual text-xl md:text-2xl text-[#2EC4B6] mb-4 max-w-2xl mx-auto text-center"></p>
        <div class="sticky-note bg-[#FF5E78] text-white mb-12 max-w-lg mx-auto rotate-in">
            <p class="handwritten-alt text-xl">"Every week I will share a story, a lesson, or a moment that helped me get unstuck—so you know you’re not alone."</p>
            <p class="handwritten text-right mt-2">- Traci</p>
            <div class="doodle-heart"></div>
        </div>
        <!-- Post Feed -->
        <section class="mt-16">
            <div id="postFeed" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Posts will be added here dynamically -->
            </div>
        </section>
        <!-- Load More / Show Less Button -->
        <div class="flex justify-center mt-12">
            <button id="loadMoreBtn" class="bg-[#2EC4B6] text-white px-8 py-4 rounded-md shadow-md handwritten-casual text-xl hover:bg-[#25a99d] transition-all" style="display:none;">
                <span class="flex items-center" id="loadMoreBtnText">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                    Load More Posts
                </span>
            </button>
        </div>
        <!-- Final Note -->
        <div class="sticky-note bg-[#FF5E78] text-white mb-12 max-w-lg mx-auto mt-16 rotate-in">
            <p class="handwritten-alt text-xl">"Every post is a snapshot of where I was at that moment. Some days I'm flying high, others I'm barely hanging on. That's the messy truth of getting unstuck—it's not linear."</p>
            <p class="handwritten text-right mt-2">- Traci</p>
            <div class="doodle-heart"></div>
        </div>
    </div>
    <!-- Call to Action -->
    <div class="sticky-note bg-[#2EC4B6] text-white max-w-2xl mx-auto mb-16">
        <h2 class="handwritten-alt text-3xl mb-4">Share Your Story</h2>
        <p class="typewriter mb-6">What's keeping you stuck? Or how did you get unstuck? Your story might be exactly what someone else needs to hear right now.</p>
        <div class="flex justify-center">
            <a href="share-story.php" class="bg-white text-[#2EC4B6] px-6 py-3 rounded-md shadow-md handwritten-casual text-xl">Tell Your Story</a>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/private/includes/footer.php'; ?>

<script>
    // Posts from PHP (admin-managed)
    const posts = <?php echo json_encode($posts, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

    // Helper to build a **relative** uploads path
    const getImagePath = (imagePath) => {
        if (!imagePath) return '';
        const fileName = imagePath.split('/').pop().trim();
        // remove leading slash so browser will resolve to ./uploads/…
        return `uploads/${fileName}`;
    };

    // Scrapbook style classes
    const scrapbookStyles = [
        "sticky-note bg-[#FF5E78] text-white handwritten-alt rotate-in",
        "polaroid bg-white text-gray-800 handwritten-casual shadow-lg float-in",
        "taped-photo bg-[#83A2C3] text-white handwritten float-in"
    ];

    // Show only 3 posts by default, show all when expanded
    let expanded = false;
    const initialPosts = 3;

    function renderPosts() {
        const feed = document.getElementById('postFeed');
        feed.innerHTML = '';
        const toShow = expanded ? posts.length : Math.min(initialPosts, posts.length);

        posts.slice(0, toShow).forEach((post, index) => {
            const styleClass = scrapbookStyles[index % scrapbookStyles.length];
            const card = document.createElement('div');
            card.className = `relative p-6 mb-8 rounded-lg ${styleClass} transition transform hover:scale-105`;
            card.innerHTML = `
                ${post.image
                    ? `<img src="${getImagePath(post.image)}"`
                        + ` alt="Post Image"`
                        + ` class="blog-card-img mb-4 rounded-lg shadow"`
                        + ` style="max-width:100%;height:auto;">`
                    : ''}
                <h2 class="text-2xl mb-2 handwritten-bold">${post.title}</h2>
                <p class="mb-4 handwritten-casual">${post.excerpt}</p>
                ${post.link
                    ? `<a href="${post.link}" class="inline-block px-4 py-2 bg-white text-[#2EC4B6] rounded handwritten-alt shadow-md">Read Post</a>`
                    : ''}
            `;
            feed.appendChild(card);
        });

        // Show Load More/Show Less button only if there are more than 3 posts
        const loadMoreBtn = document.getElementById('loadMoreBtn');
        const loadMoreBtnText = document.getElementById('loadMoreBtnText');
        if (posts.length > initialPosts) {
            loadMoreBtn.style.display = '';
            if (!expanded) {
                loadMoreBtnText.innerHTML = `
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                    Load More Posts
                `;
            } else {
                loadMoreBtnText.innerHTML = `
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 15l-7-7-7 7"></path>
                    </svg>
                    Show Less
                `;
            }
        } else {
            loadMoreBtn.style.display = 'none';
        }
    }

    document.getElementById('loadMoreBtn').addEventListener('click', function() {
        expanded = !expanded;
        renderPosts();
    });

    // Initial render
    renderPosts();
</script>
</body>
</html>
