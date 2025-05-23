<?php
require_once 'models/ProductModel.php';

class ProductController {
    private $productModel;

    public function __construct() {
        $this->productModel = new ProductModel();
    }


    public function boutique() {
        $category = isset($_GET['category']) ? $_GET['category'] : 'All';
        $minPrice = isset($_GET['min_price']) ? floatval($_GET['min_price']) : null;
        $maxPrice = isset($_GET['max_price']) ? floatval($_GET['max_price']) : null;
        
        // Get products with filters
        $products = $this->productModel->getProducts($category, $minPrice, $maxPrice);
        include 'views/boutique.php';
    }

    // Display product details
    public function productDetail() {
        if (!isset($_GET['id'])) {
            header('Location: index.php?controller=product&action=boutique');
            return;
        }
        
        $productId = $_GET['id'];
        $product = $this->productModel->getProductById($productId);
        
        if (!$product) {
            header('Location: index.php?controller=product&action=boutique');
            return;
        }
        
        // Get comments for this product
        $comments = $this->productModel->getComments($productId);
        $product['comments'] = $comments;
        
        include 'views/product_detail.php';
    }

    // Display the shopping cart
    public function cart() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=auth&action=login');
            return;
        }
        include 'views/cart.php';
    }

    public function addToCart() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=auth&action=login');
            return;
        }

        // Get product ID from either POST or GET parameters
        $productId = isset($_POST['product_id']) ? $_POST['product_id'] : (isset($_GET['product_id']) ? $_GET['product_id'] : null);
        
        if (!$productId) {
            header('Location: index.php?controller=product&action=boutique');
            return;
        }
        
        // Get quantity from POST or default to 1
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        if ($quantity < 1) $quantity = 1;
        
        $product = $this->productModel->getProductById($productId);
        if ($product && $product['stock'] > 0) {
            // Cap quantity to available stock
            if ($quantity > $product['stock']) {
                $quantity = $product['stock'];
            }
            
            $found = false;
            if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as &$item) {
                    if ($item['product_id'] == $productId) {
                        $item['quantity'] += $quantity;
                        $found = true;
                        break;
                    }
                }
            } else {
                $_SESSION['cart'] = [];
            }
            
            if (!$found) {
                $_SESSION['cart'][] = ['product_id' => $productId, 'quantity' => $quantity];
            }
        }
        
        // Check if we need to redirect to checkout (Buy Now functionality)
        if (isset($_POST['redirect']) && $_POST['redirect'] === 'checkout') {
            header('Location: index.php?controller=product&action=checkout');
            return;
        }
        
        // Redirect to product details if we came from there, otherwise to boutique
        $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        if (strpos($referrer, 'action=productDetail') !== false) {
            header('Location: index.php?controller=product&action=productDetail&id=' . $productId);
        } else {
            header('Location: index.php?controller=product&action=cart');
        }
    }

    public function updateCart() {
        if (!isset($_SESSION['user']) || !isset($_SESSION['cart'])) {
            header('Location: index.php?controller=product&action=boutique');
            return;
        }
        
        $productId = isset($_POST['product_id']) ? $_POST['product_id'] : null;
        $action = isset($_POST['action']) ? $_POST['action'] : null;
        
        if (!$productId || !$action) {
            header('Location: index.php?controller=product&action=cart');
            return;
        }
        
        $product = $this->productModel->getProductById($productId);
        if (!$product) {
            header('Location: index.php?controller=product&action=cart');
            return;
        }
        
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['product_id'] == $productId) {
                if ($action === 'increase') {
                    // Check if we can increase (within stock limits)
                    if ($item['quantity'] < $product['stock']) {
                        $item['quantity']++;
                    }
                } else if ($action === 'decrease') {
                    // Decrease quantity
                    $item['quantity']--;
                    
                    // If quantity is zero, remove the item
                    if ($item['quantity'] <= 0) {
                        $_SESSION['cart'] = array_filter($_SESSION['cart'], function($cartItem) use ($productId) {
                            return $cartItem['product_id'] != $productId;
                        });
                    }
                }
                break;
            }
        }
        
        header('Location: index.php?controller=product&action=cart');
    }

    public function removeFromCart() {
        if (!isset($_SESSION['user']) || !isset($_SESSION['cart'])) {
            header('Location: index.php?controller=product&action=boutique');
            return;
        }
        
        $productId = isset($_POST['product_id']) ? $_POST['product_id'] : null;
        
        if (!$productId) {
            header('Location: index.php?controller=product&action=cart');
            return;
        }
        
        $_SESSION['cart'] = array_filter($_SESSION['cart'], function($item) use ($productId) {
            return $item['product_id'] != $productId;
        });
        
        header('Location: index.php?controller=product&action=cart');
    }

    public function purchaseNow() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=auth&action=login');
            return;
        }
        
        $productId = $_GET['product_id'];
        $quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;
        
        // If quantity is specified in the form 
        if (isset($_POST['quantity'])) {
            $quantity = (int)$_POST['quantity'];
        }
        
        $product = $this->productModel->getProductById($productId);
        
        if ($product && $product['stock'] >= $quantity) {
            // Add the item to the cart
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
            
            $found = false;
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['product_id'] == $productId) {
                    $item['quantity'] += $quantity;
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $_SESSION['cart'][] = ['product_id' => $productId, 'quantity' => $quantity];
            }
            
            // Redirect to the cart page
            header('Location: index.php?controller=product&action=cart&message=Product added to cart successfully!');
        } else {
            // Redirect with error message if product not found or insufficient stock
            header('Location: index.php?controller=product&action=boutique&error=Unable to add product to cart. Check if product exists or has sufficient stock.');
        }
    }

    public function checkout() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=auth&action=login');
            return;
        }
        $purchase = [
            'id' => uniqid(),
            'userId' => $_SESSION['user']['id'],
            'items' => $_SESSION['cart'],
            'date' => date('Y-m-d H:i:s')
        ];
        foreach ($_SESSION['cart'] as $item) {
            $product = $this->productModel->getProductById($item['product_id']);
            if ($product) {
                // Update session data
                if (isset($_SESSION['products'])) {
                    foreach ($_SESSION['products'] as &$p) {
                        if ($p['id'] == $item['product_id']) {
                            $p['stock'] -= $item['quantity'];
                            $p['sales'] += $item['quantity'];
                            break;
                        }
                    }
                }
                
                // Update database record
                $updatedProduct = [
                    'id' => $item['product_id'],
                    'name' => $product['name'],
                    'category' => $product['category'],
                    'price' => $product['price'],
                    'stock' => $product['stock'] - $item['quantity'],
                    'sales' => $product['sales'] + $item['quantity'],
                    'image' => $product['image'],
                    'comments' => $product['comments'] ?? []
                ];
                $this->productModel->updateProduct($updatedProduct);
            }
        }
        $this->productModel->addPurchase($purchase);
        $_SESSION['cart'] = [];
        header('Location: index.php?controller=product&action=boutique&message=Checkout completed successfully!');
    }

    public function comment() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'client') {
            header('Location: index.php?controller=auth&action=login');
            return;
        }
        $productId = $_POST['product_id'];
        $comment = $_POST['comment'];
        $rating = (int)$_POST['rating'];
        $this->productModel->addComment($productId, $comment, $rating);
        header('Location: index.php?controller=product&action=boutique');
    }

    public function addProduct() {
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['it', 'admin', 'commercial'])) {
            header('Location: index.php?controller=auth&action=login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $product = [
                    'id' => uniqid(),
                    'name' => $_POST['name'],
                    'category' => $_POST['category'],
                    'price' => (float)$_POST['price'],
                    'stock' => (int)$_POST['stock'],
                    'sales' => 0,
                    'image' => $_POST['image'],
                    'comments' => []
                ];

                // If user is admin, directly add the product
                if ($_SESSION['user']['role'] === 'admin') {
                    $this->productModel->addProduct($product);
                    header('Location: index.php?controller=product&action=manage&message=Product added successfully');
                } else {
                    // For non-admin users, create a product request
                    $product['user_id'] = $_SESSION['user']['id'];
                    $this->productModel->addProductRequest($product);
                    header('Location: index.php?controller=product&action=boutique&message=Product request sent to admin for approval');
                }
            } catch (Exception $e) {
                header('Location: index.php?controller=product&action=manage&error=' . urlencode('Error adding product: ' . $e->getMessage()));
            }
        } else {
            include 'views/manage_products.php';
        }
    }

    public function updateProduct() {
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['it', 'admin'])) {
            header('Location: index.php?controller=auth&action=login');
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product = [
                'id' => $_POST['id'],
                'name' => $_POST['name'],
                'category' => $_POST['category'],
                'price' => (float)$_POST['price'],
                'stock' => (int)$_POST['stock'],
                'sales' => $this->productModel->getProductById($_POST['id'])['sales'],
                'image' => $_POST['image'],
                'comments' => $this->productModel->getProductById($_POST['id'])['comments']
            ];
            $this->productModel->updateProduct($product);
            header('Location: index.php?controller=product&action=manage');
        } else {
            $product = $this->productModel->getProductById($_GET['id']);
            include 'views/manage_products.php';
        }
    }

    public function deleteProduct() {
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['it', 'admin'])) {
            header('Location: index.php?controller=auth&action=login');
            return;
        }
        
        try {
            if (!isset($_GET['id']) || empty($_GET['id'])) {
                throw new Exception('No product ID provided');
            }
            
            $productId = $_GET['id'];
            $result = $this->productModel->deleteProduct($productId);
            
            if ($result) {
                header('Location: index.php?controller=product&action=manage&message=Product deleted successfully');
            } else {
                header('Location: index.php?controller=product&action=manage&error=Failed to delete product');
            }
        } catch (Exception $e) {
            header('Location: index.php?controller=product&action=manage&error=' . urlencode('Error deleting product: ' . $e->getMessage()));
        }
    }

    public function manage() {
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['it', 'admin'])) {
            header('Location: index.php?controller=auth&action=login');
            return;
        }
        include 'views/manage_products.php';
    }

    public function clearCart() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=auth&action=login');
            return;
        }
        
        $_SESSION['cart'] = [];
        header('Location: index.php?controller=product&action=cart');
    }
}
?>