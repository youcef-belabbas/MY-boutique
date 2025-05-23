<?php
/**
 * Front Controller for MY-boutique
 * Main entry point for all requests
 */

// Error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialiser la langue (français par défaut)
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'fr';
}

// Gérer le changement de langue
if (isset($_GET['lang']) && in_array($_GET['lang'], ['fr', 'en'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

// Charger le système de langue
require_once 'includes/language.php';

// Initialize session data
if (!isset($_SESSION['products'])) {
    $_SESSION['products'] = [
        ['id' => 1, 'name' => 'Chemise Classique', 'category' => 'Chemises', 'price' => 29.99, 'stock' => 10, 'sales' => 50, 'image' => 'shirt.jpg', 'comments' => []],
        ['id' => 2, 'name' => 'Jean Slim', 'category' => 'Pantalons', 'price' => 49.99, 'stock' => 5, 'sales' => 30, 'image' => 'jeans.jpg', 'comments' => []],
        ['id' => 3, 'name' => 'Veste en Cuir', 'category' => 'Vestes', 'price' => 99.99, 'stock' => 3, 'sales' => 20, 'image' => 'jacket.jpg', 'comments' => []]
    ];
}
if (!isset($_SESSION['users'])) {
    $_SESSION['users'] = [['id' => 1, 'name' => 'Admin', 'email' => 'admin@my.com', 'password' => 'admin123', 'role' => 'admin', 'address' => '', 'card' => '']];
}
if (!isset($_SESSION['events'])) {
    $_SESSION['events'] = [['id' => 1, 'title' => 'Défilé de Mode 2025', 'image' => 'fashion_show.jpg', 'status' => 'approved']];
}
if (!isset($_SESSION['purchases'])) {
    $_SESSION['purchases'] = [];
}
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Load controllers
require_once 'controllers/AuthController.php';
require_once 'models/ProductModel.php';
require_once 'controllers/ProductController.php';
require_once 'controllers/EventController.php';
require_once 'controllers/AdminController_new.php';
require_once 'models/UserModel.php';

// Routing
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

switch ($controller) {
    case 'auth':
        $authController = new AuthController();
        $authController->$action();
        break;
    case 'product':
        $productController = new ProductController();
        $productController->$action();
        break;
    case 'event':
        $eventController = new EventController();
        $eventController->$action();
        break;
    case 'admin':
        $adminController = new AdminController();
        $adminController->$action();
        break;
    default:
        include 'views/home.php';
}
?>