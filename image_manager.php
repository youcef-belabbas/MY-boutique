<?php
session_start();
require_once 'models/database.php';

// Create uploads directory if it doesn't exist
$upload_directory = 'uploads/events/';
if (!is_dir($upload_directory)) {
    mkdir($upload_directory, 0755, true);
}

$message = '';
$success = false;

// Handle image upload
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if it's a new upload or replacing an existing image
    if (isset($_FILES["event_image"]) && $_FILES["event_image"]["error"] == 0) {
        $file_name = time() . '_' . basename($_FILES["event_image"]["name"]);
        $target_file = $upload_directory . $file_name;
        
        // Check file type
        $allowed_types = array("jpg", "jpeg", "png", "gif");
        $file_extension = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        if (in_array($file_extension, $allowed_types)) {
            // Try to upload the file
            if (move_uploaded_file($_FILES["event_image"]["tmp_name"], $target_file)) {
                $success = true;
                
                // If we're updating an existing event
                if (isset($_POST['event_id']) && !empty($_POST['event_id'])) {
                    try {
                        $db = new Database();
                        $eventId = $_POST['event_id'];
                        
                        // Get the current image path to delete the old file
                        $sql = "SELECT image FROM event_requests WHERE id = '$eventId'";
                        $currentEvent = $db->fetchOne($sql);
                        
                        if ($currentEvent && !empty($currentEvent['image']) && file_exists($currentEvent['image'])) {
                            // Delete the old image file if it exists and is not the default
                            if (strpos($currentEvent['image'], 'default-event.jpg') === false) {
                                unlink($currentEvent['image']);
                            }
                        }
                        
                        // Update the database with new image path
                        $sql = "UPDATE event_requests SET image = '$target_file' WHERE id = '$eventId'";
                        $db->query($sql);
                        $message = "Image updated successfully for event ID: $eventId";
                    } catch (Exception $e) {
                        $message = "Database error: " . $e->getMessage();
                        $success = false;
                    }
                } 
                // If it's a new event with image only
                else if (isset($_POST['title']) && !empty($_POST['title'])) {
                    try {
                        $db = new Database();
                        $eventId = uniqid();
                        $title = $_POST['title'];
                        $description = isset($_POST['description']) ? $_POST['description'] : 'No description';
                        $date = isset($_POST['date']) ? $_POST['date'] : date('Y-m-d', strtotime('+30 days'));
                        $time = isset($_POST['time']) ? $_POST['time'] : '18:00:00';
                        $location = isset($_POST['location']) ? $_POST['location'] : 'TBA';
                        
                        $sql = "INSERT INTO event_requests (id, title, description, event_date, time, location, image, status, created_by, created_at) 
                                VALUES ('$eventId', '$title', '$description', '$date', '$time', '$location', '$target_file', 'approved', 'admin', NOW())";
                        $db->query($sql);
                        $message = "New event created with image. Event ID: $eventId";
                    } catch (Exception $e) {
                        $message = "Database error: " . $e->getMessage();
                        $success = false;
                    }
                }
                // Just upload the image without assigning to any event
                else {
                    $message = "File uploaded successfully. Path: $target_file";
                }
            } else {
                $message = "Error: There was a problem uploading your file.";
                $success = false;
            }
        } else {
            $message = "Error: Only JPG, JPEG, PNG, and GIF files are allowed.";
            $success = false;
        }
    } else if (isset($_POST['delete_image']) && isset($_POST['event_id'])) {
        // Handle image deletion
        try {
            $db = new Database();
            $eventId = $_POST['event_id'];
            
            // Get current image
            $sql = "SELECT image FROM event_requests WHERE id = '$eventId'";
            $currentEvent = $db->fetchOne($sql);
            
            if ($currentEvent && !empty($currentEvent['image'])) {
                // Delete physical file if it exists
                if (file_exists($currentEvent['image']) && strpos($currentEvent['image'], 'default-event.jpg') === false) {
                    unlink($currentEvent['image']);
                }
                
                // Update database to empty image
                $sql = "UPDATE event_requests SET image = '' WHERE id = '$eventId'";
                $db->query($sql);
                $message = "Image removed from event ID: $eventId";
                $success = true;
            } else {
                $message = "No image found for this event";
                $success = false;
            }
        } catch (Exception $e) {
            $message = "Database error: " . $e->getMessage();
            $success = false;
        }
    }
}

// Get all events to display
try {
    $db = new Database();
    $sql = "SELECT * FROM event_requests ORDER BY created_at DESC";
    $events = $db->fetchAll($sql);
} catch (Exception $e) {
    $events = [];
    $message = "Error loading events: " . $e->getMessage();
    $success = false;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Event Image Manager</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --black: #000;
            --white: #fff;
            --yellow: #ffc107;
            --gray: #f5f5f5;
            --dark-gray: #333;
        }
        
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f7f7f7;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h1, h2, h3 {
            color: #333;
        }
        
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .success {
            background-color: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        .upload-section, .events-section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        .btn {
            padding: 10px 15px;
            background-color: var(--yellow);
            color: var(--black);
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #e0aa00;
        }
        
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c82333;
        }
        
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .event-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .event-card:hover {
            transform: translateY(-5px);
        }
        
        .event-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .no-image {
            height: 200px;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-style: italic;
        }
        
        .event-details {
            padding: 15px;
        }
        
        .event-title {
            margin-top: 0;
            font-size: 18px;
        }
        
        .event-meta {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .event-actions {
            padding: 10px 15px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
        }
        
        .tabs {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }
        
        .tab {
            padding: 10px 20px;
            cursor: pointer;
            border: 1px solid transparent;
            margin-bottom: -1px;
            border-radius: 4px 4px 0 0;
        }
        
        .tab.active {
            border: 1px solid #ddd;
            border-bottom-color: white;
            background-color: white;
            font-weight: bold;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .preview-container {
            margin-top: 15px;
            display: none;
        }
        
        .preview-container img {
            max-width: 300px;
            max-height: 200px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .nav-bar {
            padding: 10px 0;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .nav-links {
            display: flex;
            gap: 15px;
        }
        
        .nav-link {
            text-decoration: none;
            color: var(--black);
            padding: 5px 10px;
            border-radius: 4px;
            transition: all 0.3s;
        }
        
        .nav-link:hover {
            background-color: var(--yellow);
        }
        
        @media (max-width: 768px) {
            .events-grid {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="nav-bar">
            <h1>Event Image Manager</h1>
            <div class="nav-links">
                <a href="index.php" class="nav-link">Home</a>
                <a href="index.php?controller=event&action=events" class="nav-link">Events</a>
                <a href="index.php?controller=event&action=manage" class="nav-link">Manage Events</a>
            </div>
        </div>
        
        <?php if ($message): ?>
            <div class="message <?php echo $success ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="tabs">
            <div class="tab active" data-tab="upload">Upload Image</div>
            <div class="tab" data-tab="manage">Manage Event Images</div>
        </div>
        
        <div id="upload-tab" class="tab-content active">
            <div class="upload-section">
                <h2>Upload New Event Image</h2>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="event_image">Select Image File</label>
                        <input type="file" class="form-control" id="event_image" name="event_image" required>
                    </div>
                    
                    <div class="preview-container" id="image-preview">
                        <h3>Image Preview</h3>
                        <img id="preview-img" src="#" alt="Preview">
                    </div>
                    
                    <div class="form-group">
                        <label for="title">Event Title (Optional - Creates New Event)</label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="Leave blank to just upload image">
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Event Description (Optional)</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="date">Event Date (Optional)</label>
                        <input type="date" class="form-control" id="date" name="date">
                    </div>
                    
                    <div class="form-group">
                        <label for="location">Event Location (Optional)</label>
                        <input type="text" class="form-control" id="location" name="location">
                    </div>
                    
                    <button type="submit" class="btn">Upload Image</button>
                </form>
            </div>
        </div>
        
        <div id="manage-tab" class="tab-content">
            <div class="events-section">
                <h2>Manage Event Images</h2>
                
                <?php if (empty($events)): ?>
                    <p>No events found in the database.</p>
                <?php else: ?>
                    <div class="events-grid">
                        <?php foreach ($events as $event): ?>
                            <div class="event-card">
                                <?php if (!empty($event['image']) && file_exists($event['image'])): ?>
                                    <img src="<?php echo htmlspecialchars($event['image']); ?>" class="event-image" alt="<?php echo htmlspecialchars($event['title']); ?>">
                                <?php else: ?>
                                    <div class="no-image">No Image Available</div>
                                <?php endif; ?>
                                
                                <div class="event-details">
                                    <h3 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                                    <div class="event-meta">
                                        <div>ID: <?php echo $event['id']; ?></div>
                                        <div>Date: <?php echo isset($event['event_date']) ? date('M d, Y', strtotime($event['event_date'])) : 'N/A'; ?></div>
                                        <div>Status: <?php echo ucfirst($event['status']); ?></div>
                                    </div>
                                </div>
                                
                                <div class="event-actions">
                                    <form method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                        <div class="form-group">
                                            <label>Replace Image:</label>
                                            <input type="file" name="event_image" required>
                                        </div>
                                        <button type="submit" class="btn">Update</button>
                                    </form>
                                    
                                    <?php if (!empty($event['image'])): ?>
                                        <form method="POST" style="margin-top:10px;">
                                            <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                            <input type="hidden" name="delete_image" value="1">
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to remove this image?')">Remove Image</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Tab switching functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.tab');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    tabs.forEach(t => t.classList.remove('active'));
                    tabContents.forEach(c => c.classList.remove('active'));
                    
                    tab.classList.add('active');
                    document.getElementById(tab.getAttribute('data-tab') + '-tab').classList.add('active');
                });
            });
            
            // Image preview functionality
            document.getElementById('event_image').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    const previewContainer = document.getElementById('image-preview');
                    const previewImg = document.getElementById('preview-img');
                    
                    reader.onload = function(event) {
                        previewImg.src = event.target.result;
                        previewContainer.style.display = 'block';
                    };
                    
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
</body>
</html> 