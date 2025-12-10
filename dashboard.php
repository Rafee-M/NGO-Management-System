<?php
require_once 'config.php';
checkLogin();

$user_level = $_SESSION['user_level'];
$user_name = $_SESSION['full_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - NGO Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --light-color: #f8f9fa;
            --dark-color: #212529;
        }
        .sidebar {
            background: var(--primary-color);
            color: white;
            height: 100vh;
            position: fixed;
            width: 250px;
            padding: 20px 0;
        }
        .sidebar .logo {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 5px 10px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
            background: #f5f7fb;
            min-height: 100vh;
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        .badge-role {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
        }
        .badge-ceo {
            background: #ff6b6b;
            color: white;
        }
        .badge-manager {
            background: #4ecdc4;
            color: white;
        }
        .badge-employee {
            background: #45b7d1;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <h4 class="mb-0"><i class="bi bi-heart-fill me-2"></i> NGO System</h4>
            <small class="text-white-50">Management Dashboard</small>
        </div>
        
        <!-- User Info -->
        <div class="px-3 mb-4">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h6 class="mb-0"><?php echo htmlspecialchars($user_name); ?></h6>
                    <span class="badge badge-role badge-<?php echo strtolower($user_level); ?>">
                        <?php echo $user_level; ?>
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Navigation -->
        <nav class="nav flex-column">
            <a href="dashboard.php" class="nav-link active">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
            
            <div class="px-3 text-white-50 mt-3 mb-2">DATA MANAGEMENT</div>
            
            <a href="modules/donors/view.php" class="nav-link">
                <i class="bi bi-cash-coin me-2"></i> Donors
            </a>

            <a href="modules/donations/view.php" class="nav-link">
                <i class="bi bi-cash-coin me-2"></i> Donations
            </a>
            
            <a href="modules/beneficiaries/view.php" class="nav-link">
                <i class="bi bi-people-fill me-2"></i> Beneficiaries
            </a>
            
            <a href="modules/projects/view.php" class="nav-link">
                <i class="bi bi-clipboard-data me-2"></i> Projects
            </a>
            
            <a href="modules/volunteers/view.php" class="nav-link">
                <i class="bi bi-person-badge me-2"></i> Volunteers
            </a>
            
            <a href="modules/inventory/view.php" class="nav-link">
                <i class="bi bi-box-seam me-2"></i> Inventory
            </a>
            
            <?php if (checkPermission('Manager')): ?>
            <div class="px-3 text-white-50 mt-3 mb-2">MANAGEMENT</div>
            
            <a href="modules/projects/manage.php" class="nav-link">
                <i class="bi bi-plus-circle me-2"></i> New Project
            </a>
            
            <a href="modules/volunteers/manage.php" class="nav-link">
                <i class="bi bi-person-plus me-2"></i> Assign Volunteers
            </a>
            <?php endif; ?>
            
            <?php if (checkPermission('CEO')): ?>
            <div class="px-3 text-white-50 mt-3 mb-2">ADMINISTRATION</div>
            
            <a href="modules/staff/view.php" class="nav-link">
                <i class="bi bi-person-lines-fill me-2"></i> Staff
            </a>
            
            <a href="modules/reports/financial.php" class="nav-link">
                <i class="bi bi-graph-up me-2"></i> Reports
            </a>
            <?php endif; ?>
            
            <div class="mt-auto px-3">
                <a href="logout.php" class="btn btn-outline-light w-100">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </a>
            </div>
        </nav>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Welcome back, <?php echo explode(' ', $user_name)[0]; ?>!</h4>
            <span class="text-muted"><?php echo date('F j, Y'); ?></span>
        </div>
        
<!-- Statistics Cards - Updated for 5 items in one row -->
<div class="row mb-4">
    <div class="col">
        <div class="stat-card h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Total Donations</h6>
                    <h3 class="mb-0">
                        <?php
                        $stmt = $pdo->query("SELECT COUNT(*) as count FROM Donation");
                        $result = $stmt->fetch();
                        echo $result['count'];
                        ?>
                    </h3>
                </div>
                <div class="stat-icon" style="background: #e3f2fd;">
                    <i class="bi bi-cash-coin text-primary"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col">
        <div class="stat-card h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Available Cash</h6>
                    <h3 class="mb-0">
                        <?php
                        $stmt = $pdo->query("SELECT COALESCE(SUM(amount), 0) as total_cash FROM CashInventory");
                        $total_cash = $stmt->fetch()['total_cash'];

                        $stmt = $pdo->query("
                            SELECT COALESCE(SUM(allocated_budget), 0) as total_allocated 
                            FROM Project 
                            WHERE status IN ('Active', 'Completed', 'Planning', 'Cancelled')
                        ");
                        $total_allocated = $stmt->fetch()['total_allocated'];
                    
                        // Calculate available cash
                        $available_cash = $total_cash - $total_allocated;

                        echo '$' . number_format($available_cash);
                        ?>
                    </h3>
                </div>
                <div class="stat-icon" style="background: #fff3cd;">
                    <i class="bi bi-wallet2 text-warning"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col">
        <div class="stat-card h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Active Projects</h6>
                    <h3 class="mb-0">
                        <?php
                        $stmt = $pdo->query("SELECT COUNT(*) as count FROM Project WHERE status = 'Active'");
                        $result = $stmt->fetch();
                        echo $result['count'];
                        ?>
                    </h3>
                </div>
                <div class="stat-icon" style="background: #e8f5e9;">
                    <i class="bi bi-clipboard-check text-success"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col">
        <div class="stat-card h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Beneficiaries</h6>
                    <h3 class="mb-0">
                        <?php
                        $stmt = $pdo->query("SELECT COUNT(*) as count FROM Beneficiary");
                        $result = $stmt->fetch();
                        echo $result['count'];
                        ?>
                    </h3>
                </div>
                <div class="stat-icon" style="background: #fff3e0;">
                    <i class="bi bi-people-fill text-warning"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col">
        <div class="stat-card h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Volunteers</h6>
                    <h3 class="mb-0">
                        <?php
                        $stmt = $pdo->query("SELECT COUNT(*) as count FROM Volunteer WHERE status = 'Current'");
                        $result = $stmt->fetch();
                        echo $result['count'];
                        ?>
                    </h3>
                </div>
                <div class="stat-icon" style="background: #f3e5f5;">
                    <i class="bi bi-person-badge text-purple"></i>
                </div>
            </div>
        </div>
    </div>
</div>
        
        <!-- Recent Activities -->
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Activities</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Get recent donations
                                    $stmt = $pdo->query("
                                        SELECT donation_type, amount, donation_date 
                                        FROM Donation 
                                        ORDER BY donation_date DESC 
                                        LIMIT 5
                                    ");
                                    
                                    while ($row = $stmt->fetch()) {
                                        echo '<tr>';
                                        echo '<td><span class="badge bg-info">' . $row['donation_type'] . '</span></td>';
                                        echo '<td>Donation of $' . number_format($row['amount']) . '</td>';
                                        echo '<td>' . $row['donation_date'] . '</td>';
                                        echo '</tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <a href="modules/donations/add.php" class="list-group-item list-group-item-action">
                                <i class="bi bi-plus-circle me-2"></i> Add New Donation
                            </a>
                            <a href="modules/beneficiaries/manage.php" class="list-group-item list-group-item-action">
                                <i class="bi bi-person-plus me-2"></i> Add Beneficiary
                            </a>
                            <?php if (checkPermission('Manager')): ?>
                            <a href="modules/projects/manage.php" class="list-group-item list-group-item-action">
                                <i class="bi bi-clipboard-plus me-2"></i> Create Project
                            </a>
                            <?php endif; ?>
                            <?php if (checkPermission('CEO')): ?>
                            <a href="modules/staff/manage.php" class="list-group-item list-group-item-action">
                                <i class="bi bi-person-plus me-2"></i> Add Staff Member
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>