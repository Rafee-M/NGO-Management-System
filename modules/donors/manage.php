<?php
require_once '../../config.php';
checkLogin();

$donor_id = $_GET['id'] ?? 0;
$donor = null;
$page_title = 'Add New Donor';

// Fetch donor data if editing
if ($donor_id) {
    $stmt = $pdo->prepare("SELECT * FROM Donor WHERE donor_id = ?");
    $stmt->execute([$donor_id]);
    $donor = $stmt->fetch();
    
    if ($donor) {
        $page_title = 'Edit Donor: ' . $donor['first_name'] . ' ' . $donor['last_name'];
    } else {
        $donor_id = 0;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']) ?: null;
    $city= trim($_POST['city']) ?: null;
    // Validation
    $errors = [];
    
    if (empty($first_name)) $errors[] = "First name is required";
    if (empty($last_name)) $errors[] = "Last name is required";
    if (empty($email)) $errors[] = "Email is required";
    if (empty($phone)) $errors[] = "Phone number is required";
   
    
    
    // Save if no errors
    if (empty($errors)) {
        if ($donor_id) {
            // Update
            $stmt = $pdo->prepare("
                UPDATE Donor SET 
                first_name = ?, last_name = ?,email = ?, 
                phone = ?, address = ?, city = ?
                WHERE donor_id = ?
            ");
            $stmt->execute([
                $first_name, $last_name, $email, 
                $phone, $address, $city, $donor_id
            ]);
            $message = "Donor updated successfully!";
        } else {
            // Insert
            $stmt = $pdo->prepare("
                INSERT INTO Donor 
                (first_name, last_name, email, phone, address, city)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $first_name, $last_name, $email, $phone, $address, $city
            ]);
            $donor_id = $pdo->lastInsertId();
            $message = "Donor added successfully!";
        }
        
        if (isset($_POST['save_and_view'])) {
            header("Location: view.php?success=1&id=" . $donor_id);
            exit();
        } else {
            $success = $message;
            // Refresh data
            $stmt = $pdo->prepare("SELECT * FROM Donor WHERE donor_id = ?");
            $stmt->execute([$donor_id]);
            $donor = $stmt->fetch();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> Donor Management - NGO Management</title>
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
                    <h2><?php echo $donor_id ? 'Edit Donor' : 'Add New Donor'; ?></h2>
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
                                               value="<?php echo htmlspecialchars($donor['first_name'] ?? ''); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Last Name *</label>
                                        <input type="text" class="form-control" name="last_name" 
                                               value="<?php echo htmlspecialchars($donor['last_name'] ?? ''); ?>" required>
                                    </div>
                                    
                                
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" 
                                               value="<?php echo htmlspecialchars($donor['email'] ?? ''); ?>">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Phone *</label>
                                        <input type="text" class="form-control" name="phone" 
                                               value="<?php echo htmlspecialchars($donor['phone'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                
                                <!-- Address & Other Info -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Address</label>
                                        <input type="text" class="form-control" name="address" 
                                               value="<?php echo htmlspecialchars($donor['address'] ?? ''); ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">City</label>
                                        <input type="text" class="form-control" name="city" 
                                               value="<?php echo htmlspecialchars($donor['city'] ?? ''); ?>">
                                    </div>

                                    
                                    
                            
                            <!-- Read-only Info for Edit Mode -->
                            <?php if ($donor_id): ?>
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Donor ID</label>
                                        <input type="text" class="form-control" value="<?php echo $donor_id; ?>" readonly>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Buttons -->
                            <div class="mt-4">
                                <button type="submit" name="save" class="btn btn-primary">
                                    <?php echo $donor_id ? 'Update' : 'Save'; ?> Donor
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