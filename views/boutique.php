<?php
require_once 'models/ProductModel.php';
$productModel = new ProductModel();
$category = isset($_GET['category']) ? $_GET['category'] : 'All';
$products = $productModel->getProducts($category);
$topSales = $productModel->getTopSalesByCategory();
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

// Calculate cart total if user has a cart
$cartTotal = 0;
if (isset($_SESSION['user']) && isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $product = $productModel->getProductById($item['product_id']);
        $cartTotal += $product['price'] * $item['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MY Clothing Store - Boutique</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .filter-container {
            background-color: var(--white);
            border-radius: 8px;
            padding: 0.75rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-container label {
            margin-right: 1rem;
            font-weight: 600;
            color: var(--black);
            display: block;
            margin-bottom: 0.5rem;
        }
        
        .filter-control {
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
        }
        
        .price-inputs {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .price-separator {
            color: var(--dark-gray);
            font-weight: bold;
        }
        
        #min-price, #max-price {
            width: 100px;
            text-align: center;
        }
        
        #apply-filters {
            background-color: var(--yellow);
            color: var(--black);
            border: none;
            cursor: pointer;
            padding: 0.5rem 0.75rem;
            border-radius: 4px;
            font-weight: bold;
        }
        
        #apply-filters:hover {
            background-color: #e5c000;
        }
        
        .top-sales {
            background-color: var(--white);
            border-radius: 8px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .top-sales h3 {
            margin-top: 0;
            color: var(--black);
            border-bottom: 2px solid var(--yellow);
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }
        
        .top-sales-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 1rem;
        }
        
        .top-sale-item {
            text-align: center;
            padding: 0.75rem;
            border-radius: 8px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background-color: var(--white);
            border: 1px solid #eee;
        }
        
        .top-sale-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
            border-color: var(--yellow);
        }
        
        .top-sale-item img {
            width: 100%;
            height: 140px;
            object-fit: cover;
            border-radius: 6px;
            margin-bottom: 0.5rem;
        }
        
        .top-sale-item h4 {
            font-size: 0.95rem;
            margin: 0.5rem 0;
        }
        
        .products-section {
            background-color: var(--white);
            border-radius: 8px;
            padding: 1.25rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            border-bottom: 2px solid var(--yellow);
            padding-bottom: 0.5rem;
        }
        
        .section-header h3 {
            font-size: 1.2rem;
            margin: 0;
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.25rem;
            margin-top: 1rem;
        }
        
        .product-card {
            background-color: var(--white);
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #eee;
            position: relative;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .product-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-color: var(--yellow);
        }
        
        .product-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        
        .product-info {
            padding: 0.75rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        
        .product-card h4 {
            font-size: 0.95rem;
            margin: 0 0 0.5rem;
            color: var(--black);
        }
        
        .product-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
            padding-top: 0.5rem;
            border-top: 1px solid #f0f0f0;
        }
        
        .product-actions .btn-sm {
            padding: 4px 8px;
            font-size: 0.75rem;
        }
        
        .quantity-input {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .quantity-input input {
            width: 40px;
            text-align: center;
            padding: 3px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.75rem;
        }
        
        .btn-primary {
            background-color: var(--yellow);
            color: var(--black);
        }
        
        .btn-dark {
            background-color: var(--dark-gray);
            color: var(--white);
        }
        
        .product-rating {
            margin: 8px 0;
        }
        
        .stars {
            display: flex;
            align-items: center;
            font-size: 0.9rem;
        }
        
        .stars i {
            margin-right: 2px;
        }
        
        .text-warning {
            color: #ffc107;
        }
        
        .text-muted {
            color: #ccc;
        }
        
        .rating-text {
            margin-left: 5px;
            font-size: 0.8rem;
            color: var(--dark-gray);
        }
        
        .cart-summary {
            background-color: var(--white);
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 1.25rem;
            margin-top: 1.5rem;
            border-top: 3px solid var(--yellow);
        }
        
        .cart-summary h3 {
            margin-top: 0;
            border-bottom: 1px solid #eee;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
            color: var(--black);
            font-size: 1.2rem;
        }
        
        .cart-total {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--black);
        }
        
        .price {
            color: var(--black);
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.3rem;
        }
        
        .stock {
            color: #666;
            margin-bottom: 0.5rem;
            font-size: 0.8rem;
        }
        
        .category-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            background-color: var(--yellow);
            color: var(--black);
            padding: 3px 8px;
            border-radius: 15px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .btn {
            padding: 6px 12px;
            font-size: 0.85rem;
        }
        
        @media (max-width: 768px) {
            .filter-container {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .filter-group {
                width: 100%;
            }
            
            .filter-container label {
                margin-bottom: 0.5rem;
            }
            
            .price-inputs {
                flex-wrap: wrap;
            }
            
            #min-price, #max-price {
                width: calc(50% - 1rem);
            }
            
            #apply-filters {
                width: 100%;
                margin-top: 0.5rem;
            }
            
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
                gap: 1rem;
            }
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
    </style>
</head>
<body>
    <?php include 'views/partials/navbar.php'; ?>

    <header class="hero">
        <div class="hero-content">
            <h1>Our Boutique</h1>
            <p>Discover our curated collection of high-quality clothing</p>
        </div>
    </header>

    <main class="container">
        <section class="boutique-section">
            <!-- Category Filter -->
            <div class="filter-container">
                <div class="filter-group">
                    <label for="category-filter">Filter by Category:</label>
                    <select id="category-filter" class="filter-control">
                        <option value="All" <?php echo $category == 'All' ? 'selected' : ''; ?>>All Categories</option>
                        <option value="Chemises" <?php echo $category == 'Chemises' ? 'selected' : ''; ?>>Chemises</option>
                        <option value="Pantalons" <?php echo $category == 'Pantalons' ? 'selected' : ''; ?>>Pantalons</option>
                        <option value="Vestes" <?php echo $category == 'Vestes' ? 'selected' : ''; ?>>Vestes</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="min-price">Price Range:</label>
                    <div class="price-inputs">
                        <input type="number" id="min-price" class="filter-control" placeholder="Min" min="0" value="<?php echo isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : ''; ?>">
                        <span class="price-separator">-</span>
                        <input type="number" id="max-price" class="filter-control" placeholder="Max" min="0" value="<?php echo isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : ''; ?>">
                        <button id="apply-filters" class="btn">Apply</button>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Handle filter application
                    document.getElementById('apply-filters').addEventListener('click', applyFilters);
                    
                    // Also apply filters when pressing Enter in the price fields
                    document.getElementById('min-price').addEventListener('keyup', function(e) {
                        if (e.key === 'Enter') applyFilters();
                    });
                    document.getElementById('max-price').addEventListener('keyup', function(e) {
                        if (e.key === 'Enter') applyFilters();
                    });
                    
                    // Handle category change
                    document.getElementById('category-filter').addEventListener('change', applyFilters);
                    
                    function applyFilters() {
                        const category = document.getElementById('category-filter').value;
                        const minPrice = document.getElementById('min-price').value;
                        const maxPrice = document.getElementById('max-price').value;
                        
                        let url = 'index.php?controller=product&action=boutique';
                        
                        // Add category if not "All"
                        if (category && category !== 'All') {
                            url += '&category=' + encodeURIComponent(category);
                        }
                        
                        // Add price filters if provided
                        if (minPrice) {
                            url += '&min_price=' + encodeURIComponent(minPrice);
                        }
                        
                        if (maxPrice) {
                            url += '&max_price=' + encodeURIComponent(maxPrice);
                        }
                        
                        window.location.href = url;
                    }
                });
            </script>

            <!-- Top Sales Section -->
            <div class="top-sales">
                <h3><i class="fas fa-crown" style="color: var(--yellow);"></i> Top Sales</h3>
                <div class="top-sales-grid">
                    <?php foreach ($topSales as $category => $product): ?>
                        <div class="product-card">
                            <div class="category-badge"><?php echo htmlspecialchars($category); ?></div>
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <div class="product-info">
                                <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                                <div class="price">$<?php echo number_format($product['price'], 2); ?></div>
                                <div class="product-actions">
                                    <a href="index.php?controller=product&action=productDetail&id=<?php echo $product['id']; ?>" class="btn btn-sm">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Products Section -->
            <div class="products-section">
                <div class="section-header">
                    <h3><?php echo $category == 'All' ? 'All Products' : $category; ?></h3>
                    <?php if (isset($_SESSION['user'])): ?>
                        <a href="index.php?controller=product&action=cart" class="btn">
                            <i class="fas fa-shopping-cart"></i> View Cart
                            <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                                (<?php echo count($_SESSION['cart']); ?>)
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>
                </div>

                <div class="products-grid">
                    <?php if (empty($products)): ?>
                        <p>No products found in this category.</p>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <div class="product-card">
                                <div class="category-badge"><?php echo htmlspecialchars($product['category']); ?></div>
                                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <div class="product-info">
                                    <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                                    <div class="price">$<?php echo number_format($product['price'], 2); ?></div>
                                    <div class="stock">
                                        <?php if ($product['stock'] > 10): ?>
                                            <span class="text-success"><i class="fas fa-check-circle"></i> In Stock</span>
                                        <?php elseif ($product['stock'] > 0): ?>
                                            <span class="text-warning"><i class="fas fa-exclamation-circle"></i> Low Stock (<?php echo $product['stock']; ?>)</span>
                                        <?php else: ?>
                                            <span class="text-danger"><i class="fas fa-times-circle"></i> Out of Stock</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php
                                    // Get product comments and calculate average rating
                                    $comments = $productModel->getComments($product['id']);
                                    $avgRating = 0;
                                    $ratingCount = count($comments);
                                    
                                    if ($ratingCount > 0) {
                                        $ratingSum = 0;
                                        foreach ($comments as $comment) {
                                            $ratingSum += $comment['rating'];
                                        }
                                        $avgRating = round($ratingSum / $ratingCount, 1);
                                    }
                                    ?>
                                    
                                    <?php if ($ratingCount > 0): ?>
                                    <div class="product-rating">
                                        <div class="stars">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <?php if ($i <= $avgRating): ?>
                                                    <i class="fas fa-star text-warning"></i>
                                                <?php elseif ($i - 0.5 <= $avgRating): ?>
                                                    <i class="fas fa-star-half-alt text-warning"></i>
                                                <?php else: ?>
                                                    <i class="far fa-star text-muted"></i>
                                                <?php endif; ?>
                                            <?php endfor; ?>
                                            <span class="rating-text">(<?php echo $avgRating; ?> - <?php echo $ratingCount; ?> reviews)</span>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="product-actions">
                                        <a href="index.php?controller=product&action=productDetail&id=<?php echo $product['id']; ?>" class="btn btn-sm">View Details</a>
                                        
                                        <?php if (isset($_SESSION['user']) && $product['stock'] > 0): ?>
                                            <form action="index.php?controller=product&action=addToCart" method="post" class="add-to-cart">
                                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                <div class="quantity-input">
                                                    <input type="number" id="quantity-<?php echo $product['id']; ?>" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
                                                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-cart-plus"></i></button>
                                                </div>
                                            </form>
                                        <?php elseif (!isset($_SESSION['user'])): ?>
                                            <a href="index.php?controller=auth&action=login" class="btn btn-dark btn-sm">Login</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if (isset($_SESSION['user']) && isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                <div class="cart-summary">
                    <h3>Your Cart Summary</h3>
                    <p>You have <?php echo count($_SESSION['cart']); ?> item(s) in your cart.</p>
                    <p class="cart-total">Total: $<?php echo number_format($cartTotal, 2); ?></p>
                    <a href="index.php?controller=product&action=cart" class="btn">View Cart</a>
                    <a href="index.php?controller=product&action=checkout" class="btn">Checkout</a>
                </div>
            <?php endif; ?>
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
    </script>
</body>
</html>