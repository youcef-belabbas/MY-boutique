<?php
require_once 'database.php';

/**
 * Purchase Model
 * Handles all purchase-related data operations
 */
class PurchaseModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Add a new purchase
     * @param array $purchase Purchase data
     * @return int|bool Purchase ID on success, false on failure
     */
    public function addPurchase($purchase) {
        // When using database, use:
        // return $this->insert($purchase);
        
        // For now, use session data
        $_SESSION['purchases'][] = $purchase;
        return $purchase['id'] ?? count($_SESSION['purchases']);
    }

    /**
     * Get purchases for a specific user
     * @param string $userId User ID
     * @return array Array of purchases
     */
    public function getPurchasesByUser($userId) {
        // When using database, use:
        // return $this->findWhere('user_id = :userId', ['userId' => $userId]);
        
        // For now, use session data
        return array_filter($_SESSION['purchases'] ?? [], function($purchase) use ($userId) {
            return $purchase['userId'] == $userId;
        });
    }

    /**
     * Get all purchases
     * @return array Array of all purchases
     */
    public function getAllPurchases() {
        // When using database, use:
        // return $this->findAll();
        
        // For now, use session data
        return $_SESSION['purchases'] ?? [];
    }
}
?>