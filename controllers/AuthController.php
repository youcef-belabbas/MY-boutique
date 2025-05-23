<?php
require_once 'models/UserModel.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function index() {
        include 'views/home.php';
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = [
                'id' => uniqid(),
                'name' => $_POST['name'],
                'surname' => $_POST['surname'],
                'email' => $_POST['email'],
                'password' => $_POST['password'],
                'address' => $_POST['address'],
                'card' => $_POST['card'] ?? '',
                'role' => 'client'
            ];
            $this->userModel->addUser($user);
            header('Location: index.php?controller=auth&action=login&message=Registration successful');
        } else {
            include 'views/register.php';
        }
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = $this->userModel->getUserByEmail($_POST['email']);
            // Check if the password is verified with hash or matches directly (legacy plaintext)
            if ($user && ($this->userModel->verifyPassword($_POST['password'], $user['password']) || $_POST['password'] === $user['password'])) {
                // If it's a plaintext password, hash it for future logins
                if ($_POST['password'] === $user['password']) {
                    // Update the password to a hashed version
                    $user['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $this->userModel->updateUser($user);
                }
                
                $_SESSION['user'] = $user;
                header('Location: index.php?controller=auth&action=dashboard');
            } else {
                include 'views/login.php';
                echo '<p class="error">Invalid credentials</p>';
            }
        } else {
            include 'views/login.php';
        }
    }

    public function logout() {
        session_destroy();
        header('Location: index.php');
    }

    public function account() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=auth&action=login');
            return;
        }
        include 'views/account.php';
    }

    public function updateAccount() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=auth&action=login');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get current user data
            $userId = $_SESSION['user']['id'];
            $currentUser = $this->userModel->getUserById($userId);
            
            // Verify current password
            if (!isset($_POST['current_password']) || !$this->userModel->verifyPassword($_POST['current_password'], $currentUser['password'])) {
                header('Location: index.php?controller=auth&action=account#settings&error=Current password is incorrect');
                return;
            }
            
            // Update user data
            $updatedUser = [
                'id' => $userId,
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'address' => $_POST['address'] ?? '',
                'role' => $currentUser['role'],
                'password' => $currentUser['password'] // Default to current password
            ];
            
            // If new password is provided and confirmed
            if (!empty($_POST['new_password']) && $_POST['new_password'] === $_POST['confirm_password']) {
                $updatedUser['password'] = $_POST['new_password'];
            } elseif (!empty($_POST['new_password']) && $_POST['new_password'] !== $_POST['confirm_password']) {
                header('Location: index.php?controller=auth&action=account#settings&error=New passwords do not match');
                return;
            }
            
            // Update the user in the database
            $result = $this->userModel->updateUser($updatedUser);
            
            if ($result) {
                // Update session data but remove password from session for security
                $_SESSION['user'] = $updatedUser;
                unset($_SESSION['user']['password']);
                header('Location: index.php?controller=auth&action=account#settings&message=Account updated successfully');
            } else {
                header('Location: index.php?controller=auth&action=account#settings&error=Error updating account');
            }
        } else {
            header('Location: index.php?controller=auth&action=account');
        }
    }

    public function dashboard() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=auth&action=login');
            return;
        }
        include 'views/dashboard.php';
    }

    public function update_profile() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=auth&action=login');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user']['id'];
            $currentUser = $this->userModel->getUserById($userId);
            
            $updatedUser = [
                'id' => $userId,
                'name' => $_POST['name'],
                'email' => $currentUser['email'], // Email cannot be changed
                'password' => $currentUser['password'],
                'role' => $currentUser['role']
            ];
            
            $result = $this->userModel->updateUser($updatedUser);
            
            if ($result) {
                // Update session data
                $_SESSION['user']['name'] = $_POST['name'];
                header('Location: index.php?controller=auth&action=account&message=Profile updated successfully');
            } else {
                header('Location: index.php?controller=auth&action=account&error=Error updating profile');
            }
        } else {
            header('Location: index.php?controller=auth&action=account');
        }
    }
    
    public function change_password() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=auth&action=login');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user']['id'];
            $currentUser = $this->userModel->getUserById($userId);
            
            // Verify current password
            if (!$this->userModel->verifyPassword($_POST['current_password'], $currentUser['password'])) {
                header('Location: index.php?controller=auth&action=account&error=Current password is incorrect');
                return;
            }
            
            // Check if new passwords match
            if ($_POST['new_password'] !== $_POST['confirm_password']) {
                header('Location: index.php?controller=auth&action=account&error=New passwords do not match');
                return;
            }
            
            // Update password
            $updatedUser = [
                'id' => $userId,
                'name' => $currentUser['name'],
                'email' => $currentUser['email'],
                'password' => $_POST['new_password'], // Will be hashed in the model
                'role' => $currentUser['role']
            ];
            
            $result = $this->userModel->updateUser($updatedUser);
            
            if ($result) {
                // Don't store password in session for security
                unset($_SESSION['user']['password']);
                header('Location: index.php?controller=auth&action=account&message=Password changed successfully');
            } else {
                header('Location: index.php?controller=auth&action=account&error=Error changing password');
            }
        } else {
            header('Location: index.php?controller=auth&action=account');
        }
    }
    
    public function delete_account() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=auth&action=login');
            return;
        }
        
        $userId = $_SESSION['user']['id'];
        
        try {
            $result = $this->userModel->deleteUser($userId);
            
            if ($result) {
                session_destroy();
                header('Location: index.php?message=Account deleted successfully');
            } else {
                header('Location: index.php?controller=auth&action=account&error=Error deleting account');
            }
        } catch (Exception $e) {
            header('Location: index.php?controller=auth&action=account&error=' . urlencode($e->getMessage()));
        }
    }
}
?>  