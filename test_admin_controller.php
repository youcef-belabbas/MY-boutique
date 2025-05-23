<?php
require_once 'controllers/AdminController_new.php';

$admin = new AdminController();

echo "Checking if editUser method exists...\n";
if (method_exists($admin, 'editUser')) {
    echo "SUCCESS: editUser method exists in AdminController.\n";
} else {
    echo "ERROR: editUser method does not exist in AdminController.\n";
}

// Display all available methods in the class
echo "\nAll methods in AdminController:\n";
$methods = get_class_methods($admin);
print_r($methods);

echo "\nTest completed.\n";
?> 