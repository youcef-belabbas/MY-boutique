<?php
require_once 'models/ProductModel.php';
$productModel = new ProductModel();
$products = $productModel->getProducts();
$editProduct = isset($_GET['id']) ? $productModel->getProductById($_GET['id']) : null;
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - MY Clothing</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .product-form {
            background-color: var(--white);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .product-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .product-item {
            background-color: var(--white);
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px;
            position: relative;
            transition: transform 0.3s, box-shadow 0.3s;
            border: 1px solid var(--gray);
        }
        
        .product-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-color: var(--yellow);
        }
        
        .product-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }
        
        .product-info {
            margin: 10px 0;
        }
        
        .product-category {
            background-color: var(--light-gray);
            color: var(--black);
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.85rem;
            display: inline-block;
            margin-bottom: 5px;
        }
        
        .product-price {
            color: var(--black);
            font-weight: 600;
            font-size: 1.1rem;
            margin: 5px 0;
        }
        
        .product-stock {
            color: var(--dark-gray);
            font-size: 0.9rem;
        }
        
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 6px;
        }
        
        .section-header {
            border-bottom: 2px solid var(--yellow);
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include 'views/partials/navbar.php'; ?>

    <header class="hero">
        <div class="hero-content">
            <h1>Manage Products</h1>
            <p>Add, edit, and manage the products in your store.</p>
        </div>
    </header>

    <main class="container">
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_GET['message']) ?>
            </div>
        <?php endif; ?>
        
        <section class="product-form">
            <h2 class="section-header"><?= $editProduct ? 'Edit Product' : 'Add Product'; ?></h2>
            <form action="index.php?controller=product&action=<?= $editProduct ? 'updateProduct' : 'addProduct'; ?>" method="post">
                <?php if ($editProduct): ?>
                    <input type="hidden" name="id" value="<?= $editProduct['id']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="name" class="form-label">Product Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= $editProduct ? htmlspecialchars($editProduct['name']) : ''; ?>" placeholder="Product Name" required>
                </div>
                
                <div class="form-group">
                    <label for="category" class="form-label">Category</label>
                    <select class="form-control" id="category" name="category" required>
                        <option value="Chemises" <?= $editProduct && $editProduct['category'] === 'Chemises' ? 'selected' : ''; ?>>Chemises</option>
                        <option value="Pantalons" <?= $editProduct && $editProduct['category'] === 'Pantalons' ? 'selected' : ''; ?>>Pantalons</option>
                        <option value="Vestes" <?= $editProduct && $editProduct['category'] === 'Vestes' ? 'selected' : ''; ?>>Vestes</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="price" class="form-label">Price</label>
                    <input type="number" class="form-control" id="price" name="price" value="<?= $editProduct ? $editProduct['price'] : ''; ?>" placeholder="Price" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label for="stock" class="form-label">Stock</label>
                    <input type="number" class="form-control" id="stock" name="stock" value="<?= $editProduct ? $editProduct['stock'] : ''; ?>" placeholder="Stock" required>
                </div>
                
                <div class="form-group">
                    <label for="image" class="form-label">Image URL</label>
                    <input type="text" class="form-control" id="image" name="image" value="<?= $editProduct ? htmlspecialchars($editProduct['image']) : ''; ?>" placeholder="Image URL" required>
                </div>
                
                <button type="submit" class="btn"><?= $editProduct ? 'Update' : 'Add'; ?> Product</button>
                <?php if ($editProduct): ?>
                    <a href="index.php?controller=product&action=manage" class="btn btn-dark">Cancel Edit</a>
                <?php endif; ?>
            </form>
        </section>

        <section>
            <h2 class="section-header">Product List</h2>
            <div class="product-list">
                <?php foreach ($products as $product): ?>
                    <div class="product-item">
                        <img src="<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>" class="product-image">
                        <h4 class="mt-3"><?= htmlspecialchars($product['name']); ?></h4>
                        <div class="product-info">
                            <span class="product-category"><?= htmlspecialchars($product['category']); ?></span>
                            <div class="product-price">$<?= number_format($product['price'], 2); ?></div>
                            <div class="product-stock">
                                <i class="fas fa-box"></i> Stock: <?= $product['stock']; ?> 
                                <i class="fas fa-chart-line ml-2"></i> Sales: <?= $product['sales']; ?>
                            </div>
                        </div>
                        
                        <div class="product-actions">
                            <a href="index.php?controller=product&action=updateProduct&id=<?= $product['id']; ?>" class="btn btn-sm">Edit</a>
                            <a href="index.php?controller=product&action=deleteProduct&id=<?= $product['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                        </div>
                    </div>
                <?php endforeach; ?>
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