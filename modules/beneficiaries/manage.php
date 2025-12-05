<?php
require_once '../../config.php';
checkLogin();

$beneficiary_id = $_GET['id'] ?? 0;
$beneficiary = null;
$page_title = 'Add New Beneficiary';

// Fetch beneficiary data if editing
if ($beneficiary_id) {
    $stmt = $pdo->prepare("SELECT * FROM Beneficiary WHERE beneficiary_id = ?");
    $stmt->execute([$beneficiary_id]);
    $beneficiary = $stmt->fetch();
    
    if ($beneficiary) {
        $page_title = 'Edit Beneficiary: ' . $beneficiary['first_name'] . ' ' . $beneficiary['last_name'];
    } else {
        $beneficiary_id = 0;
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
    $city = trim($_POST['city']);
    $occupation = trim($_POST['occupation']) ?: null;
    $nid = $_POST['nid'] ?: null;
    $family_size = (int)$_POST['family_size'] ?: 1;
    $income = (int)$_POST['income'] ?: 0;
    $special_needs = trim($_POST['special_needs']) ?: null;
    $status = $_POST['status'];
    
    // Validation
    $errors = [];
    
    if (empty($first_name)) $errors[] = "First name is required";
    if (empty($last_name)) $errors[] = "Last name is required";
    if (empty($phone)) $errors[] = "Phone number is required";
    if (empty($city)) $errors[] = "City is required";
    if ($family_size < 1) $errors[] = "Family size must be at least 1";
    if ($income < 0) $errors[] = "Income cannot be negative";
    
    // Save if no errors
    if (empty($errors)) {
        if ($beneficiary_id) {
            // Update
            $stmt = $pdo->prepare("
                UPDATE Beneficiary SET 
                first_name = ?, last_name = ?, dob = ?, gender = ?, email = ?, 
                phone = ?, address = ?, city = ?, occupation = ?, nid = ?, 
                family_size = ?, income = ?, special_needs = ?, status = ?
                WHERE beneficiary_id = ?
            ");
            $stmt->execute([
                $first_name, $last_name, $dob, $gender, $email, 
                $phone, $address, $city, $occupation, $nid,
                $family_size, $income, $special_needs, $status,
                $beneficiary_id
            ]);
            $message = "Beneficiary updated successfully!";
        } else {
            // Insert
            $stmt = $pdo->prepare("
                INSERT INTO Beneficiary 
                (first_name, last_name, dob, gender, email, phone, address, 
                 city, occupation, nid, family_size, income, special_needs, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $first_name, $last_name, $dob, $gender, $email, 
                $phone, $address, $city, $occupation, $nid,
                $family_size, $income, $special_needs, $status
            ]);
            $beneficiary_id = $pdo->lastInsertId();
            $message = "Beneficiary added successfully!";
        }
        
        if (isset($_POST['save_and_view'])) {
            header("Location: view.php?success=1&id=" . $beneficiary_id);
            exit();
        } else {
            $success = $message;
            // Refresh data
            $stmt = $pdo->prepare("SELECT * FROM Beneficiary WHERE beneficiary_id = ?");
            $stmt->execute([$beneficiary_id]);
            $beneficiary = $stmt->fetch();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - NGO Management</title>
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
                    <h2><?php echo $beneficiary_id ? 'Edit Beneficiary' : 'Add New Beneficiary'; ?></h2>
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
                                               value="<?php echo htmlspecialchars($beneficiary['first_name'] ?? ''); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Last Name *</label>
                                        <input type="text" class="form-control" name="last_name" 
                                               value="<?php echo htmlspecialchars($beneficiary['last_name'] ?? ''); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Date of Birth</label>
                                        <input type="date" class="form-control" name="dob" 
                                               value="<?php echo $beneficiary['dob'] ?? ''; ?>">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Gender</label>
                                        <select class="form-select" name="gender">
                                            <option value="">Select Gender</option>
                                            <option value="Male" <?php echo ($beneficiary['gender'] ?? '') == 'Male' ? 'selected' : ''; ?>>Male</option>
                                            <option value="Female" <?php echo ($beneficiary['gender'] ?? '') == 'Female' ? 'selected' : ''; ?>>Female</option>
                                            <option value="Other" <?php echo ($beneficiary['gender'] ?? '') == 'Other' ? 'selected' : ''; ?>>Other</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" 
                                               value="<?php echo htmlspecialchars($beneficiary['email'] ?? ''); ?>">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Phone *</label>
                                        <input type="text" class="form-control" name="phone" 
                                               value="<?php echo htmlspecialchars($beneficiary['phone'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                
                                <!-- Address & Other Info -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Address</label>
                                        <input type="text" class="form-control" name="address" 
                                               value="<?php echo htmlspecialchars($beneficiary['address'] ?? ''); ?>">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">City *</label>
                                        <input type="text" class="form-control" name="city" 
                                               value="<?php echo htmlspecialchars($beneficiary['city'] ?? ''); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Occupation</label>
                                        <input type="text" class="form-control" name="occupation" 
                                               value="<?php echo htmlspecialchars($beneficiary['occupation'] ?? ''); ?>">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">National ID</label>
                                        <input type="text" class="form-control" name="nid" 
                                               value="<?php echo $beneficiary['nid'] ?? ''; ?>">
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Family Size</label>
                                                <input type="number" class="form-control" name="family_size" 
                                                       value="<?php echo $beneficiary['family_size'] ?? 1; ?>" min="1">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Income (৳)</label>
                                                <input type="number" class="form-control" name="income" 
                                                       value="<?php echo $beneficiary['income'] ?? 0; ?>" min="0">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Special Needs</label>
                                        <textarea class="form-control" name="special_needs" rows="2"><?php echo htmlspecialchars($beneficiary['special_needs'] ?? ''); ?></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Status *</label>
                                        <select class="form-select" name="status" required>
                                            <option value="Active" <?php echo ($beneficiary['status'] ?? 'Active') == 'Active' ? 'selected' : ''; ?>>Active</option>
                                            <option value="Inactive" <?php echo ($beneficiary['status'] ?? '') == 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                                            <option value="Graduated" <?php echo ($beneficiary['status'] ?? '') == 'Graduated' ? 'selected' : ''; ?>>Graduated</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Read-only Info for Edit Mode -->
                            <?php if ($beneficiary_id): ?>
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Beneficiary ID</label>
                                        <input type="text" class="form-control" value="<?php echo $beneficiary_id; ?>" readonly>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Buttons -->
                            <div class="mt-4">
                                <button type="submit" name="save" class="btn btn-primary">
                                    <?php echo $beneficiary_id ? 'Update' : 'Save'; ?> Beneficiary
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