<?php
require_once 'database.php';

/**
 * Product Model
 * Handles all product-related data operations
 */
class ProductModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getProducts($category = 'All', $minPrice = null, $maxPrice = null) {
        $where = [];
        
        // Add category filter if specified
        if ($category !== 'All') {
            $where[] = "category='$category'";
        }
        
        // Add price range filters if specified
        if ($minPrice !== null && is_numeric($minPrice)) {
            $where[] = "price >= " . floatval($minPrice);
        }
        
        if ($maxPrice !== null && is_numeric($maxPrice)) {
            $where[] = "price <= " . floatval($maxPrice);
        }
        
        // Build the SQL query
        $sql = "SELECT * FROM products";
        
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        return $this->db->fetchAll($sql);
    }

    public function getProductById($id) {
        $sql = "SELECT * FROM products WHERE id='$id'";
        return $this->db->fetchOne($sql);
    }

    public function addProduct($product) {
        $sql = "INSERT INTO products (id, name, category, price, stock, sales, image) VALUES ('{$product['id']}', '{$product['name']}', '{$product['category']}', {$product['price']}, {$product['stock']}, {$product['sales']}, '{$product['image']}')";
        $this->db->query($sql);
    }

    public function updateProduct($updatedProduct) {
        $sql = "UPDATE products SET name='{$updatedProduct['name']}', category='{$updatedProduct['category']}', price={$updatedProduct['price']}, stock={$updatedProduct['stock']}, sales={$updatedProduct['sales']}, image='{$updatedProduct['image']}' WHERE id='{$updatedProduct['id']}'";
        $this->db->query($sql);
    }

    public function deleteProduct($id) {
        try {
            $sql = "DELETE FROM products WHERE id='$id'";
            $this->db->query($sql);
            return true;
        } catch (Exception $e) {
            // Log error
            error_log("Error deleting product ID $id: " . $e->getMessage());
            return false;
        }
    }

    public function addComment($productId, $comment, $rating) {
        $userName = $_SESSION['user']['name'];
        $commentId = uniqid();
        $sql = "INSERT INTO product_comments (id, product_id, user_name, comment, rating) VALUES ('$commentId', '$productId', '$userName', '$comment', $rating)";
        $this->db->query($sql);
    }

    public function getComments($productId) {
        $sql = "SELECT pc.*, u.name as user_name, u.surname, u.role 
                FROM product_comments pc 
                LEFT JOIN users u ON pc.user_name = u.name 
                WHERE pc.product_id='$productId' 
                ORDER BY pc.id DESC";
        
        $comments = $this->db->fetchAll($sql);
        
        // Format the comments for display
        foreach ($comments as &$comment) {
            // Set default user info in case the user doesn't exist anymore
            if (empty($comment['user_name'])) {
                $comment['user_name'] = 'Anonymous';
            }
            
            // Format the comment data
            $comment['text'] = $comment['comment'];
            $comment['date'] = date('F j, Y', strtotime($comment['id'])); // Using ID to approximate date
        }
        
        return $comments;
    }

    public function addPurchase($purchase) {
        $purchaseId = $purchase['id'];
        $sql = "INSERT INTO purchases (id, user_id, purchase_date) VALUES ('$purchaseId', '{$purchase['userId']}', '{$purchase['date']}')";
        $this->db->query($sql);
        foreach ($purchase['items'] as $item) {
            $itemId = uniqid();
            $sql = "INSERT INTO purchase_items (id, purchase_id, product_id, quantity) VALUES ('$itemId', '$purchaseId', '{$item['product_id']}', {$item['quantity']})";
            $this->db->query($sql);
        }
    }

    public function getPurchasesByUser($userId) {
        $sql = "SELECT * FROM purchases WHERE user_id='$userId'";
        return $this->db->fetchAll($sql);
    }

    public function getPurchaseItems($purchaseId) {
        $sql = "SELECT * FROM purchase_items WHERE purchase_id='$purchaseId'";
        return $this->db->fetchAll($sql);
    }

    public function getTopSalesByCategory() {
        $categories = ['Chemises', 'Pantalons', 'Vestes'];
        $topSales = [];
        foreach ($categories as $category) {
            $sql = "SELECT * FROM products WHERE category='$category' ORDER BY sales DESC LIMIT 1";
            $product = $this->db->fetchOne($sql);
            if ($product) {
                $topSales[$category] = $product;
            }
        }
        return $topSales;
    }

    // New methods for product requests
    public function addProductRequest($product) {
        // Store the product request with pending status
        $sql = "INSERT INTO product_requests (id, name, category, price, stock, image, user_id, status, request_date) 
                VALUES ('{$product['id']}', '{$product['name']}', '{$product['category']}', {$product['price']}, 
                {$product['stock']}, '{$product['image']}', '{$product['user_id']}', 'pending', NOW())";
        $this->db->query($sql);
    }

    public function getPendingProductRequests() {
        $sql = "SELECT pr.*, u.name as requester_name FROM product_requests pr 
                JOIN users u ON pr.user_id = u.id 
                WHERE pr.status='pending'";
        return $this->db->fetchAll($sql);
    }

    public function approveProductRequest($id) {
        // Get the request details
        $sql = "SELECT * FROM product_requests WHERE id='$id'";
        $request = $this->db->fetchOne($sql);
        
        if ($request) {
            // Add the product to the products table
            $this->addProduct([
                'id' => $request['id'],
                'name' => $request['name'],
                'category' => $request['category'],
                'price' => $request['price'],
                'stock' => $request['stock'],
                'sales' => 0,
                'image' => $request['image'],
                'comments' => []
            ]);
            
            // Update request status to approved
            $sql = "UPDATE product_requests SET status='approved' WHERE id='$id'";
            $this->db->query($sql);
            return true;
        }
        return false;
    }

    public function rejectProductRequest($id) {
        $sql = "UPDATE product_requests SET status='rejected' WHERE id='$id'";
        $this->db->query($sql);
    }

    // Get all purchases with details
    public function getAllPurchases() {
        // Get all purchases from the database
        $sql = "SELECT p.*, u.name as user_name, u.email FROM purchases p JOIN users u ON p.user_id = u.id ORDER BY purchase_date DESC";
        $purchases = $this->db->fetchAll($sql);
        
        // For each purchase, get the items
        foreach ($purchases as &$purchase) {
            $sql = "SELECT pi.*, pr.name, pr.price, pr.category, pr.image FROM purchase_items pi 
                   JOIN products pr ON pi.product_id = pr.id 
                   WHERE pi.purchase_id = '{$purchase['id']}'";
            $items = $this->db->fetchAll($sql);
            $purchase['items'] = $items;
            
            // Calculate purchase total
            $total = 0;
            foreach ($items as $item) {
                $total += $item['price'] * $item['quantity'];
            }
            $purchase['total'] = $total;
        }
        
        return $purchases;
    }
}
?>