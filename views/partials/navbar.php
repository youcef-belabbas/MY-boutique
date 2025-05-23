<?php
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
?>

<nav class="navbar fixed-navbar">
    <div class="navbar-container">
        <a href="index.php" class="logo">MY Clothing</a>
        <div class="nav-toggle" onclick="toggleMenu()">☰</div>
        <ul class="nav-menu">
            <li><a href="index.php">Home</a></li>
            <li><a href="index.php?controller=product&action=boutique">Boutique</a></li>
            <li><a href="index.php?controller=event&action=events">Events</a></li>
            <?php if ($user): ?>
                <li><a href="index.php?controller=auth&action=account">Account</a></li>
                <?php if ($user['role'] === 'it' || $user['role'] === 'admin'): ?>
                    <li><a href="index.php?controller=product&action=manage">Manage Products</a></li>
                    <li><a href="index.php?controller=event&action=manage">Manage Events</a></li>
                <?php endif; ?>
                <?php if ($user['role'] === 'admin'): ?>
                    <li><a href="index.php?controller=admin&action=dashboard">Admin Dashboard</a></li>
                <?php endif; ?>
                <li><a href="index.php?controller=product&action=cart"><i class="fas fa-shopping-cart"></i> Cart
                    <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                        <span class="cart-count"><?php echo count($_SESSION['cart']); ?></span>
                    <?php endif; ?>
                </a></li>
                <li><a href="index.php?controller=auth&action=logout">Logout</a></li>
            <?php else: ?>
                <li><a href="index.php?controller=auth&action=login">Login</a></li>
                <li><a href="index.php?controller=auth&action=register">Register</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<div class="navbar-spacer"></div>

<script src="js/cache_buster.js?v=<?php echo time(); ?>"></script>

<style>
.fixed-navbar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;
}

.navbar-spacer {
    height: 65px; /* Match the height of your navbar */
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
    .nav-menu.active {
        position: fixed;
        top: 65px; /* Match the height of your navbar */
    }
    
    .navbar-spacer {
        height: 60px; /* Adjust for mobile */
    }
}
</style> 