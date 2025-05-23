<?php
/**
 * Password Hashing Script
 * This script will find all users with plaintext passwords in the database and hash them
 */

// Error reporting for development
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Load required files
require_once 'models/database.php';

// Define a function to check if a password appears to be already hashed
function isAlreadyHashed($password) {
    // Password hash from password_hash() is at least 60 characters and starts with '$2y$'
    return strlen($password) >= 60 && strpos($password, '$2y$') === 0;
}

echo "<h1>Password Hashing Tool</h1>";
echo "<p>Starting password migration to secure hashed format...</p>";

// Initialize database connection
$db = new Database();

// Test database connection
try {
    // Get all users with a simple query to test connection
    $testSql = "SELECT COUNT(*) as count FROM users";
    $testResult = $db->fetchOne($testSql);
    echo "<p>Database connection successful. Found {$testResult['count']} user accounts.</p>";
} catch (Exception $e) {
    die("<p style='color:red'>Database connection error: " . $e->getMessage() . "</p>");
}

// Get all users
$sql = "SELECT * FROM users";
$users = $db->fetchAll($sql);

if (empty($users)) {
    echo "<p>No users found in the database.</p>";
    exit;
}

echo "<p>Found " . count($users) . " user accounts. Checking for plaintext passwords...</p>";

$updatedCount = 0;
$alreadyHashedCount = 0;
$errorCount = 0;

// Process each user
foreach ($users as $user) {
    $userId = $user['id'];
    $name = htmlspecialchars($user['name']);
    $email = htmlspecialchars($user['email']);
    $password = $user['password'];
    
    echo "<p>Checking user: $name ($email)... ";
    
    // Check if password is already hashed
    if (isAlreadyHashed($password)) {
        echo "Password already hashed.</p>";
        $alreadyHashedCount++;
        continue;
    }
    
    try {
        // Hash the password - make sure to escape it for SQL
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        if (!$hashedPassword) {
            throw new Exception("Failed to hash password");
        }
        
        // Make sure the hash is properly escaped for SQL
        $escapedHash = addslashes($hashedPassword);
        
        // Update the user record with prepared statement approach
        $updateSql = "UPDATE users SET password='$escapedHash' WHERE id='$userId'";
        $db->query($updateSql);
        
        echo "<span style='color:green'>Password hashed successfully!</span></p>";
        
        // Verify the hash was saved correctly
        $verifySql = "SELECT password FROM users WHERE id='$userId'";
        $verifyResult = $db->fetchOne($verifySql);
        if ($verifyResult && isAlreadyHashed($verifyResult['password'])) {
            $updatedCount++;
        } else {
            echo "<span style='color:red'>Warning: Password updated but verification failed!</span></p>";
            $errorCount++;
        }
    } catch (Exception $e) {
        echo "<span style='color:red'>Error: " . $e->getMessage() . "</span></p>";
        $errorCount++;
    }
}

echo "<h2>Summary</h2>";
echo "<p>Total users processed: " . count($users) . "</p>";
echo "<p>Users with already hashed passwords: $alreadyHashedCount</p>";
echo "<p>Users with passwords newly hashed: $updatedCount</p>";
echo "<p>Users with errors: $errorCount</p>";

if ($updatedCount > 0) {
    echo "<p style='color:green; font-weight:bold;'>Password migration completed successfully!</p>";
} else if ($alreadyHashedCount === count($users)) {
    echo "<p style='color:blue; font-weight:bold;'>All passwords are already in secure hashed format. No changes needed.</p>";
} else {
    echo "<p style='color:red; font-weight:bold;'>No passwords were updated. Please check for errors.</p>";
}

if ($errorCount > 0) {
    echo "<p style='color:red; font-weight:bold;'>Warning: Some passwords could not be updated. See errors above.</p>";
}

// Show plaintext passwords table for debugging (REMOVE IN PRODUCTION!)
echo "<h2>Current User Status (Debug)</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Name</th><th>Email</th><th>Password Type</th><th>Password Preview</th></tr>";

$refreshedUsers = $db->fetchAll("SELECT * FROM users");
foreach ($refreshedUsers as $user) {
    $passwordType = isAlreadyHashed($user['password']) ? "Hashed" : "Plaintext";
    $passwordPreview = isAlreadyHashed($user['password']) ? substr($user['password'], 0, 20) . "..." : "[PLAINTEXT VISIBLE]";
    
    echo "<tr>";
    echo "<td>" . htmlspecialchars($user['name']) . "</td>";
    echo "<td>" . htmlspecialchars($user['email']) . "</td>";
    echo "<td>" . $passwordType . "</td>";
    echo "<td>" . htmlspecialchars($passwordPreview) . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<p><a href='index.php'>Return to homepage</a></p>";
?> 