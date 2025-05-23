<?php
require_once 'models/ProductModel.php';
$productModel = new ProductModel();
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$cartItems = [];
$cartTotal = 0;

if (isset($_SESSION['user']) && isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    foreach ($_SESSION['cart'] as $item) {
        $product = $productModel->getProductById($item['product_id']);
        if ($product) {
            $cartItem = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'image' => $product['image'],
                'category' => $product['category'],
                'quantity' => $item['quantity'],
                'subtotal' => $product['price'] * $item['quantity']
            ];
            $cartItems[] = $cartItem;
            $cartTotal += $cartItem['subtotal'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - MY Clothing</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .cart-container {
            background-color: var(--white);
            border-radius: 8px;
            padding: 1.25rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .cart-empty {
            text-align: center;
            padding: 2rem;
        }
        
        .cart-empty i {
            font-size: 3rem;
            color: var(--gray);
            margin-bottom: 1rem;
            display: block;
        }
        
        .cart-heading {
            border-bottom: 2px solid var(--yellow);
            padding-bottom: 0.75rem;
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
        }
        
        .cart-items {
            border-bottom: 1px solid #eee;
        }
        
        .cart-item {
            display: grid;
            grid-template-columns: 80px 1fr auto;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid #f0f0f0;
            align-items: center;
        }
        
        .cart-item:last-child {
            border-bottom: none;
        }
        
        .cart-item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
        }
        
        .cart-item-details h4 {
            margin: 0 0 0.5rem;
            font-size: 1rem;
        }
        
        .cart-item-details .price {
            font-weight: 600;
            color: var(--black);
        }
        
        .cart-item-details .category {
            display: inline-block;
            background-color: var(--light-gray);
            padding: 2px 6px;
            border-radius: 4px;
            margin-right: 0.5rem;
            font-size: 0.8rem;
        }
        
        .cart-item-actions {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 0.5rem;
        }
        
        .cart-item-quantity {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .quantity-btn {
            width: 24px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: var(--light-gray);
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
        }
        
        .quantity-btn:hover {
            background-color: var(--gray);
        }
        
        .quantity-display {
            width: 30px;
            text-align: center;
        }
        
        .remove-btn {
            background-color: transparent;
            color: var(--dark-gray);
            border: none;
            font-size: 0.8rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .remove-btn:hover {
            color: #ff3333;
        }
        
        .subtotal {
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .cart-summary {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .cart-totals {
            background-color: var(--light-gray);
            padding: 1rem;
            border-radius: 8px;
        }
        
        .cart-total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px dashed #ddd;
        }
        
        .cart-total-row.final {
            font-weight: 600;
            font-size: 1.1rem;
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
            margin-top: 1rem;
        }
        
        .checkout-actions {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .checkout-btn {
            padding: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
            width: 100%;
            text-align: center;
        }
        
        .continue-shopping {
            font-size: 0.9rem;
            display: block;
            text-align: center;
            text-decoration: none;
            margin-top: 0.5rem;
            color: var(--dark-gray);
        }
        
        .continue-shopping:hover {
            color: var(--black);
            text-decoration: underline;
        }
        
        .cart-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: var(--yellow);
            color: var(--black);
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            font-weight: bold;
            position: relative;
            top: -1px;
        }
        
        @media (max-width: 768px) {
            .cart-summary {
                grid-template-columns: 1fr;
            }
            
            .cart-item {
                grid-template-columns: 60px 1fr;
                grid-template-areas:
                    "image details"
                    "actions actions";
                gap: 0.75rem;
            }
            
            .cart-item-image {
                grid-area: image;
                width: 60px;
                height: 60px;
            }
            
            .cart-item-details {
                grid-area: details;
            }
            
            .cart-item-actions {
                grid-area: actions;
                flex-direction: row;
                justify-content: space-between;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include 'views/partials/navbar.php'; ?>

    <header class="hero">
        <div class="hero-content">
            <h1>Your Shopping Cart</h1>
            <p>Review your items and proceed to checkout</p>
        </div>
    </header>

    <main class="container">
        <div class="cart-container">
            <?php if (empty($cartItems)): ?>
                <div class="cart-empty">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>Your cart is empty</h3>
                    <p>Looks like you haven't added any products to your cart yet.</p>
                    <a href="index.php?controller=product&action=boutique" class="btn">Start Shopping</a>
                </div>
            <?php else: ?>
                <h2 class="cart-heading">Shopping Cart (<?php echo count($cartItems); ?> items)</h2>
                
                <div class="cart-items">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="cart-item">
                            <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="cart-item-image">
                            
                            <div class="cart-item-details">
                                <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                <span class="category"><?php echo htmlspecialchars($item['category']); ?></span>
                                <div class="price">$<?php echo number_format($item['price'], 2); ?> each</div>
                            </div>
                            
                            <div class="cart-item-actions">
                                <div class="cart-item-quantity">
                                    <form action="index.php?controller=product&action=updateCart" method="post">
                                        <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                        <input type="hidden" name="action" value="decrease">
                                        <button type="submit" class="quantity-btn">-</button>
                                    </form>
                                    <span class="quantity-display"><?php echo $item['quantity']; ?></span>
                                    <form action="index.php?controller=product&action=updateCart" method="post">
                                        <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                        <input type="hidden" name="action" value="increase">
                                        <button type="submit" class="quantity-btn">+</button>
                                    </form>
                                </div>
                                
                                <div class="subtotal">$<?php echo number_format($item['subtotal'], 2); ?></div>
                                
                                <form action="index.php?controller=product&action=removeFromCart" method="post">
                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" class="remove-btn">
                                        <i class="fas fa-trash-alt"></i> Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="cart-summary">
                    <div class="cart-totals">
                        <h3>Cart Summary</h3>
                        <div class="cart-total-row">
                            <span>Subtotal:</span>
                            <span>$<?php echo number_format($cartTotal, 2); ?></span>
                        </div>
                        <div class="cart-total-row">
                            <span>Shipping:</span>
                            <span>$5.00</span>
                        </div>
                        <div class="cart-total-row final">
                            <span>Total:</span>
                            <span>$<?php echo number_format($cartTotal + 5, 2); ?></span>
                        </div>
                    </div>
                    
                    <div class="checkout-actions">
                        <a href="index.php?controller=product&action=checkout" class="btn checkout-btn">
                            Proceed to Checkout
                        </a>
                        
                        <form action="index.php?controller=product&action=clearCart" method="post">
                            <button type="submit" class="btn btn-dark checkout-btn">
                                Clear Cart
                            </button>
                        </form>
                        
                        <a href="index.php?controller=product&action=boutique" class="continue-shopping">
                            <i class="fas fa-arrow-left"></i> Continue Shopping
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h3 class="footer-heading">MY Clothing</h3>
                <p>Your destination for high-quality fashion and stylish apparel.</p>
            </div>
            <div class="footer-section">
                <h3 class="footer-heading">Quick Links</h3>
                <a href="index.php" class="footer-link">Home</a>
                <a href="index.php?controller=product&action=boutique" class="footer-link">Shop</a>
                <a href="index.php?controller=event&action=events" class="footer-link">Events</a>
            </div>
            <div class="footer-section">
                <h3 class="footer-heading">Account</h3>
                <?php if ($user): ?>
                    <a href="index.php?controller=auth&action=account" class="footer-link">My Account</a>
                    <a href="index.php?controller=auth&action=logout" class="footer-link">Logout</a>
                <?php else: ?>
                    <a href="index.php?controller=auth&action=login" class="footer-link">Login</a>
                    <a href="index.php?controller=auth&action=register" class="footer-link">Register</a>
                <?php endif; ?>
            </div>
            <div class="copyright">
                &copy; <?= date('Y') ?> MY Clothing Store. All rights reserved.
            </div>
        </div>
    </footer>

    <script>
        function toggleMenu() {
            document.querySelector('.nav-menu').classList.toggle('active');
        }
    </script>
</body>
</html> 