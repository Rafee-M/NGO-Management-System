<?php
require_once '../../config.php';
checkLogin();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $volunteer_id = (int)$_POST['delete_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM Volunteer WHERE volunteer_id = ?");
        $stmt->execute([$volunteer_id]);

        // Success - show message
        $success_message = "Volunteer deleted successfully!";
    } catch (Exception $e) {
        $error_message = "Error deleting Volunteer: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volunteer Management - NGO Management</title>
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
                    <h2>Volunteer Management</h2>
                    <a href="manage.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add New Volunteer
                    </a>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">All Volunteers</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Date of Birth</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>City</th>
                                        <th>NID</th>
                                        <th>Date Joined</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $pdo->query("
                                        SELECT *
                                        FROM Volunteer
                                        ORDER BY volunteer_id
                                    ");
                                    
                                    while ($row = $stmt->fetch()) {
                                        $status_color = $row['status'] == 'Current' ? 'success' : 'danger';
                                        
                                        echo '<tr>';
                                        echo '<td>' . $row['volunteer_id'] . '</td>'; 
                                        echo '<td>' . $row['first_name'] . ' ' . $row['last_name'] . '</td>';
                                        echo '<td>' . $row['dob'] . '</td>';
                                        echo '<td>' . $row['email'] . '</td>';
                                        echo '<td>' . $row['phone'] . '</td>';
                                        echo '<td>' . $row['city'] . '</td>';
                                        echo '<td>' . $row['nid'] . '</td>';
                                        echo '<td>' . $row['date_joined'] . '</td>';
                                        echo '<td><span class="badge bg-' . $status_color . '">' . $row['status'] . '</span></td>';
                                        echo '<td>
                                            <a href="manage.php?id=' . $row['volunteer_id'] . '" class="btn btn-sm btn-warning">Edit</a>
                                            <!-- POST Delete Form -->
                                            <form method="POST" action="" style="display: inline;" 
                                                   onsubmit="return confirmDelete(\'' . $row['first_name'] . ' ' . $row['last_name'] . '\')">
                                                <input type="hidden" name="delete_id" value="' . $row['volunteer_id'] . '">
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