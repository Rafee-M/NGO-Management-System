<?php
require_once '../../config.php';
checkLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donations - NGO Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
                    <h2>Donations Management</h2>
                    <a href="add.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add New Donation
                    </a>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">All Donations</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Type</th>
                                        <th>Amount/Item</th>
                                        <th>Donor Info</th>
                                        <th>Date</th>
                                        <th>Payment Method</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $pdo->query("
                                        SELECT d.*, dn.first_name, dn.last_name 
                                        FROM Donation d
                                        LEFT JOIN Donor dn ON d.donor_id = dn.donor_id
                                        ORDER BY d.donation_date DESC
                                    ");
                                    
                                    while ($row = $stmt->fetch()) {
                                        echo '<tr>';
                                        echo '<td>' . $row['donation_id'] . '</td>';
                                        echo '<td><span class="badge ' . ($row['donation_type'] == 'Money' ? 'bg-success' : 'bg-warning') . '">' . $row['donation_type'] . '</span></td>';
                                        
                                        if ($row['donation_type'] == 'Money') {
                                            echo '<td>$' . number_format($row['amount']) . '</td>';
                                        } else {
                                            echo '<td>' . $row['quantity'] . ' ' . $row['item_name'] . '</td>';
                                        }
                                        
                                        echo '<td>' . $row['first_name'] . ' ' . $row['last_name'] . '</td>';
                                        echo '<td>' . $row['donation_date'] . '</td>';
                                        echo '<td>' . ($row['payment_method'] ?? 'N/A') . '</td>';
                                        echo '<td>
                                            <a href="#" class="btn btn-sm btn-info">View</a>
                                        </td>';
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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>