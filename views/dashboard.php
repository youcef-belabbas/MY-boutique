<?php
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
if (!$user) {
    header('Location: index.php?controller=auth&action=login');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MY Boutique</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body { background: linear-gradient(135deg, #f8fafc 0%, #e0e7ff 100%); }
        .user-profile {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 10px;
            background-color: rgba(255, 255, 255, 0.7);
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .user-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: #6c757d;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <?php include 'views/partials/navbar.php'; ?>

    <header class="hero">
        <div class="hero-content">
            <div class="user-profile">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                </div>
                <div>
                    <h3>Welcome, <?php echo htmlspecialchars($user['name'] . ' ' . ($user['surname'] ?? '')); ?>!</h3>
                    <p>Role: <?php echo ucfirst(htmlspecialchars($user['role'])); ?></p>
                </div>
                <a href="index.php?controller=auth&action=account" class="btn btn-primary ms-auto">My Profile</a>
                <a href="index.php?controller=auth&action=logout" class="btn btn-danger">Logout</a>
            </div>
            
            <h1>Your Personal Dashboard</h1>
            <p>Discover the latest trends and manage your shopping experience.</p>
            <div class="hero-buttons">
                <a href="index.php?controller=product&action=boutique" class="btn btn-primary">Shop Now</a>
                <a href="index.php?controller=event&action=events" class="btn btn-secondary">Explore Events</a>
            </div>
        </div>
    </header>
    <main>
        <section class="welcome-section">
            <h2>Your Shopping Dashboard</h2>
            <p>Welcome to your personalized dashboard! Here you can view your recent activity, manage your profile, and explore our latest collections.</p>
            <div class="featured-content">
                <div class="feature-card">
                    <img src="https://img.freepik.com/free-vector/new-collection-calligraphic-lettering-with-flowers-leaves_1262-13802.jpg?semt=ais_hybrid&w=740" alt="New Collection">
                    <h3>New Arrivals</h3>
                    <p>Check out our latest clothing line for 2025.</p>
                    <a href="index.php?controller=product&action=boutique" class="btn btn-secondary">View Collection</a>
                </div>
                <div class="feature-card">
                    <img src="https://assets.vogue.com/photos/5f74f7d208fdcc6598c7bb75/master/w_2560%2Cc_limit/_CIK0894.jpg" alt="Fashion Events">
                    <h3>Fashion Events</h3>
                    <p>Join us at our exclusive fashion shows and events.</p>
                    <a href="index.php?controller=event&action=events" class="btn btn-secondary">See Events</a>
                </div>
            </div>
        </section>
    </main>
    <footer>
        <p>&copy; 2025 MY Boutique. All rights reserved.</p>
    </footer>
</body>
</html> 