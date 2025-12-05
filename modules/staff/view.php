<?php
require_once '../../config.php';
checkLogin();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $staff_id = (int)$_POST['delete_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM Staff WHERE staff_id = ?");
        $stmt->execute([$staff_id]);

        // Success - show message
        $success_message = "Staff deleted successfully!";
    } catch (Exception $e) {
        $error_message = "Error deleting Staff: " . $e->getMessage();
    }
}

// Only CEO can access
if (!checkPermission('CEO')) {
    header("Location: ../../dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Management - NGO Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function confirmDelete(name) {
            return confirm("Are you sure you want to delete " + name + "?");
        }
    </script>
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
                    <h2>Staff Management</h2>
                    <a href="manage.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add New Staff
                    </a>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">All Staff Members</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Status</th>
                                        <th>Hire Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $pdo->query("
                                        SELECT s1.*, s2.first_name as supervisor_fname, s2.last_name as supervisor_lname
                                        FROM Staff s1
                                        LEFT JOIN Staff s2 ON s1.supervisor_id = s2.staff_id
                                        ORDER BY s1.position DESC, s1.hire_date
                                    ");
                                    
                                    while ($row = $stmt->fetch()) {
                                        $status_color = $row['status'] == 'Active' ? 'success' : 'danger';
                                        
                                        echo '<tr>';
                                        echo '<td>' . $row['staff_id'] . '</td>';
                                        echo '<td>' . $row['first_name'] . ' ' . $row['last_name'] . '</td>';
                                        echo '<td>' . $row['position'] . '</td>';
                                        echo '<td>' . $row['email'] . '</td>';
                                        echo '<td>' . $row['phone'] . '</td>';
                                        echo '<td><span class="badge bg-' . $status_color . '">' . $row['status'] . '</span></td>';
                                        echo '<td>' . $row['hire_date'] . '</td>';
                                        echo '<td>
                                            <a href="manage.php?id=' . $row['staff_id'] . '" class="btn btn-sm btn-warning">Edit</a>
                                            <!-- POST Delete Form -->
                                            <form method="POST" action="" style="display: inline;" 
                                                   onsubmit="return confirmDelete(\'' . $row['first_name'] . ' ' . $row['last_name'] . '\')">
                                                <input type="hidden" name="delete_id" value="' . $row['staff_id'] . '">
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
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