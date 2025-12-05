<?php
require_once '../../config.php';
checkLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management - NGO Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        .inventory-card {
            border-left: 4px solid #0d6efd;
            transition: all 0.3s;
        }
        .inventory-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .stock-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }
        .stock-in {
            background-color: #28a745;
        }
        .stock-low {
            background-color: #ffc107;
        }
        .stock-out {
            background-color: #dc3545;
        }
        .filter-badge {
            cursor: pointer;
            transition: all 0.2s;
        }
        .filter-badge:hover {
            transform: scale(1.05);
        }
        .progress-bar-custom {
            height: 8px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <?php include '../../includes/sidebar.php'; ?>
            </div>
            
            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="mb-1">Inventory Management</h2>
                        <p class="text-muted mb-0">Track and manage all donated goods in stock</p>
                    </div>
                    <div>
                        <a href="../donations/add.php" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Add New Donation
                        </a>
                    </div>
                </div>
                
                <!-- Inventory Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card border-0 bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-1">Total Items</h6>
                                        <h3 class="mb-0">
                                            <?php
                                            $stmt = $pdo->query("SELECT COUNT(DISTINCT item_name) as count FROM GoodsInventory");
                                            $result = $stmt->fetch();
                                            echo $result['count'];
                                            ?>
                                        </h3>
                                    </div>
                                    <i class="bi bi-box-seam" style="font-size: 2rem; opacity: 0.8;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card border-0 bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-1">In Stock</h6>
                                        <h3 class="mb-0">
                                            <?php
                                            $stmt = $pdo->query("SELECT COUNT(DISTINCT item_name) as count FROM GoodsInventory WHERE available_quantity > 0");
                                            $result = $stmt->fetch();
                                            echo $result['count'];
                                            ?>
                                        </h3>
                                    </div>
                                    <i class="bi bi-check-circle" style="font-size: 2rem; opacity: 0.8;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card border-0 bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-1">Low Stock</h6>
                                        <h3 class="mb-0">
                                            <?php
                                            $stmt = $pdo->query("
                                                SELECT COUNT(DISTINCT item_name) as count 
                                                FROM GoodsInventory 
                                                WHERE available_quantity > 0 
                                                AND available_quantity <= 10
                                            ");
                                            $result = $stmt->fetch();
                                            echo $result['count'];
                                            ?>
                                        </h3>
                                    </div>
                                    <i class="bi bi-exclamation-triangle" style="font-size: 2rem; opacity: 0.8;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card border-0 bg-danger text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-1">Out of Stock</h6>
                                        <h3 class="mb-0">
                                            <?php
                                            $stmt = $pdo->query("SELECT COUNT(DISTINCT item_name) as count FROM GoodsInventory WHERE available_quantity = 0");
                                            $result = $stmt->fetch();
                                            echo $result['count'];
                                            ?>
                                        </h3>
                                    </div>
                                    <i class="bi bi-x-circle" style="font-size: 2rem; opacity: 0.8;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Filter Badges -->
                <div class="mb-3">
                    <span class="badge bg-primary filter-badge me-2 mb-2" onclick="filterItems('all')">
                        <i class="bi bi-grid"></i> All Items
                    </span>
                    <span class="badge bg-success filter-badge me-2 mb-2" onclick="filterItems('in-stock')">
                        <i class="bi bi-check-circle"></i> In Stock
                    </span>
                    <span class="badge bg-warning filter-badge me-2 mb-2" onclick="filterItems('low-stock')">
                        <i class="bi bi-exclamation-triangle"></i> Low Stock (<10)
                    </span>
                    <span class="badge bg-danger filter-badge me-2 mb-2" onclick="filterItems('out-of-stock')">
                        <i class="bi bi-x-circle"></i> Out of Stock
                    </span>
                </div>
                
                <!-- Inventory Items Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">All Inventory Items</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="inventoryTable">
                                <thead>
                                    <tr>
                                        <th>Item Name</th>
                                        <th>Total Donated</th>
                                        <th>Available Now</th>
                                        <th>Stock Level</th>
                                        <th>Storage Location</th>
                                        <th>Status</th>
                                        <th>Last Donation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Get all distinct items with aggregated data
                                    $stmt = $pdo->query("
                                        SELECT 
                                            gi.item_name,
                                            -- Total donated from all donations of this item
                                            COALESCE(SUM(gi.available_quantity), 0) as total_donated,
                                            -- Currently available
                                            COALESCE(SUM(gi.available_quantity), 0) as available_now,
                                            -- Storage locations (comma separated)
                                            GROUP_CONCAT(DISTINCT gi.storage_location SEPARATOR ', ') as storage_locations,
                                            -- Last donation date
                                            MAX(d.donation_date) as last_donation_date,
                                            -- Calculate allocated (total - available)
                                            COALESCE(SUM(gi.available_quantity - gi.available_quantity), 0) as allocated,
                                            -- Get status from the first item (or calculate)
                                            MAX(gi.status) as item_status
                                        FROM GoodsInventory gi
                                        LEFT JOIN Donation d ON gi.donation_id = d.donation_id
                                        GROUP BY gi.item_name
                                        ORDER BY gi.item_name
                                    ");
                                    
                                    while ($row = $stmt->fetch()) {
                                        $item_name = $row['item_name'];
                                        $total_donated = $row['total_donated'];
                                        $available_now = $row['available_now'];
                                        $allocated = $row['allocated'];
                                        $storage_locations = $row['storage_locations'] ?: 'Not specified';
                                        $last_donation = $row['last_donation_date'] ?: 'Never';
                                        $item_status = $row['item_status'];
                                        
                                        // Determine stock level and status
                                        $stock_percentage = $total_donated > 0 ? ($available_now / $total_donated) * 100 : 0;
                                        
                                        if ($available_now <= 0) {
                                            $stock_class = 'stock-out';
                                            $stock_status = 'Out of Stock';
                                            $status_badge = 'danger';
                                            $progress_class = 'bg-danger';
                                        } elseif ($available_now <= 10) {
                                            $stock_class = 'stock-low';
                                            $stock_status = 'Low Stock';
                                            $status_badge = 'warning';
                                            $progress_class = 'bg-warning';
                                        } else {
                                            $stock_class = 'stock-in';
                                            $stock_status = 'In Stock';
                                            $status_badge = 'success';
                                            $progress_class = 'bg-success';
                                        }
                                        
                                        // Data attributes for filtering
                                        $filter_class = '';
                                        if ($available_now <= 0) $filter_class .= ' out-of-stock';
                                        if ($available_now > 0 && $available_now <= 10) $filter_class .= ' low-stock';
                                        if ($available_now > 10) $filter_class .= ' in-stock';
                                        
                                        echo '<tr class="inventory-item' . $filter_class . '">';
                                        echo '<td>';
                                        echo '<div class="d-flex align-items-center">';
                                        echo '<span class="stock-indicator ' . $stock_class . '"></span>';
                                        echo '<strong>' . htmlspecialchars($item_name) . '</strong>';
                                        echo '</div>';
                                        echo '</td>';
                                        
                                        echo '<td>';
                                        echo '<div class="d-flex align-items-center">';
                                        echo '<span class="badge bg-info rounded-pill me-2">' . $total_donated . '</span>';
                                        echo '<small class="text-muted">units</small>';
                                        echo '</div>';
                                        echo '</td>';
                                        
                                        echo '<td>';
                                        echo '<div class="d-flex align-items-center">';
                                        echo '<span class="badge bg-primary rounded-pill me-2">' . $available_now . '</span>';
                                        echo '<small class="text-muted">available</small>';
                                        if ($allocated > 0) {
                                            echo '<small class="text-muted ms-2">(' . $allocated . ' allocated)</small>';
                                        }
                                        echo '</div>';
                                        echo '</td>';
                                        
                                        echo '<td>';
                                        echo '<div class="d-flex align-items-center">';
                                        echo '<div class="progress progress-bar-custom flex-grow-1 me-2" style="width: 100px;">';
                                        echo '<div class="progress-bar ' . $progress_class . '" role="progressbar" ';
                                        echo 'style="width: ' . $stock_percentage . '%" ';
                                        echo 'aria-valuenow="' . $stock_percentage . '" aria-valuemin="0" aria-valuemax="100"></div>';
                                        echo '</div>';
                                        echo '<small class="text-muted">' . round($stock_percentage) . '%</small>';
                                        echo '</div>';
                                        echo '</td>';
                                        
                                        echo '<td>';
                                        echo '<span class="badge bg-light text-dark">' . htmlspecialchars($storage_locations) . '</span>';
                                        echo '</td>';
                                        
                                        echo '<td>';
                                        echo '<span class="badge bg-' . $status_badge . '">' . $stock_status . '</span>';
                                        echo '</td>';
                                        
                                        echo '<td>';
                                        echo '<small class="text-muted">' . $last_donation . '</small>';
                                        echo '</td>';
                                        
                                        
                                        echo '</tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <?php
                        // Check if no items
                        $count_stmt = $pdo->query("SELECT COUNT(*) as count FROM GoodsInventory");
                        $count = $count_stmt->fetch()['count'];
                        
                        if ($count == 0): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-inbox" style="font-size: 4rem; color: #6c757d;"></i>
                                <h4 class="mt-3">No Inventory Items</h4>
                                <p class="text-muted">No goods donations have been added yet.</p>
                                <a href="../donations/add.php" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Add First Donation
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Recent Donations Card -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Goods Donations</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Item</th>
                                        <th>Quantity</th>
                                        <th>Donor</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $pdo->query("
                                        SELECT d.donation_date, d.item_name, d.quantity, 
                                               dn.first_name, dn.last_name, d.description,
                                               gi.status
                                        FROM Donation d
                                        LEFT JOIN Donor dn ON d.donor_id = dn.donor_id
                                        LEFT JOIN GoodsInventory gi ON d.donation_id = gi.donation_id
                                        WHERE d.donation_type = 'Goods'
                                        ORDER BY d.donation_date DESC
                                        LIMIT 5
                                    ");
                                    
                                    while ($row = $stmt->fetch()) {
                                        echo '<tr>';
                                        echo '<td><small>' . $row['donation_date'] . '</small></td>';
                                        echo '<td><strong>' . htmlspecialchars($row['item_name']) . '</strong></td>';
                                        echo '<td><span class="badge bg-secondary">' . $row['quantity'] . '</span></td>';
                                        echo '<td>' . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . '</td>';
                                        echo '<td><small class="text-muted">' . htmlspecialchars(substr($row['description'] ?? '', 0, 30)) . '...</small></td>';
                                        echo '<td><span class="badge bg-' . ($row['status'] == 'In Stock' ? 'success' : 'warning') . '">' . $row['status'] . '</span></td>';
                                        echo '</tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- JavaScript for Filtering and Actions -->
    <script>
        function filterItems(filterType) {
            const items = document.querySelectorAll('.inventory-item');
            const badges = document.querySelectorAll('.filter-badge');
            
            // Remove active class from all badges
            badges.forEach(badge => {
                badge.classList.remove('active');
            });
            
            // Add active class to clicked badge
            event.target.classList.add('active');
            
            // Show/hide items based on filter
            items.forEach(item => {
                switch(filterType) {
                    case 'all':
                        item.style.display = '';
                        break;
                    case 'in-stock':
                        if (item.classList.contains('in-stock')) {
                            item.style.display = '';
                        } else {
                            item.style.display = 'none';
                        }
                        break;
                    case 'low-stock':
                        if (item.classList.contains('low-stock')) {
                            item.style.display = '';
                        } else {
                            item.style.display = 'none';
                        }
                        break;
                    case 'out-of-stock':
                        if (item.classList.contains('out-of-stock')) {
                            item.style.display = '';
                        } else {
                            item.style.display = 'none';
                        }
                        break;
                }
            });
        }
        
        function viewItemDetails(itemName) {
            // In a real implementation, this would open a modal or redirect to details page
            alert('Viewing details for: ' + itemName + '\n\nThis would show donation history, allocation records, and item details.');
            
            // Example of what could be done:
            // window.location.href = 'item_details.php?item=' + encodeURIComponent(itemName);
        }
        
        function allocateItem(itemName) {
            // In a real implementation, this would open an allocation form
            alert('Allocating item: ' + itemName + '\n\nThis would open a form to allocate this item to a beneficiary through a project.');
            
            // Example:
            // window.location.href = '../beneficiaries/allocate.php?item=' + encodeURIComponent(itemName);
        }
        
        // Initialize - show all items by default
        document.addEventListener('DOMContentLoaded', function() {
            // Set first filter badge as active
            document.querySelector('.filter-badge').classList.add('active');
            
            // Add row click handlers
            document.querySelectorAll('.inventory-item').forEach(row => {
                row.addEventListener('click', function(e) {
                    // Don't trigger if clicking on buttons
                    if (!e.target.closest('button')) {
                        const itemName = this.querySelector('strong').textContent;
                        viewItemDetails(itemName);
                    }
                });
            });
        });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>