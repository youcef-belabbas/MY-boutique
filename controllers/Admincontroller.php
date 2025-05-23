<?php
require_once 'models/UserModel.php';
require_once 'models/ProductModel.php';

// Controller for admin-specific actions
class AdminController {
    private $userModel;
    private $productModel;
    private $purchaseModel;
    private $eventModel;

    public function __construct() {
        $this->userModel = new UserModel();
        $this->productModel = new ProductModel();
    }

    // Display admin dashboard with sales stats, user management, and event approvals
    public function dashboard() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: index.php?controller=auth&action=login');
            return;
        }
        include 'views/admin_dashboard.php';
    }

    // View detailed sales data
    public function viewSales() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: index.php?controller=auth&action=login');
            return;
        }
        
        // Get all purchases
        $purchases = $this->productModel->getAllPurchases();
        
        // Get product performance statistics
        $productStats = [];
        $products = $this->productModel->getProducts();
        
        foreach ($products as $product) {
            $productStats[] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'category' => $product['category'],
                'price' => $product['price'],
                'stock' => $product['stock'],
                'sales' => $product['sales'],
                'revenue' => $product['price'] * $product['sales'],
                'image' => $product['image']
            ];
        }
        
        // Sort by sales (highest first)
        usort($productStats, function($a, $b) {
            return $b['sales'] - $a['sales'];
        });
        
        include 'views/admin_sales.php';
    }
    
    // Edit a user account
    public function editUser() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: index.php?controller=auth&action=login');
            return;
        }
        
        // Check if user ID is provided
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            header('Location: index.php?controller=admin&action=dashboard&error=User ID is missing');
            return;
        }
        
        $userId = $_GET['id'];
        $editUser = $this->userModel->getUserById($userId);
        
        // Check if user exists
        if (!$editUser) {
            header('Location: index.php?controller=admin&action=dashboard&error=User not found');
            return;
        }
        
        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $surname = $_POST['surname'] ?? '';
            $email = $_POST['email'] ?? '';
            $role = $_POST['role'] ?? 'client';
            $address = $_POST['address'] ?? '';
            
            // Validate required fields
            if (empty($name) || empty($email)) {
                header('Location: index.php?controller=admin&action=editUser&id=' . $userId . '&error=Name and email are required');
                return;
            }
            
            // Update user data
            $userData = [
                'id' => $userId,
                'name' => $name,
                'surname' => $surname,
                'email' => $email,
                'role' => $role,
                'address' => $address,
                'password' => $editUser['password'] // Keep existing password by default
            ];
            
            // Handle password change if provided
            if (!empty($_POST['new_password'])) {
                // Validate password match
                if ($_POST['new_password'] !== $_POST['confirm_password']) {
                    header('Location: index.php?controller=admin&action=editUser&id=' . $userId . '&error=Passwords do not match');
                    return;
                }
                
                // Update password in user data
                $userData['password'] = $_POST['new_password'];
            }
            
            // Update user in database
            $result = $this->userModel->updateUser($userData);
            
            if ($result) {
                header('Location: index.php?controller=admin&action=editUser&id=' . $userId . '&message=User updated successfully');
            } else {
                header('Location: index.php?controller=admin&action=editUser&id=' . $userId . '&error=Error updating user');
            }
            return;
        }
        
        // Display edit form
        include 'views/edit_user.php';
    }

    // Delete a user account (except admin accounts)
    public function deleteUser() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: index.php?controller=auth&action=login');
            return;
        }
        
        // Check if user_id is provided
        if (!isset($_POST['user_id']) || empty($_POST['user_id'])) {
            header('Location: index.php?controller=admin&action=dashboard&error=User ID is missing');
            return;
        }
        
        $userId = $_POST['user_id'];
        $user = $this->userModel->getUserById($userId);
        
        // Check if user exists
        if (!$user) {
            header('Location: index.php?controller=admin&action=dashboard&error=User not found');
            return;
        }
        
        // Don't delete admin users (additional protection)
        if ($user['role'] === 'admin') {
            header('Location: index.php?controller=admin&action=dashboard&error=Cannot delete admin users');
            return;
        }
        
        // Try to delete the user
        try {
            $this->userModel->deleteUser($userId);
            header('Location: index.php?controller=admin&action=dashboard&message=User deleted successfully');
        } catch (Exception $e) {
            header('Location: index.php?controller=admin&action=dashboard&error=' . urlencode('Error deleting user: ' . $e->getMessage()));
        }
    }

    // Delete a product 
    public function deleteProduct() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: index.php?controller=auth&action=login');
            return;
        }
        
        $productId = $_GET['id'];
        $this->productModel->deleteProduct($productId);
        header('Location: index.php?controller=admin&action=dashboard');
    }

    // Approve a product request
    public function approveProductRequest() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: index.php?controller=auth&action=login');
            return;
        }
        
        $requestId = $_GET['id'];
        $this->productModel->approveProductRequest($requestId);
        header('Location: index.php?controller=admin&action=dashboard');
    }

    // Reject a product request
    public function rejectProductRequest() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: index.php?controller=auth&action=login');
            return;
        }
        
        $requestId = $_GET['id'];
        $this->productModel->rejectProductRequest($requestId);
        header('Location: index.php?controller=admin&action=dashboard');
    }
}
?>