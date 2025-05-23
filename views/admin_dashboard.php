<?php
require_once 'models/ProductModel.php';
require_once 'models/PurchaseModel.php';
require_once 'models/UserModel.php';
require_once 'models/EventModel.php';
$productModel = new ProductModel();
$purchaseModel = new PurchaseModel();
$userModel = new UserModel();
$eventModel = new EventModel();
$topSales = $productModel->getTopSalesByCategory();
$purchases = $productModel->getAllPurchases();
$users = $userModel->getAllUsers();
$pendingEvents = $eventModel->getEvents('pending');
$pendingProducts = $productModel->getPendingProductRequests();

// Calculate totals for statistics
$totalRevenue = 0;
$totalSales = 0;
$totalUsers = count($users);
$categorySales = [];

foreach ($purchases as $purchase) {
    foreach ($purchase['items'] as $item) {
        $totalRevenue += $item['price'] * $item['quantity'];
        $totalSales += $item['quantity'];
        
        if (!isset($categorySales[$item['category']])) {
            $categorySales[$item['category']] = 0;
        }
        $categorySales[$item['category']] += $item['quantity'];
    }
}

$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - MY Boutique</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background-color: var(--white);
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            border-left: 4px solid var(--yellow);
        }
        
        .stat-card h3 {
            font-size: 1rem;
            margin: 0 0 0.5rem 0;
            color: var(--dark-gray);
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--black);
        }
        
        .stat-icon {
            font-size: 2.5rem;
            color: var(--yellow);
            margin-bottom: 1rem;
        }
        
        .section-title {
            border-bottom: 2px solid var(--yellow);
            padding-bottom: 0.75rem;
            margin-bottom: 1.5rem;
        }
        
        .dashboard-card {
            background-color: var(--white);
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            overflow: hidden;
        }
        
        .dashboard-card-header {
            background-color: var(--black);
            color: var(--yellow);
            padding: 1rem 1.5rem;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .dashboard-card-content {
            padding: 1.5rem;
        }
        
        .pending-item {
            background-color: var(--light-gray);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid var(--yellow);
        }
        
        .pending-item:last-child {
            margin-bottom: 0;
        }
        
        .pending-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        
        .pending-item-title {
            font-weight: 600;
            color: var(--black);
        }
        
        .pending-item-meta {
            color: var(--dark-gray);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .pending-item-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-approve {
            background-color: #4CAF50;
            color: white;
        }
        
        .btn-reject {
            background-color: #f44336;
            color: white;
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        .chart-container {
            height: 300px;
            margin-bottom: 1.5rem;
        }
        
        .tab-buttons {
            display: flex;
            border-bottom: 1px solid var(--gray);
            margin-bottom: 1.5rem;
        }
        
        .tab-button {
            padding: 0.75rem 1.25rem;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            font-weight: 600;
            color: var(--dark-gray);
        }
        
        .tab-button.active {
            border-bottom-color: var(--yellow);
            color: var(--black);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
    </style>
</head>
<body>
    <?php include 'views/partials/navbar.php'; ?>

    <header class="hero">
        <div class="hero-content">
            <h1>Admin Dashboard</h1>
            <p>Manage your store, track sales, and approve requests</p>
        </div>
    </header>

    <main class="dashboard-container">
        <!-- Stats Section -->
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-coins stat-icon"></i>
                <h3>Total Revenue</h3>
                <div class="stat-value">$<?php echo number_format($totalRevenue, 2); ?></div>
            </div>
            <div class="stat-card">
                <i class="fas fa-shopping-bag stat-icon"></i>
                <h3>Total Sales</h3>
                <div class="stat-value"><?php echo $totalSales; ?> items</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-users stat-icon"></i>
                <h3>Total Users</h3>
                <div class="stat-value"><?php echo $totalUsers; ?></div>
            </div>
            <div class="stat-card">
                <i class="fas fa-chart-line stat-icon"></i>
                <h3>Top Category</h3>
                <div class="stat-value">
                    <?php 
                        if (!empty($categorySales)) {
                            $topCategory = array_search(max($categorySales), $categorySales);
                            echo htmlspecialchars($topCategory);
                        } else {
                            echo "N/A";
                        }
                    ?>
                </div>
            </div>
        </div>
        
        <!-- Tab Navigation -->
        <div class="tab-buttons">
            <div class="tab-button active" data-tab="sales">Sales Analytics</div>
            <div class="tab-button" data-tab="approvals">Pending Approvals</div>
            <div class="tab-button" data-tab="users">User Management</div>
        </div>
        
        <!-- Sales Analytics Tab -->
        <div id="sales-tab" class="tab-content active">
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h2>Sales by Category</h2>
                    <a href="index.php?controller=admin&action=viewSales" class="btn btn-sm">View Detailed Report</a>
                </div>
                <div class="dashboard-card-content">
                    <div class="chart-container">
                        <canvas id="categorySalesChart"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h2>Recent Purchases</h2>
                </div>
                <div class="dashboard-card-content">
                    <?php if (empty($purchases)): ?>
                        <p>No purchases have been made yet.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Customer</th>
                                        <th>Date</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($purchases, 0, 5) as $purchase): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars(substr($purchase['id'], 0, 8)); ?></td>
                                            <td><?php echo htmlspecialchars($purchase['user_name']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($purchase['purchase_date'])); ?></td>
                                            <td><?php echo count($purchase['items']); ?> items</td>
                                            <td>$<?php echo number_format($purchase['total'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Pending Approvals Tab -->
        <div id="approvals-tab" class="tab-content">
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h2>Pending Product Requests</h2>
                </div>
                <div class="dashboard-card-content">
                    <?php if (empty($pendingProducts)): ?>
                        <p>No pending product requests.</p>
                    <?php else: ?>
                        <?php foreach ($pendingProducts as $product): ?>
                            <div class="pending-item">
                                <div class="pending-item-header">
                                    <div class="pending-item-title"><?php echo htmlspecialchars($product['name']); ?></div>
                                    <div class="pending-item-meta">
                                        Requested by: <?php echo htmlspecialchars($product['requester_name']); ?>
                                    </div>
                                </div>
                                <div class="pending-item-meta">
                                    Category: <?php echo htmlspecialchars($product['category']); ?> | 
                                    Price: $<?php echo number_format($product['price'], 2); ?> | 
                                    Stock: <?php echo $product['stock']; ?>
                                </div>
                                <div class="pending-item-actions">
                                    <a href="index.php?controller=admin&action=approveProductRequest&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-approve">Approve</a>
                                    <a href="index.php?controller=admin&action=rejectProductRequest&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-reject">Reject</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h2>Pending Event Approvals</h2>
                </div>
                <div class="dashboard-card-content">
                    <?php if (empty($pendingEvents)): ?>
                        <p>No pending events to approve.</p>
                    <?php else: ?>
                        <?php foreach ($pendingEvents as $event): ?>
                            <div class="pending-item">
                                <div class="pending-item-header">
                                    <div class="pending-item-title"><?php echo htmlspecialchars($event['title']); ?></div>
                                </div>
                                <div class="pending-item-meta">
                                    Date: <?php echo isset($event['date']) ? date('M d, Y', strtotime($event['date'])) : 'N/A'; ?> | 
                                    Location: <?php echo isset($event['location']) ? htmlspecialchars($event['location']) : 'N/A'; ?>
                                </div>
                                <div class="pending-item-actions">
                                    <a href="index.php?controller=event&action=approveEvent&id=<?php echo $event['id']; ?>" class="btn btn-sm btn-approve">Approve</a>
                                    <a href="index.php?controller=event&action=rejectEvent&id=<?php echo $event['id']; ?>" class="btn btn-sm btn-reject">Reject</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- User Management Tab -->
        <div id="users-tab" class="tab-content">
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h2>User Management</h2>
                </div>
                <div class="dashboard-card-content">
                    <?php if (empty($users)): ?>
                        <p>No users found.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $userItem): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($userItem['name']); ?></td>
                                            <td><?php echo htmlspecialchars($userItem['email']); ?></td>
                                            <td><?php echo htmlspecialchars($userItem['role']); ?></td>
                                            <td>
                                                <a href="index.php?controller=admin&action=editUser&id=<?php echo $userItem['id']; ?>" class="btn btn-sm">Edit</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <?php include 'views/partials/footer.php'; ?>

    <script>
        // Initialize charts
        document.addEventListener('DOMContentLoaded', function() {
            // Category Sales Chart
            const salesCtx = document.getElementById('categorySalesChart').getContext('2d');
            const categorySalesChart = new Chart(salesCtx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode(array_keys($categorySales)); ?>,
                    datasets: [{
                        label: 'Sales by Category',
                        data: <?php echo json_encode(array_values($categorySales)); ?>,
                        backgroundColor: [
                            'rgba(255, 215, 0, 0.7)',
                            'rgba(255, 159, 64, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(153, 102, 255, 0.7)'
                        ],
                        borderColor: [
                            'rgba(255, 215, 0, 1)',
                            'rgba(255, 159, 64, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(153, 102, 255, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
            
            // Tab navigation
            document.querySelectorAll('.tab-button').forEach(button => {
                button.addEventListener('click', () => {
                    // Remove active class from all buttons and contents
                    document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
                    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                    
                    // Add active class to clicked button
                    button.classList.add('active');
                    
                    // Show corresponding content
                    const tabId = button.getAttribute('data-tab');
                    document.getElementById(tabId + '-tab').classList.add('active');
                });
            });
            
            // Check for URL hash and activate corresponding tab
            const hash = window.location.hash;
            if (hash) {
                const tabId = hash.substring(1);
                const tabButton = document.querySelector(`.tab-button[data-tab="${tabId}"]`);
                if (tabButton) {
                    tabButton.click();
                }
            }
        });
    </script>
</body>
</html>