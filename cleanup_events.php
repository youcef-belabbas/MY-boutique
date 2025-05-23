<?php
session_start();

echo "Starting cleanup...<br>";

// Clear any session-based events to avoid duplicates
if (isset($_SESSION['events'])) {
    echo "Removing session-based events...<br>";
    unset($_SESSION['events']);
    echo "Session events cleared.<br>";
}

echo "Cleanup complete. You can now delete this file.<br>";
echo "<a href='index.php'>Return to homepage</a>";
?> 