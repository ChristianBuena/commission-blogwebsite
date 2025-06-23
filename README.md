# Get Unstuck – Project README

_Last updated: June 16, 2025_

## Overview

Get Unstuck is a blog and resource platform focused on empowerment, weekly reflections, and community stories. This document outlines the latest features, improvements, and best practices implemented in the project.

---

### 1. SEO Optimization

**Meta Tags Generation:**
- All pages use a centralized PHP function to generate `<title>` and `<meta name="description">` tags for consistency and improved SEO.
  ```php
  function generateMetaTags($title, $description) {
      echo "<title>$title</title>";
      echo "<meta name='description' content='$description'>";
  }
  ```

**Heading Hierarchy:**
- Public-facing pages use semantic heading tags (`<h1>`, `<h2>`, `<h3>`) for accessibility and SEO.

**Open Graph & Canonical Tags:**
- Open Graph meta tags and canonical links are included on all major pages for better sharing and search ranking.
  ```html
  <meta property="og:title" content="Get Unstuck" />
  <meta property="og:description" content="Empowerment and weekly reflections." />
  <meta property="og:image" content="URL_to_image" />
  <meta property="og:url" content="URL_to_page" />
  <link rel="canonical" href="URL_to_page" />
  ```

---

### 2. Admin Enhancements

**Authentication System:**
- Session-based login system restricts access to admin and private routes.
  ```php
  session_start();
  // Check credentials and set $_SESSION['user'] on successful login
  ```

**Bulk Actions in Submission Manager:**
- Admins can perform bulk actions (approve, decline, mark as read, feature) on reader submissions using checkboxes and dropdowns.

**Confirmation Modals:**
- JavaScript confirmation modals are used before deleting items to prevent accidental actions.
  ```javascript
  function confirmDelete() {
      return confirm("Are you sure you want to delete this item?");
  }
  ```

**Task Manager Improvements:**
- Tasks can be added, deleted, and have their status and priority managed directly from the dashboard.

**Notifications:**
- Recent notifications are displayed in the admin panel, with a dedicated notifications page.

---

### 3. Performance Improvements

**Image Upload Optimization:**
- Uploaded images are compressed using PHP functions before saving to reduce file size and improve load times.

**Server-Side Caching:**
- Caching (e.g., Redis or Memcached) is recommended for repeated queries to improve performance.

**Loading Skeletons:**
- Tailwind CSS loading skeletons are used for AJAX-loaded content to enhance perceived performance.
  ```html
  <div class="animate-pulse bg-gray-200 h-4 w-full"></div>
  ```

**Minified Assets:**
- CSS and JS files are minified for production using build tools.

---

### 4. UI/UX Enhancements

**Smooth Page Transitions:**
- Alpine.js is used for smooth transitions and interactive UI elements.
  ```html
  <div x-data="{ show: false }" x-show="show" class="transition-opacity duration-500">
      <!-- Content -->
  </div>
  ```

**Reusable Components:**
- Sticky notes, quotes, and polaroid components are modularized for reuse across the site.
  ```html
  <div class="sticky-note">Your note here</div>
  ```

**Improved Form UX:**
- Inline validation and character counters are implemented for all forms.

---

### 5. Accessibility & Best Practices

**Lighthouse Audit:**
- Regular Lighthouse audits are performed to address issues such as color contrast and missing alt tags.

**Keyboard Navigation:**
- All interactive elements and modals are accessible via keyboard navigation.

**Accessible Labels:**
- All buttons and form fields include `aria-label` or visible text for accessibility.
  ```html
  <button aria-label="Submit Form">Submit</button>
  ```

---

### 6. Blog Pagination and AJAX

**AJAX Loading:**
- The "load more" button uses AJAX (Fetch API) to load additional posts without reloading the page.

**Optional Filters:**
- Filters for recent, featured, or random posts are available, improving content discovery.

---

### 7. Error Handling & Deployment

**Custom Error Pages:**
- Custom 404 and 500 error pages are implemented for a better user experience.

**Content Headers:**
- All PHP files set proper content headers.
  ```php
  header("Content-Type: text/html; charset=UTF-8");
  ```

**.htaccess Configuration:**
- URL rewriting is enabled, and sensitive files are protected.
- Root requests are redirected to `/public/index.php`.
  ```apache
  RewriteEngine On
  RewriteBase /get-unstuck/
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
  ErrorDocument 404 /get-unstuck/public/404.php
  ErrorDocument 500 /get-unstuck/public/500.php
  <FilesMatch "\.(env|ini|log|sh|bak)$">
      Require all denied
  </FilesMatch>
  ```

---

### 8. Content & Features

**Weekly Posts:**
- Admins can create, edit, publish, and delete weekly posts with image uploads.

**Podcast Management:**
- Podcast segments can be managed from the admin dashboard.

**Reader Stories:**
- Users can submit stories, which admins can feature or approve.

---

## Conclusion

By following this structured approach, the "Get Unstuck" platform delivers improved SEO, accessibility, performance, and user experience. All changes are tested before deployment to ensure a smooth and reliable experience for both users and administrators.