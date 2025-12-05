<?php
require_once '../../config.php';
checkLogin();

$staff_id = $_GET['id'] ?? 0;
$staff = null;
$page_title = 'Add New Staff';

// Fetch staff data if editing
if ($staff_id) {
    $stmt = $pdo->prepare("SELECT * FROM Staff WHERE staff_id = ?");
    $stmt->execute([$staff_id]);
    $staff = $stmt->fetch();
    
    if ($staff) {
        $page_title = 'Edit Staff: ' . $staff['first_name'] . ' ' . $staff['last_name'];
    } else {
        $staff_id = 0;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $dob = $_POST['dob'] ?: null;
    $gender = $_POST['gender'] ?: null;
    $email = trim($_POST['email']) ?: null;
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']) ?: null;
    $position = trim($_POST['position']) ?: null;
    $nid = $_POST['nid'] ?: null;
    $supervisor_id = (int)$_POST['supervisor_id'] ;
    $hire_date = $_POST['hire_date'] ?: null;
    $status = $_POST['status'];
    
    // Validation
    $errors = [];
    
    if (empty($first_name)) $errors[] = "First name is required";
    if (empty($last_name)) $errors[] = "Last name is required";
    if (empty($phone)) $errors[] = "Phone number is required";
    if (empty($supervisor_id)) $errors[] = "Supervisor ID is required";
    
    
    // Save if no errors
    if (empty($errors)) {
        if ($staff_id) {
            // Update
            $stmt = $pdo->prepare("
                UPDATE Staff SET 
                first_name = ?, last_name = ?, dob = ?, gender = ?, email = ?, 
                phone = ?, address = ?, position = ?, nid = ?, supervisor_id = ?,
                hire_date = ?, status = ?
                WHERE staff_id = ?
            ");
            $stmt->execute([
                $first_name, $last_name, $dob, $gender, $email, 
                $phone, $address, $position, $nid,
                $supervisor_id, $hire_date, $status, $staff_id
            ]);
            $message = "Staff updated successfully!";
        } else {
            // Insert
            $stmt = $pdo->prepare("
                INSERT INTO Staff 
                (first_name, last_name, dob, gender, email, phone, address, 
                position, nid, supervisor_id, hire_date, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $first_name, $last_name, $dob, $gender, $email, 
                $phone, $address, $position, $nid,
                $supervisor_id, $hire_date, $status
            ]);
            $staff_id = $pdo->lastInsertId();
            $message = "Staff added successfully!";
        }
        
        if (isset($_POST['save_and_view'])) {
            header("Location: view.php?success=1&id=" . $staff_id);
            exit();
        } else {
            $success = $message;
            // Refresh data
            $stmt = $pdo->prepare("SELECT * FROM Staff WHERE staff_id = ?");
            $stmt->execute([$staff_id]);
            $staff = $stmt->fetch();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> Staff Management - NGO Management</title>
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
                    <h2><?php echo $staff_id ? 'Edit Staff' : 'Add New Staff'; ?></h2>
                    <a href="view.php" class="btn btn-secondary">Back to List</a>
                </div>
                
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="row">
                                <!-- Personal Info -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">First Name *</label>
                                        <input type="text" class="form-control" name="first_name" 
                                               value="<?php echo htmlspecialchars($staff['first_name'] ?? ''); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Last Name *</label>
                                        <input type="text" class="form-control" name="last_name" 
                                               value="<?php echo htmlspecialchars($staff['last_name'] ?? ''); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Date of Birth</label>
                                        <input type="date" class="form-control" name="dob" 
                                               value="<?php echo $staff['dob'] ?? ''; ?>">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Gender</label>
                                        <select class="form-select" name="gender">
                                            <option value="">Select Gender</option>
                                            <option value="Male" <?php echo ($staff['gender'] ?? '') == 'Male' ? 'selected' : ''; ?>>Male</option>
                                            <option value="Female" <?php echo ($staff['gender'] ?? '') == 'Female' ? 'selected' : ''; ?>>Female</option>
                                            <option value="Other" <?php echo ($staff['gender'] ?? '') == 'Other' ? 'selected' : ''; ?>>Other</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" 
                                               value="<?php echo htmlspecialchars($staff['email'] ?? ''); ?>">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Phone *</label>
                                        <input type="text" class="form-control" name="phone" 
                                               value="<?php echo htmlspecialchars($staff['phone'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                
                                <!-- Address & Other Info -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Address</label>
                                        <input type="text" class="form-control" name="address" 
                                               value="<?php echo htmlspecialchars($staff['address'] ?? ''); ?>">
                                    </div>

                                    
                                    <div class="mb-3">
                                        <label class="form-label">Position</label>
                                        <select class="form-select" name="position" id="position" required>
                                        <option value="Employee">Employee</option>
                                        <option value="Manager">Manager</option>
                                        <option value="CEO">CEO</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">National ID</label>
                                        <input type="text" class="form-control" name="nid" 
                                               value="<?php echo $staff['nid'] ?? ''; ?>">
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Supervisor ID</label>
                                                <input type="number" class="form-control" name="supervisor_id" 
                                                       value="<?php echo htmlspecialchars($staff['supervisor_id'] ?? ''); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                            <label class="form-label">Hire Date</label>
                                            <input type="date" class="form-control" name="hire_date" 
                                                value="<?php echo $staff['dob'] ?? ''; ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Status *</label>
                                        <select class="form-select" name="status" required>
                                            <option value="Active" <?php echo ($staff['status'] ?? 'Active') == 'Active' ? 'selected' : ''; ?>>Active</option>
                                            <option value="Inactive" <?php echo ($staff['status'] ?? '') == 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                                            
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Read-only Info for Edit Mode -->
                            <?php if ($staff_id): ?>
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Staff ID</label>
                                        <input type="text" class="form-control" value="<?php echo $staff_id; ?>" readonly>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Buttons -->
                            <div class="mt-4">
                                <button type="submit" name="save" class="btn btn-primary">
                                    <?php echo $staff_id ? 'Update' : 'Save'; ?> Staff
                                </button>
                                <button type="submit" name="save_and_view" class="btn btn-success">
                                    Save & View All
                                </button>
                                <a href="view.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>