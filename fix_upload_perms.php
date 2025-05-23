<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Fixing Upload Directory Permissions</h1>";

// Directory paths to check and fix
$directories = [
    'uploads/',
    'uploads/events/',
    'uploads/products/'
];

foreach ($directories as $dir) {
    echo "<h2>Checking: {$dir}</h2>";
    
    // Check if directory exists
    if (!file_exists($dir)) {
        echo "<p>Directory does not exist. Creating...</p>";
        if (mkdir($dir, 0777, true)) {
            echo "<p style='color:green'>Directory created successfully</p>";
        } else {
            echo "<p style='color:red'>Failed to create directory. Check server permissions.</p>";
            continue;
        }
    }
    
    // Check current permissions
    $perms = substr(sprintf('%o', fileperms($dir)), -4);
    echo "<p>Current permissions: {$perms}</p>";
    
    // Try to set full permissions
    echo "<p>Setting to 0777...</p>";
    if (chmod($dir, 0777)) {
        echo "<p style='color:green'>Successfully updated permissions to 0777</p>";
    } else {
        echo "<p style='color:red'>Failed to change permissions. You may need to manually run:</p>";
        echo "<code>chmod -R 777 {$dir}</code>";
    }
    
    // Check if writable
    if (is_writable($dir)) {
        echo "<p style='color:green'>Directory is writable!</p>";
    } else {
        echo "<p style='color:red'>Directory is still NOT writable.</p>";
    }
}

// Create test file to verify write access
$testFile = 'uploads/events/test_write.txt';
echo "<h2>Testing write access</h2>";
$content = "This is a test file created at " . date('Y-m-d H:i:s');

if (file_put_contents($testFile, $content) !== false) {
    echo "<p style='color:green'>Successfully wrote test file: {$testFile}</p>";
    
    // Read back the content
    echo "<p>File content: " . htmlspecialchars(file_get_contents($testFile)) . "</p>";
    
    // Clean up
    unlink($testFile);
    echo "<p>Test file removed.</p>";
} else {
    echo "<p style='color:red'>Failed to write test file. Directory may not be writable.</p>";
}

// Check for disabled functions
echo "<h2>PHP Security Settings</h2>";
$disabled_functions = ini_get('disable_functions');
echo "<p>Disabled functions: " . (empty($disabled_functions) ? "None" : $disabled_functions) . "</p>";

// Check open_basedir restrictions
$open_basedir = ini_get('open_basedir');
echo "<p>open_basedir restrictions: " . (empty($open_basedir) ? "None" : $open_basedir) . "</p>";

// Show PHP file upload settings
echo "<h2>PHP Upload Settings</h2>";
echo "<ul>";
echo "<li>upload_max_filesize: " . ini_get('upload_max_filesize') . "</li>";
echo "<li>max_file_uploads: " . ini_get('max_file_uploads') . "</li>";
echo "<li>post_max_size: " . ini_get('post_max_size') . "</li>";
echo "<li>memory_limit: " . ini_get('memory_limit') . "</li>";
echo "</ul>";

echo "<p>The default upload_max_filesize (2M) might be too small for your images.</p>";

// Show temporary directory info
$tempDir = sys_get_temp_dir();
echo "<h2>Temporary Directory</h2>";
echo "<p>Path: {$tempDir}</p>";

if (is_writable($tempDir)) {
    echo "<p style='color:green'>Temporary directory is writable.</p>";
} else {
    echo "<p style='color:red'>Temporary directory is NOT writable. This could be preventing uploads.</p>";
}

// Display php.ini location
echo "<h2>PHP Configuration</h2>";
echo "<p>php.ini location: " . php_ini_loaded_file() . "</p>";

// Check for SELinux
exec('which getenforce 2>/dev/null', $output, $return_var);
if ($return_var === 0) {
    exec('getenforce', $selinux);
    echo "<p>SELinux status: {$selinux[0]}</p>";
    if (strcasecmp($selinux[0], 'Enforcing') === 0) {
        echo "<p style='color:orange'>SELinux is enforcing. This might be preventing uploads.</p>";
    }
} else {
    echo "<p>SELinux not detected.</p>";
}

echo "<h2>Next Steps</h2>";
echo "<p>If uploads are still failing:</p>";
echo "<ol>";
echo "<li>Try uploading a smaller file (less than 2MB)</li>";
echo "<li>Check PHP error logs for specific errors</li>";
echo "<li>Ensure your form has <code>enctype='multipart/form-data'</code> attribute</li>";
echo "<li>Consider increasing upload_max_filesize in php.ini to 10M or more</li>";
echo "<li>Manually set permissions using: <code>chmod -R 777 uploads/</code></li>";
echo "</ol>";
?> 