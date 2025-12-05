<?php
require_once '../../config.php';
checkLogin();

// Only Manager and CEO can manage projects
if (!checkPermission('Manager')) {
    header("Location: ../../dashboard.php");
    exit();
}

$project_id = $_GET['id'] ?? 0;
$project = null;
$available_cash = 0;
$current_allocated_budget = 0;

// Get total available cash
$cash_stmt = $pdo->query("SELECT COALESCE(SUM(amount), 0) as total_cash FROM CashInventory");
$total_cash = $cash_stmt->fetch()['total_cash'];

if ($project_id) {
    // Edit mode - fetch project
    $stmt = $pdo->prepare("SELECT * FROM Project WHERE project_id = ?");
    $stmt->execute([$project_id]);
    $project = $stmt->fetch();
    $current_allocated_budget = $project['allocated_budget'] ?? 0;
}

// Calculate available cash (same calculation for both new and edit)
$stmt = $pdo->query("
    SELECT 
        (SELECT COALESCE(SUM(amount), 0) FROM CashInventory) - 
        (SELECT COALESCE(SUM(allocated_budget), 0) FROM Project WHERE status IN ('Active', 'Completed'))
    as available_for_allocation
");
$available_cash = $stmt->fetch()['available_for_allocation'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $project_name = $_POST['project_name'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'] ?: null;
    $status = $_POST['status'];
    $project_manager = $_POST['project_manager'];
    $allocated_budget = $_POST['allocated_budget'] ?? 0;
    
    // Validate budget allocation
    if ($allocated_budget < 0) {
        die("Error: Budget cannot be negative");
    }
    
    // Check if enough cash is available (same check for both new and edit)
    if ($allocated_budget > $available_cash) {
        die("Error: Insufficient cash available. You can allocate up to $" . $available_cash);
    }
    
    // Check if status is Active/Completed - only these statuses consume budget
    $budget_consuming_status = in_array($status, ['Active', 'Completed']);
    
    if ($budget_consuming_status) {
        // If status consumes budget, use allocated_budget
        if ($project_id) {
            // Update existing project
            $stmt = $pdo->prepare("
                UPDATE Project SET 
                project_name = ?, description = ?, start_date = ?, end_date = ?, 
                status = ?, project_manager = ?, allocated_budget = ?
                WHERE project_id = ?
            ");
            $stmt->execute([$project_name, $description, $start_date, $end_date, $status, $project_manager, $allocated_budget, $project_id]);
        } else {
            // Insert new project
            $stmt = $pdo->prepare("
                INSERT INTO Project (project_name, description, start_date, end_date, status, project_manager, allocated_budget)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$project_name, $description, $start_date, $end_date, $status, $project_manager, $allocated_budget]);
            $project_id = $pdo->lastInsertId();
        }
    } else {
        // If status doesn't consume budget, set allocated_budget to 0
        if ($project_id) {
            $stmt = $pdo->prepare("
                UPDATE Project SET 
                project_name = ?, description = ?, start_date = ?, end_date = ?, 
                status = ?, project_manager = ?, allocated_budget = 0
                WHERE project_id = ?
            ");
            $stmt->execute([$project_name, $description, $start_date, $end_date, $status, $project_manager, $project_id]);
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO Project (project_name, description, start_date, end_date, status, project_manager, allocated_budget)
                VALUES (?, ?, ?, ?, ?, ?, 0)
            ");
            $stmt->execute([$project_name, $description, $start_date, $end_date, $status, $project_manager]);
            $project_id = $pdo->lastInsertId();
        }
    }
    
    header("Location: view.php?success=1");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $project ? 'Edit' : 'Add'; ?> Project - NGO Management</title>
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
                <h2><?php echo $project ? 'Edit Project' : 'Add New Project'; ?></h2>
                
                <!-- Budget Information Card -->
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Budget Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="alert alert-primary">
                                    <h6 class="alert-heading">Total Available Cash</h6>
                                    <p class="mb-0 fs-4">$<?php echo number_format($total_cash); ?></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-warning">
                                    <h6 class="alert-heading">Currently Allocated</h6>
                                    <p class="mb-0 fs-4">$<?php echo number_format($total_cash - $available_cash); ?></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-success">
                                    <h6 class="alert-heading">Available for Allocation</h6>
                                    <p class="mb-0 fs-4">$<?php echo number_format($available_cash); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php if ($project_id): ?>
                            <div class="alert alert-secondary">
                                <h6 class="alert-heading">Current Project Budget</h6>
                                <p class="mb-0">This project currently has $<?php echo number_format($current_allocated_budget); ?> allocated.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">Project Name</label>
                                <input type="text" class="form-control" name="project_name" 
                                       value="<?php echo $project['project_name'] ?? ''; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="4" required><?php echo $project['description'] ?? ''; ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Start Date</label>
                                        <input type="date" class="form-control" name="start_date" 
                                               value="<?php echo $project['start_date'] ?? date('Y-m-d'); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">End Date (Optional)</label>
                                        <input type="date" class="form-control" name="end_date" 
                                               value="<?php echo $project['end_date'] ?? ''; ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" name="status" id="status" required>
                                            <option value="Planning" <?php echo ($project['status'] ?? '') == 'Planning' ? 'selected' : ''; ?>>Planning</option>
                                            <option value="Active" <?php echo ($project['status'] ?? '') == 'Active' ? 'selected' : ''; ?>>Active</option>
                                            <option value="Completed" <?php echo ($project['status'] ?? '') == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                            <option value="Cancelled" <?php echo ($project['status'] ?? '') == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Allocated Budget ($)</label>
                                        <input type="number" class="form-control" name="allocated_budget" 
                                               id="allocated_budget" 
                                               value="<?php echo $project['allocated_budget'] ?? 0; ?>" 
                                               min="0" 
                                               max="<?php echo $available_cash; ?>">
                                        <div class="form-text">
                                            Only applicable for Active/Completed projects.
                                            Max: $<?php echo number_format($available_cash); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Project Manager</label>
                                        <select class="form-select" name="project_manager" required>
                                            <option value="">Select Manager</option>
                                            <?php
                                            $stmt = $pdo->query("SELECT staff_id, first_name, last_name FROM Staff WHERE status = 'Active'");
                                            while ($row = $stmt->fetch()) {
                                                $selected = ($project['project_manager'] ?? '') == $row['staff_id'] ? 'selected' : '';
                                                echo '<option value="' . $row['staff_id'] . '" ' . $selected . '>' . $row['first_name'] . ' ' . $row['last_name'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary"><?php echo $project ? 'Update' : 'Create'; ?> Project</button>
                            <a href="view.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.getElementById('status');
            const budgetInput = document.getElementById('allocated_budget');
            
            // Function to update budget field based on status
            function updateBudgetField() {
                const status = statusSelect.value;
                
                if (status === 'Active' || status === 'Completed') {
                    // Show and enable budget field
                    budgetInput.disabled = false;
                    budgetInput.required = true;
                    budgetInput.parentElement.style.display = 'block';
                } else {
                    // Hide and disable budget field, set to 0
                    budgetInput.disabled = true;
                    budgetInput.required = false;
                    budgetInput.value = 0;
                    budgetInput.parentElement.style.display = 'block'; // Still show but disabled
                }
            }
            
            // Initial update
            updateBudgetField();
            
            // Update on status change
            statusSelect.addEventListener('change', updateBudgetField);
            
            // Form validation for budget
            document.querySelector('form').addEventListener('submit', function(e) {
                const status = statusSelect.value;
                const budget = parseFloat(budgetInput.value);
                const maxBudget = parseFloat(budgetInput.max);
                
                if ((status === 'Active' || status === 'Completed') && budget > maxBudget) {
                    e.preventDefault();
                    alert(`Budget cannot exceed available cash. Maximum allowed: $${maxBudget.toLocaleString()}`);
                    budgetInput.focus();
                }
                
                if ((status === 'Active' || status === 'Completed') && budget <= 0) {
                    e.preventDefault();
                    alert('Budget must be greater than 0 for Active/Completed projects');
                    budgetInput.focus();
                }
            });
        });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>