<?php
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - MY Clothing Store</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/script.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .product-detail {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 30px;
        }
        
        .product-image {
            width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            object-fit: cover;
        }
        
        .product-info {
            padding: 20px;
        }
        
        .product-name {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .product-category {
            display: inline-block;
            background-color: var(--yellow);
            color: var(--black);
            padding: 5px 15px;
            border-radius: 20px;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .product-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--black);
            margin-bottom: 15px;
        }
        
        .product-actions {
            margin: 20px 0;
        }
        
        .quantity-input {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .quantity-input label {
            margin-right: 10px;
        }
        
        .quantity-input input {
            width: 60px;
            padding: 8px;
            border: 1px solid var(--gray);
            border-radius: 4px;
        }
        
        .stock-status {
            margin-bottom: 15px;
            font-weight: 500;
        }
        
        .back-button {
            margin-top: 20px;
            display: inline-block;
        }
        
        .product-description {
            margin-top: 20px;
            line-height: 1.6;
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
        
        /* Review styling */
        .product-reviews {
            margin-top: 30px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        
        .review {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #eee;
        }
        
        .review-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            align-items: center;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .user-name {
            font-weight: bold;
            color: var(--black);
        }
        
        .user-surname {
            color: var(--black);
        }
        
        .user-role {
            color: var(--dark-gray);
            font-size: 0.9em;
            font-style: italic;
        }
        
        .review-date {
            color: var(--dark-gray);
            font-size: 0.9em;
        }
        
        .rating {
            display: flex;
            margin-bottom: 10px;
        }
        
        .rating .fa-star {
            margin-right: 3px;
        }
        
        .text-warning {
            color: #ffc107;
        }
        
        .text-muted {
            color: #ccc;
        }
        
        .review-text {
            line-height: 1.5;
        }
        
        .add-review {
            margin-top: 30px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        
        .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-top: 5px;
        }
        
        @media (max-width: 768px) {
            .product-detail {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'views/partials/navbar.php'; ?>

    <main class="container">
        <div class="back-button">
            <a href="index.php?controller=product&action=boutique" class="btn btn-dark">
                <i class="fas fa-arrow-left"></i> Back to Products
            </a>
        </div>
        
        <section class="product-detail">
            <div>
                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
            </div>
            
            <div class="product-info">
                <h1 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h1>
                
                <span class="product-category"><?php echo htmlspecialchars($product['category']); ?></span>
                
                <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                
                <div class="stock-status">
                    <?php if ($product['stock'] > 10): ?>
                        <span class="text-success"><i class="fas fa-check-circle"></i> In Stock</span>
                    <?php elseif ($product['stock'] > 0): ?>
                        <span class="text-warning"><i class="fas fa-exclamation-circle"></i> Low Stock (<?php echo $product['stock']; ?> left)</span>
                    <?php else: ?>
                        <span class="text-danger"><i class="fas fa-times-circle"></i> Out of Stock</span>
                    <?php endif; ?>
                </div>
                
                <?php if (isset($_SESSION['user']) && $product['stock'] > 0): ?>
                    <form action="index.php?controller=product&action=addToCart" method="post" class="product-actions">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        
                        <div class="quantity-input">
                            <label for="quantity">Quantity:</label>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
                        </div>
                        
                        <button type="submit" class="btn">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                        
                        <button type="button" class="btn btn-dark" onclick="buyNow(<?php echo $product['id']; ?>)">
                            <i class="fas fa-bolt"></i> Buy Now
                        </button>
                    </form>
                <?php elseif (!isset($_SESSION['user'])): ?>
                    <a href="index.php?controller=auth&action=login" class="btn btn-dark">Login to Purchase</a>
                <?php endif; ?>
                
                <div class="product-description">
                    <h3>Product Description</h3>
                    <p>This premium quality <?php echo strtolower(htmlspecialchars($product['category'])); ?> is designed for comfort and style. Made with high-quality materials, it's perfect for casual wear or special occasions.</p>
                </div>
                
                <?php if (isset($product['comments']) && is_array($product['comments']) && count($product['comments']) > 0): ?>
                    <div class="product-reviews">
                        <h3>Customer Reviews (<?php echo count($product['comments']); ?>)</h3>
                        
                        <?php foreach ($product['comments'] as $comment): ?>
                            <div class="review">
                                <div class="review-header">
                                    <div class="user-info">
                                        <span class="user-name"><?php echo htmlspecialchars($comment['user_name']); ?></span>
                                        <?php if (!empty($comment['surname'])): ?>
                                            <span class="user-surname"><?php echo htmlspecialchars($comment['surname']); ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($comment['role'])): ?>
                                            <span class="user-role">(<?php echo htmlspecialchars(ucfirst($comment['role'])); ?>)</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="review-date">
                                        <?php echo isset($comment['date']) ? $comment['date'] : 'Unknown date'; ?>
                                    </div>
                                </div>
                                
                                <div class="rating">
                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                        <i class="fas fa-star <?php echo $i < $comment['rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                
                                <div class="review-text">
                                    <?php echo htmlspecialchars($comment['text']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['user'])): ?>
                    <div class="add-review">
                        <h3>Add Your Review</h3>
                        <form action="index.php?controller=product&action=comment" method="post">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            
                            <div class="form-group">
                                <label for="rating">Rating:</label>
                                <select name="rating" id="rating" required class="form-control">
                                    <option value="5">5 Stars - Excellent</option>
                                    <option value="4">4 Stars - Very Good</option>
                                    <option value="3">3 Stars - Good</option>
                                    <option value="2">2 Stars - Fair</option>
                                    <option value="1">1 Star - Poor</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="comment">Your Review:</label>
                                <textarea name="comment" id="comment" class="form-control" rows="3" placeholder="Share your experience with this product..." required></textarea>
                            </div>
                            
                            <button type="submit" class="btn">Submit Review</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </section>
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
                &copy; 2025 MY Clothing Store. All rights reserved.
            </div>
        </div>
    </footer>

    <script>
        function toggleMenu() {
            document.querySelector('.nav-menu').classList.toggle('active');
        }
        
        function buyNow(productId) {
            const quantityInput = document.getElementById('quantity');
            const quantity = quantityInput.value;
            
            // Create form element
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'index.php?controller=product&action=addToCart';
            
            // Add product ID input
            const productIdInput = document.createElement('input');
            productIdInput.type = 'hidden';
            productIdInput.name = 'product_id';
            productIdInput.value = productId;
            form.appendChild(productIdInput);
            
            // Add quantity input
            const quantityFormInput = document.createElement('input');
            quantityFormInput.type = 'hidden';
            quantityFormInput.name = 'quantity';
            quantityFormInput.value = quantity;
            form.appendChild(quantityFormInput);
            
            // Add redirect input to go to checkout
            const redirectInput = document.createElement('input');
            redirectInput.type = 'hidden';
            redirectInput.name = 'redirect';
            redirectInput.value = 'checkout';
            form.appendChild(redirectInput);
            
            // Append form to body and submit
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>
</html> 