<?php
// Start session (needed if it was used in the code)
session_start();

// Include necessary models
require_once 'models/EventModel.php';

// Create instance of the EventModel
$eventModel = new EventModel();

echo "<h2>Testing Event Display</h2>";
echo "<p>Checking if events can be retrieved from the database...</p>";

// Get all events
$events = $eventModel->getEvents();
echo "<h3>All Events:</h3>";
if (empty($events)) {
    echo "<p>No events found in the database.</p>";
} else {
    echo "<p>Found " . count($events) . " events:</p>";
    echo "<ul>";
    foreach ($events as $event) {
        echo "<li>";
        echo "<strong>" . htmlspecialchars($event['title']) . "</strong> | ";
        echo "Status: " . htmlspecialchars($event['status']) . " | ";
        echo "Date: " . (isset($event['date']) ? htmlspecialchars($event['date']) : "N/A") . " | ";
        echo "Location: " . htmlspecialchars($event['location']) . " | ";
        echo "Image: " . htmlspecialchars($event['image_url'] ?? $event['image'] ?? "None");
        echo "</li>";
    }
    echo "</ul>";
}

// Get only approved events
$approvedEvents = $eventModel->getEvents('approved');
echo "<h3>Approved Events:</h3>";
if (empty($approvedEvents)) {
    echo "<p>No approved events found.</p>";
} else {
    echo "<p>Found " . count($approvedEvents) . " approved events:</p>";
    echo "<ul>";
    foreach ($approvedEvents as $event) {
        echo "<li>";
        echo "<strong>" . htmlspecialchars($event['title']) . "</strong> | ";
        echo "Date: " . (isset($event['date']) ? htmlspecialchars($event['date']) : "N/A") . " | ";
        echo "Location: " . htmlspecialchars($event['location']) . " | ";
        echo "Image: " . htmlspecialchars($event['image_url'] ?? $event['image'] ?? "None");
        echo "</li>";
    }
    echo "</ul>";
}

// Test connection
echo "<h3>Database Connection Test:</h3>";
try {
    require_once 'models/database.php';
    $db = new Database();
    $conn = $db->connect();
    if ($conn) {
        echo "<p style='color: green;'>Database connection successful!</p>";
        
        // Check if the events table exists
        $result = $db->query("SHOW TABLES LIKE 'event_requests'");
        if (mysqli_num_rows($result) > 0) {
            echo "<p style='color: green;'>event_requests table exists!</p>";
            
            // Show table structure
            $tableResult = $db->query("DESCRIBE event_requests");
            echo "<h4>Table Structure:</h4>";
            echo "<table border='1'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
            while ($row = mysqli_fetch_assoc($tableResult)) {
                echo "<tr>";
                echo "<td>" . $row['Field'] . "</td>";
                echo "<td>" . $row['Type'] . "</td>";
                echo "<td>" . $row['Null'] . "</td>";
                echo "<td>" . $row['Key'] . "</td>";
                echo "<td>" . $row['Default'] . "</td>";
                echo "<td>" . $row['Extra'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: red;'>event_requests table does not exist!</p>";
        }
    } else {
        echo "<p style='color: red;'>Database connection failed!</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
} 