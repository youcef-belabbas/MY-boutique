<?php
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MY Clothing Store - Home</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/script.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'views/partials/navbar.php'; ?>

    <header class="hero">
        <div class="hero-content">
            <?php if ($user): ?>
                <div class="alert alert-success text-center" style="font-size:1.3rem; font-weight:500; margin-bottom: 1rem;">
                    Welcome<?php echo isset($user['name']) ? ', ' . htmlspecialchars($user['name']) : ''; ?>!
                </div>
                <a href="index.php?controller=auth&action=logout" class="btn btn-danger mb-3">Logout</a>
            <?php endif; ?>
            <h1>Welcome to MY Clothing Store</h1>
            <p>Discover the latest trends in fashion and elevate your style.</p>
            <div class="hero-buttons">
                <a href="index.php?controller=product&action=boutique" class="btn">Shop Now</a>
                <a href="index.php?controller=event&action=events" class="btn btn-dark">Explore Events</a>
            </div>
        </div>
    </header>

    <main>
        <section class="welcome-section">
            <div class="featured-content">
                <div class="feature-card">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR1IkzYmVTaQBHXRzAyqC0I4XJ_crVIYb00zw&s" alt="New Collection">
                    <h3>New Arrivals</h3>
                    <p>Check out our latest clothing line for 2025.</p>
                    <a href="index.php?controller=product&action=boutique" class="btn">View Collection</a>
                </div>
                <div class="feature-card">
                    <img src="https://assets.vogue.com/photos/5f74f7d208fdcc6598c7bb75/master/w_2560%2Cc_limit/_CIK0894.jpg" alt="Fashion Events">
                    <h3>Fashion Events</h3>
                    <p>Join us at our exclusive fashion shows and events.</p>
                    <a href="index.php?controller=event&action=events" class="btn">See Events</a>
                </div>
            </div>
        </section>
    </main>

    <?php include 'views/partials/footer.php'; ?>
</body>
</html>