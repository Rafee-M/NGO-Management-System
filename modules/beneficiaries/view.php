<?php
require_once '../../config.php';
checkLogin();

// ========== HANDLE POST DELETE ==========
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $beneficiary_id = (int)$_POST['delete_id'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM Beneficiary WHERE beneficiary_id = ?");
        $stmt->execute([$beneficiary_id]);
        
        // Success - show message
        $success_message = "Beneficiary deleted successfully!";
    } catch (Exception $e) {
        $error_message = "Error deleting beneficiary: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beneficiaries Management - NGO Management</title>
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
                    <h2>Beneficiaries Management</h2>
                    <a href="manage.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add New beneficiary
                    </a>
                </div>
                
                <!-- Display Success/Error Messages -->
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $success_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">All Beneficiaries</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>City</th>
                                        <th>Occupation</th>
                                        <th>Family Size</th>
                                        <th>Income</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $pdo->query("
                                        SELECT *
                                        FROM Beneficiary
                                        ORDER BY beneficiary_id
                                    ");
                                    
                                    while ($row = $stmt->fetch()) {
                                        $status_color = $row['status'] == 'Active' ? 'success' : 'danger';
                                        
                                        echo '<tr>';
                                        echo '<td>' . $row['beneficiary_id'] . '</td>';
                                        echo '<td>' . $row['first_name'] . ' ' . $row['last_name'] . '</td>';
                                        echo '<td>' . $row['phone'] . '</td>';
                                        echo '<td>' . $row['city'] . '</td>';
                                        echo '<td>' . $row['occupation'] . '</td>';
                                        echo '<td>' . $row['family_size'] . '</td>';
                                        echo '<td>$' . number_format($row['income']) . '</td>';
                                        echo '<td><span class="badge bg-' . $status_color . '">' . $row['status'] . '</span></td>';
                                        echo '<td>
                                            <div class="d-flex gap-1">
                                                <a href="manage.php?id=' . $row['beneficiary_id'] . '" class="btn btn-sm btn-warning">Edit</a>
                                                <a href="allocate.php?id=' . $row['beneficiary_id'] . '" class="btn btn-sm btn-info">Allocate</a>
                                                
                                                <!-- POST Delete Form -->
                                                <form method="POST" action="" style="display: inline;" 
                                                      onsubmit="return confirmDelete(\'' . $row['first_name'] . ' ' . $row['last_name'] . '\')">
                                                    <input type="hidden" name="delete_id" value="' . $row['beneficiary_id'] . '">
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            </div>
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