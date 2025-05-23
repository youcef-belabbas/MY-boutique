<?php
require_once 'database.php';

/**
 * User Model
 * Handles all user-related data operations
 */
class UserModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Add a new user
     * @param array $user User data
     * @return int|bool User ID on success, false on failure
     */
    public function addUser($user) {
        // Hash the password before storing it
        $hashedPassword = password_hash($user['password'], PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (id, name, surname, email, password, address, card, role) VALUES ('{$user['id']}', '{$user['name']}', '{$user['surname']}', '{$user['email']}', '$hashedPassword', '{$user['address']}', '{$user['card']}', '{$user['role']}')";
        $this->db->query($sql);
        return $user['id'];
    }

    /**
     * Update existing user information
     * @param array $updatedUser User data with updated information
     * @return bool True on success, false on failure
     */
    public function updateUser($updatedUser) {
        try {
            // Get the current user data to keep any fields that aren't provided
            $currentUser = $this->getUserById($updatedUser['id']);
            if (!$currentUser) {
                return false;
            }
            
            // Set defaults for optional fields
            $surname = isset($updatedUser['surname']) ? $updatedUser['surname'] : ($currentUser['surname'] ?? '');
            $address = isset($updatedUser['address']) ? $updatedUser['address'] : ($currentUser['address'] ?? '');
            $card = isset($updatedUser['card']) ? $updatedUser['card'] : ($currentUser['card'] ?? null);
            
            // Handle card field which can be NULL
            $cardValue = $card ? "'".addslashes($card)."'" : "NULL";
            
            // Handle password - only hash if it changed
            $passwordValue = $updatedUser['password'];
            
            // If password is different from what's stored and doesn't look like a hash already,
            // then hash the new password
            $currentPasswordHash = $currentUser['password'];
            if ($passwordValue !== $currentPasswordHash && !$this->isAlreadyHashed($passwordValue)) {
                $passwordValue = password_hash($passwordValue, PASSWORD_DEFAULT);
            }
            
            $sql = "UPDATE users SET 
                    name='".addslashes($updatedUser['name'])."', 
                    surname='".addslashes($surname)."', 
                    email='".addslashes($updatedUser['email'])."', 
                    password='".addslashes($passwordValue)."', 
                    address='".addslashes($address)."', 
                    card=$cardValue, 
                    role='".addslashes($updatedUser['role'])."' 
                    WHERE id='".addslashes($updatedUser['id'])."'";
            
            $this->db->query($sql);
            return true;
        } catch (Exception $e) {
            // Log the error or handle it as needed
            return false;
        }
    }

    /**
     * Check if a string appears to be already hashed
     * @param string $password The password to check
     * @return bool True if already hashed, false otherwise
     */
    private function isAlreadyHashed($password) {
        // Password hash from password_hash() is at least 60 characters
        // and starts with '$2y$'
        return strlen($password) >= 60 && strpos($password, '$2y$') === 0;
    }
    
    /**
     * Verify a password against a hash
     * @param string $password The password to verify
     * @param string $hash The hash to check against
     * @return bool True if password is correct, false otherwise
     */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    public function deleteUser($id) {
        try {
            // First check for related records
            // Check if user has purchases
            $purchaseCheck = "SELECT COUNT(*) as count FROM purchases WHERE user_id='$id'";
            $result = $this->db->fetchOne($purchaseCheck);
            
            if ($result && $result['count'] > 0) {
                // User has purchases, handle accordingly
                throw new Exception("Cannot delete user with existing purchases. Consider deactivating instead.");
            }
            
            // If no related records or if we want to proceed anyway, delete the user
            $sql = "DELETE FROM users WHERE id='$id'";
            $this->db->query($sql);
            return true;
        } catch (Exception $e) {
            throw $e; // Re-throw the exception for the controller to handle
        }
    }

    public function getUserByEmail($email) {
        $sql = "SELECT * FROM users WHERE email='$email'";
        return $this->db->fetchOne($sql);
    }

    public function getUserById($id) {
        $sql = "SELECT * FROM users WHERE id='$id'";
        return $this->db->fetchOne($sql);
    }

    public function getAllUsers() {
        $sql = "SELECT * FROM users";
        return $this->db->fetchAll($sql);
    }
}
?>