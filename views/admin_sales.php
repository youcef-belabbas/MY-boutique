<?php
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
if (!$user || $user['role'] !== 'admin') {
    header('Location: index.php?controller=auth&action=login');
    exit;
}

// Calculate total revenue and sales
$totalRevenue = 0;
$totalSales = 0;
$categorySales = [];

foreach ($productStats as $product) {
    $totalRevenue += $product['revenue'];
    $totalSales += $product['sales'];
    
    if (!isset($categorySales[$product['category']])) {
        $categorySales[$product['category']] = [
            'sales' => 0,
            'revenue' => 0
        ];
    }
    
    $categorySales[$product['category']]['sales'] += $product['sales'];
    $categorySales[$product['category']]['revenue'] += $product['revenue'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Management - MY Boutique</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #007bff;
            --secondary: #6c757d;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
            --light: #f8f9fa;
            --dark: #343a40;
        }
        
        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .dashboard-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .stats-card {
            text-align: center;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 10px;
            color: white;
            transition: transform 0.3s;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .stats-card.revenue {
            background: linear-gradient(45deg, #28a745, #20c997);
        }
        
        .stats-card.sales {
            background: linear-gradient(45deg, #007bff, #17a2b8);
        }
        
        .stats-card.products {
            background: linear-gradient(45deg, #fd7e14, #ffc107);
        }
        
        .stats-card h2 {
            font-size: 1.8rem;
            margin-bottom: 5px;
            font-weight: 700;
        }
        
        .stats-card p {
            font-size: 1.1rem;
            margin-bottom: 0;
        }
        
        .stats-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .sales-table {
            margin-top: 20px;
        }
        
        .table th {
            border-top: none;
            text-transform: uppercase;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .chart-container {
            height: 300px;
        }
        
        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }
        
        .top-seller {
            background-color: rgba(40, 167, 69, 0.1);
        }
        
        .low-stock {
            background-color: rgba(255, 193, 7, 0.1);
        }
        
        .badge-category {
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.8rem;
            background-color: #e9ecef;
        }
        
        .mini-card {
            padding: 10px 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-chart-line me-2"></i>Sales Management Dashboard</h1>
            <div>
                <a href="index.php?controller=admin&action=dashboard" class="btn btn-secondary me-2">
                    <i class="fas fa-tachometer-alt me-1"></i> Main Dashboard
                </a>
                <a href="index.php" class="btn btn-primary">
                    <i class="fas fa-home me-1"></i> Home
                </a>
            </div>
        </div>
        
        <!-- Display messages -->
        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> <?php echo htmlspecialchars($_GET['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <!-- Display errors -->
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> <?php echo htmlspecialchars($_GET['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <!-- Stats Overview -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stats-card revenue">
                    <div class="stats-icon"><i class="fas fa-dollar-sign"></i></div>
                    <h2>$<?php echo number_format($totalRevenue, 2); ?></h2>
                    <p>Total Revenue</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card sales">
                    <div class="stats-icon"><i class="fas fa-shopping-cart"></i></div>
                    <h2><?php echo $totalSales; ?></h2>
                    <p>Total Products Sold</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card products">
                    <div class="stats-icon"><i class="fas fa-box"></i></div>
                    <h2><?php echo count($productStats); ?></h2>
                    <p>Product Variants</p>
                </div>
            </div>
        </div>
        
        <!-- Category Performance -->
        <div class="dashboard-card mb-4">
            <h3><i class="fas fa-tags me-2"></i>Category Performance</h3>
            <div class="row mt-4">
                <?php foreach ($categorySales as $category => $stats): ?>
                <div class="col-md-4 mb-3">
                    <div class="mini-card">
                        <h5><?php echo htmlspecialchars($category); ?></h5>
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="mb-1">Sales: <strong><?php echo $stats['sales']; ?> units</strong></p>
                            </div>
                            <div>
                                <p class="mb-1">Revenue: <strong>$<?php echo number_format($stats['revenue'], 2); ?></strong></p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Product Performance -->
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3><i class="fas fa-box-open me-2"></i>Product Performance</h3>
                <a href="index.php?controller=product&action=manage" class="btn btn-primary">
                    <i class="fas fa-cog me-1"></i> Manage Products
                </a>
            </div>
            
            <div class="table-responsive sales-table">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Sales</th>
                            <th>Revenue</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productStats as $index => $product): ?>
                        <tr class="<?php echo $index < 3 ? 'top-seller' : ($product['stock'] < 10 ? 'low-stock' : ''); ?>">
                            <td>
                                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                            </td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><span class="badge-category"><?php echo htmlspecialchars($product['category']); ?></span></td>
                            <td>$<?php echo number_format($product['price'], 2); ?></td>
                            <td><?php echo $product['stock']; ?></td>
                            <td><?php echo $product['sales']; ?></td>
                            <td>$<?php echo number_format($product['revenue'], 2); ?></td>
                            <td>
                                <?php if ($index < 3): ?>
                                <span class="badge bg-success">Top Seller</span>
                                <?php elseif ($product['stock'] < 10): ?>
                                <span class="badge bg-warning text-dark">Low Stock</span>
                                <?php else: ?>
                                <span class="badge bg-secondary">Normal</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Recent Orders -->
        <div class="dashboard-card mt-4">
            <h3><i class="fas fa-shopping-basket me-2"></i>Recent Orders</h3>
            <?php if (empty($purchases)): ?>
                <p>No purchases have been made yet.</p>
            <?php else: ?>
                <div class="table-responsive sales-table">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($purchases, 0, 10) as $purchase): ?>
                            <tr>
                                <td><?php echo substr($purchase['id'], 0, 8); ?>...</td>
                                <td><?php echo htmlspecialchars($purchase['user_name']); ?></td>
                                <td><?php echo date('M d, Y H:i', strtotime($purchase['purchase_date'])); ?></td>
                                <td>
                                    <?php 
                                    foreach ($purchase['items'] as $index => $item): 
                                        echo htmlspecialchars($item['name']) . ' (x' . $item['quantity'] . ')';
                                        if ($index < count($purchase['items']) - 1) echo ', ';
                                    endforeach; 
                                    ?>
                                </td>
                                <td>$<?php echo number_format($purchase['total'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 