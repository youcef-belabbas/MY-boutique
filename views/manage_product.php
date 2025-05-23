<?php
require_once 'models/ProductModel.php';
$productModel = new ProductModel();
$products = $productModel->getProducts();
$editProduct = isset($_GET['id']) ? $productModel->getProductById($_GET['id']) : null;
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
?>

<h2>Manage Products</h2>
<h3><?php echo $editProduct ? 'Edit Product' : 'Add Product'; ?></h3>
<form action="index.php?controller=product&action=<?php echo $editProduct ? 'updateProduct' : 'addProduct'; ?>" method="post">
    <?php if ($editProduct): ?>
        <input type="hidden" name="id" value="<?php echo $editProduct['id']; ?>">
    <?php endif; ?>
    <input type="text" name="name" value="<?php echo $editProduct ? $editProduct['name'] : ''; ?>" placeholder="Product Name" required>
    <select name="category">
        <option value="Chemises" <?php echo $editProduct && $editProduct['category'] === 'Chemises' ? 'selected' : ''; ?>>Chemises</option>
        <option value="Pantalons" <?php echo $editProduct && $editProduct['category'] === 'Pantalons' ? 'selected' : ''; ?>>Pantalons</option>
        <option value="Vestes" <?php echo $editProduct && $editProduct['category'] === 'Vestes' ? 'selected' : ''; ?>>Vestes</option>
    </select>
    <input type="number" name="price" value="<?php echo $editProduct ? $editProduct['price'] : ''; ?>" placeholder="Price" step="0.01" required>
    <input type="number" name="stock" value="<?php echo $editProduct ? $editProduct['stock'] : ''; ?>" placeholder="Stock" required>
    <input type="text" name="image" value="<?php echo $editProduct ? $editProduct['image'] : ''; ?>" placeholder="Image URL" required>
    <button type="submit"><?php echo $editProduct ? 'Update' : 'Add'; ?> Product</button>
</form>
<h3>Product List</h3>
<div class="products">
    <?php foreach ($products as $product): ?>
        <div class="product">
            <h4><?php echo $product['name']; ?> (<?php echo $product['category']; ?>)</h4>
            <p>Price: $<?php echo $product['price']; ?></p>
            <p>Stock: <?php echo $product['stock']; ?></p>
            <a href="index.php?controller=product&action=updateProduct&id=<?php echo $product['id']; ?>">Edit</a>
            <a href="index.php?controller=product&action=deleteProduct&id=<?php echo $product['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
        </div>
    <?php endforeach; ?>
</div>
<nav>
    <a href="index.php?controller=auth&action=dashboard">Home</a>
    <a href="index.php?controller=product&action=boutique">Boutique</a>
    <a href="index.php?controller=event&action=events">Events</a>
    <?php if ($user): ?>
        <a href="index.php?controller=auth&action=account">Account</a>
        <?php if ($user['role'] === 'it' || $user['role'] === 'admin'): ?>
            <a href="index.php?controller=product&action=manage">Manage Products</a>
            <a href="index.php?controller=event&action=manage">Manage Events</a>
        <?php endif; ?>
        <?php if ($user['role'] === 'admin'): ?>
            <a href="index.php?controller=admin&action=dashboard">Admin Dashboard</a>
        <?php endif; ?>
        <a href="index.php?controller=auth&action=logout">Logout</a>
    <?php else: ?>
        <a href="index.php?controller=auth&action=login">Login</a>
        <a href="index.php?controller=auth&action=register">Register</a>
    <?php endif; ?>
</nav>