<link rel="stylesheet" href="../css/header.css">
    <link href="https://fonts.googleapis.com/css2?family=Shadows+Into+Light&family=Pacifico&family=Gloria+Hallelujah&family=Patrick+Hand&family=Lora&family=Open+Sans&family=Quicksand:wght@400;600&family=Special+Elite&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .custom-play-btn {
            background: #FF5E78;
            color: #fff;
            border: none;
            border-radius: 9999px;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(255,94,120,0.15);
            transition: background 0.2s;
        }
        .custom-play-btn:hover {
            background: #FF6F61;
        }
        .custom-play-btn svg {
            width: 28px;
            height: 28px;
        }
        /* Dark mode toggle button style */
        .dark-toggle-btn {
            position: absolute;
            top: 1.5rem;
            right: 2rem;
            background: #f3f3f3;
            color: #222;
            border: none;
            border-radius: 9999px;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 2px 8px rgba(44,62,80,0.08);
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
            z-index: 10;
        }
        .dark-toggle-btn:hover {
            background: #FF5E78;
            color: #fff;
        }
        @media (max-width: 600px) {
            .dark-toggle-btn { right: 1rem; top: 1rem; }
        }
    </style>
</head>
<body>
    <!-- Dark Mode Toggle Button -->
    <button id="darkModeToggle" class="dark-toggle-btn" aria-label="Toggle dark mode">🌙</button>
    <!-- Header -->
    <header class="py-6 relative overflow-hidden">
        <div class="page-container">
            <div class="flex flex-col items-center space-y-6">
                <div class="transform -rotate-2">
                    <h1 class="handwritten-bold text-5xl md:text-6xl text-[#FF5E78] mb-2">Let's Get Unstuck</h1>
                    <p class="handwritten text-xl text-[#2EC4B6] -mt-2">by Traci Edwards</p>
                </div>
                <nav class="flex flex-wrap justify-center items-center gap-3">
                    <a href="index.php" class="nav-item px-4 py-2 bg-[#FF6F61] text-white rounded-md handwritten-alt text-lg shadow-md">Home</a>
                    <a href="heart-behind-this.php" class="nav-item px-4 py-2 bg-[#2EC4B6] text-white rounded-md handwritten-alt text-lg shadow-md">The Heart Behind This</a>
                    <a href="the-coaches.php" class="nav-item px-4 py-2 bg-[#E9C46A] text-white rounded-md handwritten-alt text-lg shadow-md">The Coaches</a>
                    <a href="blog.php" class="nav-item px-4 py-2 bg-[#83A2C3] text-white rounded-md handwritten-alt text-lg shadow-md">Weekly Posts</a>
                    <a href="share-story.php" class="nav-item px-4 py-2 bg-[#FF5E78] text-white rounded-md handwritten-alt text-lg shadow-md">Share Your Story</a>
                    <!-- Logout Button -->
                    <a href="logout.php" class="nav-item px-4 py-2 bg-gray-700 text-white rounded-md handwritten-alt text-lg shadow-md ml-2">Logout</a>
                </nav>
            </div>
        </div>
    </header>
    <!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', 'G-XXXXXXXXXX');

// Dark mode toggle logic
const darkModeToggle = document.getElementById('darkModeToggle');
const body = document.body;
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
<!-- Replace G-XXXXXXXXXX with your GA4 Measurement ID -->
<meta name="google-site-verification" content="YOUR_VERIFICATION_CODE" />
</body>