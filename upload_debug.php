<?php
// Enable all error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session
session_start();

echo "<h1>Image Upload Diagnostic</h1>";

// Check if upload directory exists and is writable
$upload_directory = 'uploads/events/';
if (!file_exists($upload_directory)) {
    echo "<p style='color:red'>Upload directory does not exist. Attempting to create it...</p>";
    if (mkdir($upload_directory, 0755, true)) {
        echo "<p style='color:green'>Successfully created upload directory!</p>";
    } else {
        echo "<p style='color:red'>Failed to create upload directory. Check permissions.</p>";
    }
} else {
    echo "<p>Upload directory exists.</p>";
    if (is_writable($upload_directory)) {
        echo "<p style='color:green'>Upload directory is writable.</p>";
    } else {
        echo "<p style='color:red'>Upload directory is NOT writable. Fix permissions: chmod 755 $upload_directory</p>";
    }
}

// Display PHP upload settings
echo "<h2>PHP Upload Settings</h2>";
echo "<ul>";
echo "<li>upload_max_filesize: " . ini_get('upload_max_filesize') . "</li>";
echo "<li>max_file_uploads: " . ini_get('max_file_uploads') . "</li>";
echo "<li>post_max_size: " . ini_get('post_max_size') . "</li>";
echo "<li>memory_limit: " . ini_get('memory_limit') . "</li>";
echo "</ul>";

// Process upload if form submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "<h2>Upload Results</h2>";
    
    // Debug form data
    echo "<h3>POST data</h3>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    // Debug files data
    echo "<h3>FILES data</h3>";
    echo "<pre>";
    print_r($_FILES);
    echo "</pre>";
    
    if (isset($_FILES["test_image"]) && $_FILES["test_image"]["error"] == 0) {
        $file_name = time() . '_' . basename($_FILES["test_image"]["name"]);
        $target_file = $upload_directory . $file_name;
        
        // Check file type
        $allowed_types = array("jpg", "jpeg", "png", "gif");
        $file_extension = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        if (in_array($file_extension, $allowed_types)) {
            // Try to upload the file
            echo "<p>Attempting to upload file to: $target_file</p>";
            
            if (move_uploaded_file($_FILES["test_image"]["tmp_name"], $target_file)) {
                echo "<p style='color:green'>File uploaded successfully!</p>";
                echo "<p>File path: $target_file</p>";
                echo "<img src='$target_file' style='max-width:300px;' />";
            } else {
                echo "<p style='color:red'>Failed to move uploaded file. Check PHP error logs.</p>";
            }
        } else {
            echo "<p style='color:red'>Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.</p>";
        }
    } else {
        echo "<p style='color:red'>No file uploaded or upload error occurred.</p>";
        if (isset($_FILES["test_image"])) {
            echo "<p>Error code: " . $_FILES["test_image"]["error"] . "</p>";
            
            // Explain error codes
            $error_codes = [
                0 => "No error",
                1 => "The uploaded file exceeds the upload_max_filesize directive in php.ini",
                2 => "The uploaded file exceeds the MAX_FILE_SIZE directive specified in the HTML form",
                3 => "The uploaded file was only partially uploaded",
                4 => "No file was uploaded",
                6 => "Missing a temporary folder",
                7 => "Failed to write file to disk",
                8 => "A PHP extension stopped the file upload"
            ];
            
            if (isset($error_codes[$_FILES["test_image"]["error"]])) {
                echo "<p>Error meaning: " . $error_codes[$_FILES["test_image"]["error"]] . "</p>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        pre {
            background-color: #f5f5f5;
            padding: 10px;
            border: 1px solid #ddd;
            overflow: auto;
        }
    </style>
</head>
<body>
    <h2>Test Upload Form</h2>
    <form method="POST" enctype="multipart/form-data">
        <p>
            <label for="test_image">Select an image to upload:</label>
            <input type="file" name="test_image" id="test_image">
        </p>
        <p>
            <button type="submit">Upload Image</button>
        </p>
    </form>
</body>
</html> 