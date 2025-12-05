<?php
require_once '../../config.php';
checkLogin();

// Check permission - Only Manager and CEO can view projects
if (!checkPermission('Manager')) {
    header("Location: ../../dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects - NGO Management</title>
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
                    <h2>Project Management</h2>
                    <a href="manage.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> New Project
                    </a>
                </div>
                
                <div class="row">
                    <?php
                    $stmt = $pdo->query("
                        SELECT p.*, s.first_name, s.last_name 
                        FROM Project p
                        LEFT JOIN Staff s ON p.project_manager = s.staff_id
                        ORDER BY p.start_date DESC
                    ");
                    
                    while ($row = $stmt->fetch()) {
                        $remaining_budget = $row['allocated_budget'] - $row['spent_budget'];
                        $utilization = $row['allocated_budget'] > 0 ? ($row['spent_budget'] / $row['allocated_budget']) * 100 : 0;
                        
                        // Status badge color
                        $status_color = 'bg-secondary';
                        if ($row['status'] == 'Active') $status_color = 'bg-success';
                        if ($row['status'] == 'Completed') $status_color = 'bg-primary';
                        if ($row['status'] == 'Planning') $status_color = 'bg-warning';
                        
                        echo '<div class="col-md-6 mb-4">';
                        echo '<div class="card h-100">';
                        echo '<div class="card-header d-flex justify-content-between align-items-center">';
                        echo '<h5 class="mb-0">' . $row['project_name'] . '</h5>';
                        echo '<span class="badge ' . $status_color . '">' . $row['status'] . '</span>';
                        echo '</div>';
                        echo '<div class="card-body">';
                        echo '<p class="card-text">' . substr($row['description'], 0, 150) . '...</p>';
                        echo '<div class="mb-3">';
                        echo '<small class="text-muted">Manager: ' . $row['first_name'] . ' ' . $row['last_name'] . '</small><br>';
                        echo '<small class="text-muted">Start: ' . $row['start_date'] . '</small>';
                        if ($row['end_date']) {
                            echo '<br><small class="text-muted">End: ' . $row['end_date'] . '</small>';
                        }
                        echo '</div>';
                        echo '<div class="progress mb-2" style="height: 10px;">';
                        echo '<div class="progress-bar bg-success" role="progressbar" style="width: ' . $utilization . '%"';
                        echo 'aria-valuenow="' . $utilization . '" aria-valuemin="0" aria-valuemax="100"></div>';
                        echo '</div>';
                        echo '<div class="d-flex justify-content-between">';
                        echo '<div>';
                        echo '<small>Allocated: $' . number_format($row['allocated_budget']) . '</small><br>';
                        echo '<small>Spent: $' . number_format($row['spent_budget']) . '</small><br>';
                        echo '<small>Remaining: $' . number_format($remaining_budget) . '</small>';
                        echo '</div>';
                        echo '<div class="align-self-end">';
                        echo '<a href="manage.php?id=' . $row['project_id'] . '" class="btn btn-sm btn-primary">Edit</a>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>