<?php
session_start();

// Prevent caching
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

// Destroy all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to index page
echo "<script>
window.location.replace('index.php');
</script>";
exit();
?>
