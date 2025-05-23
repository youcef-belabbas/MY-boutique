<?php
session_start();
require_once 'models/database.php';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $upload_directory = 'uploads/events/';
    
    // Create directory if it doesn't exist
    if (!is_dir($upload_directory)) {
        mkdir($upload_directory, 0755, true);
    }
    
    // Check if file was uploaded without errors
    if (isset($_FILES["test_image"]) && $_FILES["test_image"]["error"] == 0) {
        $file_name = time() . '_' . basename($_FILES["test_image"]["name"]);
        $target_file = $upload_directory . $file_name;
        $upload_success = false;
        $message = "";
        
        // Check file type
        $allowed_types = array("jpg", "jpeg", "png", "gif");
        $file_extension = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        if (in_array($file_extension, $allowed_types)) {
            // Try to upload the file
            if (move_uploaded_file($_FILES["test_image"]["tmp_name"], $target_file)) {
                $upload_success = true;
                $message = "File was uploaded successfully. Image path: " . $target_file;
                
                // Save to database for testing
                try {
                    $db = new Database();
                    $eventId = uniqid();
                    $sql = "INSERT INTO event_requests (id, title, description, event_date, time, location, image, status, created_by, created_at) 
                            VALUES ('$eventId', 'Test Event', 'This is a test event from upload_test.php', '2023-12-31', '12:00:00', 'Test Location', '$target_file', 'approved', 'admin', NOW())";
                    $db->query($sql);
                    $message .= "<br>Event created in database with ID: $eventId";
                } catch (Exception $e) {
                    $message .= "<br>Database error: " . $e->getMessage();
                }
            } else {
                $message = "Error: There was a problem uploading your file.";
            }
        } else {
            $message = "Error: Only JPG, JPEG, PNG, and GIF files are allowed.";
        }
    } else {
        $message = "Error: " . $_FILES["test_image"]["error"];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Image Upload Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .container {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
        }
        
        .success {
            color: green;
            font-weight: bold;
        }
        
        .error {
            color: red;
            font-weight: bold;
        }
        
        .preview {
            margin-top: 20px;
            max-width: 300px;
            border: 1px solid #ddd;
            padding: 5px;
        }
        
        .preview img {
            max-width: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Event Image Upload Test</h1>
        
        <?php if (isset($message)): ?>
            <div class="<?php echo $upload_success ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
            
            <?php if ($upload_success): ?>
                <div class="preview">
                    <h3>Image Preview:</h3>
                    <img src="<?php echo $target_file; ?>" alt="Uploaded Image">
                </div>
                
                <div>
                    <h3>Testing Event Display:</h3>
                    <p>Go to <a href="index.php?controller=event&action=events">Events Page</a> to see if your image appears</p>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <h3>Upload an Image</h3>
            <input type="file" name="test_image" required>
            <br><br>
            <input type="submit" value="Upload Image">
        </form>
        
        <hr>
        <h3>Database Info</h3>
        <?php
        try {
            $db = new Database();
            $sql = "SELECT id, title, image FROM event_requests";
            $events = $db->fetchAll($sql);
            
            echo "<p>Current events in database:</p>";
            echo "<ul>";
            foreach ($events as $event) {
                echo "<li>ID: {$event['id']}, Title: {$event['title']}, Image: {$event['image']}</li>";
            }
            echo "</ul>";
        } catch (Exception $e) {
            echo "<p class='error'>Database error: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>
</body>
</html> 