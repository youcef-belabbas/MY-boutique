<?php
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
?>

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