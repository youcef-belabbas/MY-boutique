<?php
require_once 'models/PurchaseModel.php';
require_once 'models/ProductModel.php';
$purchaseModel = new PurchaseModel();
$productModel = new ProductModel();
$purchases = $purchaseModel->getPurchasesByUser($_SESSION['user']['id']);
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MY Boutique - My Account</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .account-section {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        
        .card-header {
            background-color: var(--black);
            color: var(--yellow);
            padding: 1rem 1.5rem;
            font-weight: 600;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
        }
        
        .card-header i {
            margin-right: 0.5rem;
        }
        
        /* Purchase History */
        .purchase-card {
            border: 1px solid var(--gray);
            border-radius: 8px;
            margin-bottom: 1.5rem;
            overflow: hidden;
            background-color: var(--white);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .purchase-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-color: var(--yellow);
        }
        
        .purchase-header {
            background-color: var(--light-gray);
            padding: 1rem;
            border-bottom: 1px solid var(--gray);
            font-weight: 500;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .purchase-date {
            color: var(--black);
            font-weight: 600;
        }
        
        .purchase-items {
            padding: 1rem;
        }
        
        .purchase-item {
            display: flex;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--gray);
        }
        
        .purchase-item:last-child {
            border-bottom: none;
        }
        
        .purchase-item img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
            margin-right: 1rem;
        }
        
        .purchase-item-info {
            flex-grow: 1;
        }
        
        .purchase-item-title {
            font-weight: 600;
            color: var(--black);
        }
        
        .purchase-item-meta {
            display: flex;
            justify-content: space-between;
            color: var(--dark-gray);
            font-size: 0.9rem;
            margin-top: 0.25rem;
        }
        
        .purchase-total {
            text-align: right;
            padding: 1rem;
            font-weight: 600;
            border-top: 1px solid var(--gray);
        }
        
        .profile-pic {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 1rem;
            border: 4px solid var(--yellow);
        }
        
        .tabs {
            display: flex;
            margin-bottom: 2rem;
            border-bottom: 1px solid var(--gray);
            padding-bottom: 0;
        }
        
        .tab {
            padding: 1rem 1.5rem;
            cursor: pointer;
            font-weight: 600;
            color: var(--dark-gray);
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }
        
        .tab.active {
            color: var(--black);
            border-bottom: 3px solid var(--yellow);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        @media (max-width: 768px) {
            .tabs {
                flex-direction: column;
            }
            
            .tab {
                border-bottom: none;
                border-left: 3px solid transparent;
                padding: 0.75rem 1rem;
            }
            
            .tab.active {
                border-bottom: none;
                border-left: 3px solid var(--yellow);
            }
        }
        
        .alert {
            margin-top: 1rem;
            padding: 0.75rem 1rem;
            border-radius: 4px;
            font-weight: 500;
        }
        
        .alert-success {
            background-color: rgba(40, 167, 69, 0.1);
            border-left: 4px solid #28a745;
            color: #155724;
        }
        
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            border-left: 4px solid #dc3545;
            color: #721c24;
        }
        
        .alert i {
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>
    <?php include 'views/partials/navbar.php'; ?>

    <header class="hero">
        <div class="hero-content">
            <h1>My Account</h1>
            <p>Manage your profile, view your purchase history, and update your details.</p>
            
            <?php if (isset($_GET['message'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['message']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <main>
        <div class="container">
            <section class="account-section">
                <div class="tabs">
                    <div class="tab active" data-tab="profile">Profile</div>
                    <div class="tab" data-tab="purchase-history">Purchase History</div>
                    <div class="tab" data-tab="settings">Settings</div>
                </div>
                
                <div id="profile-tab" class="tab-content active">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-user"></i> My Profile
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <img src="https://ui-avatars.com/api/?name=<?= urlencode($user['name']) ?>&background=FFD700&color=000000&size=120" alt="Profile Picture" class="profile-pic">
                                <h3><?= htmlspecialchars($user['name']) ?></h3>
                                <p><?= htmlspecialchars($user['email']) ?></p>
                                <p class="mb-3">
                                    <span class="badge bg-dark"><?= ucfirst(htmlspecialchars($user['role'])) ?></span>
                                </p>
                            </div>
                            
                            <form method="post" action="index.php?controller=auth&action=update_profile">
                                <div class="form-group">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>">
                                </div>
                                <div class="form-group">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                                </div>
                                <button type="submit" class="btn">Update Profile</button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div id="purchase-history-tab" class="tab-content">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-shopping-bag"></i> Purchase History
                        </div>
                        <div class="card-body">
                            <?php if (empty($purchases)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-shopping-cart mb-3" style="font-size: 3rem; color: var(--gray);"></i>
                                    <h3>No Purchases Yet</h3>
                                    <p class="mb-3">You haven't made any purchases yet.</p>
                                    <a href="index.php?controller=product&action=boutique" class="btn">Shop Now</a>
                                </div>
                            <?php else: ?>
                                <?php foreach ($purchases as $purchase): ?>
                                    <div class="purchase-card">
                                        <div class="purchase-header">
                                            <span class="purchase-date">
                                                <i class="far fa-calendar-alt"></i> 
                                                <?= date('F j, Y', strtotime($purchase['purchase_date'])) ?>
                                            </span>
                                            <span class="purchase-id">
                                                Order #<?= $purchase['id'] ?>
                                            </span>
                                        </div>
                                        <div class="purchase-items">
                                            <?php 
                                            $total = 0;
                                            $items = json_decode($purchase['items'], true);
                                            foreach ($items as $item): 
                                                $total += $item['price'] * $item['quantity'];
                                                // Get product details if available
                                                $product = $productModel->getProductById($item['product_id']);
                                            ?>
                                                <div class="purchase-item">
                                                    <img src="<?= !empty($product['image']) ? htmlspecialchars($product['image']) : 'https://via.placeholder.com/50x50' ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                                                    <div class="purchase-item-info">
                                                        <div class="purchase-item-title">
                                                            <?= htmlspecialchars($item['name']) ?>
                                                        </div>
                                                        <div class="purchase-item-meta">
                                                            <span>Qty: <?= $item['quantity'] ?></span>
                                                            <span>$<?= number_format($item['price'], 2) ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="purchase-total">
                                            Total: $<?= number_format($total, 2) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div id="settings-tab" class="tab-content">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-cog"></i> Account Settings
                        </div>
                        <div class="card-body">
                            <form method="post" action="index.php?controller=auth&action=change_password">
                                <div class="form-group">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password" id="current_password" name="current_password" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" id="new_password" name="new_password" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                                </div>
                                <button type="submit" class="btn">Change Password</button>
                            </form>
                            
                            <hr class="my-4">
                            
                            <div class="text-center">
                                <h3 class="mb-3">Delete Account</h3>
                                <p class="mb-3">This action cannot be undone. All your data will be permanently deleted.</p>
                                <button type="button" class="btn btn-danger" onclick="confirmDelete()">Delete My Account</button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <?php include 'views/partials/footer.php'; ?>

    <script>
        function toggleMenu() {
            document.querySelector('.nav-menu').classList.toggle('active');
        }
        
        function confirmDelete() {
            if (confirm("Are you sure you want to delete your account? This cannot be undone.")) {
                window.location.href = "index.php?controller=auth&action=delete_account";
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Tab navigation
            const tabs = document.querySelectorAll('.tab');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    // Remove active class from all tabs and contents
                    tabs.forEach(t => t.classList.remove('active'));
                    tabContents.forEach(c => c.classList.remove('active'));
                    
                    // Add active class to clicked tab
                    tab.classList.add('active');
                    
                    // Show corresponding content
                    const tabId = tab.getAttribute('data-tab');
                    document.getElementById(tabId + '-tab').classList.add('active');
                });
            });
            
            // Check for URL hash and activate corresponding tab
            const hash = window.location.hash;
            if (hash) {
                const tabId = hash.substring(1);
                const tabButton = document.querySelector(`.tab[data-tab="${tabId}"]`);
                if (tabButton) {
                    tabButton.click();
                }
            }
        });
    </script>
</body>
</html>