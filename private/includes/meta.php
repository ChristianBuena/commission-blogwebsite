<?php
if ( ! function_exists('generateMetaTags') ) {
  function generateMetaTags($title, $description) {
    echo "<title>$title</title>";
    echo "<meta name='description' content='$description'>";
  }
}
?>