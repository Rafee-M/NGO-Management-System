<?php
require_once '../../config.php';
checkLogin();

$volunteer_id = $_GET['id'] ?? 0;
$volunteer = null;
$page_title = 'Add New Volunteer';

// Fetch volunteer if editing
if ($volunteer_id) {
    $stmt = $pdo->prepare("SELECT * FROM Volunteer WHERE volunteer_id = ?");
    $stmt->execute([$volunteer_id]);
    $volunteer = $stmt->fetch();

    if ($volunteer) {
        $page_title = "Edit Volunteer: " . $volunteer['first_name'] . " " . $volunteer['last_name'];
    } else {
        $volunteer_id = 0; // Safe fallback
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Form inputs
    $first_name   = trim($_POST['first_name']);
    $last_name    = trim($_POST['last_name']);
    $email        = trim($_POST['email']);
    $dob          = $_POST['dob'] ?: null;
    $phone        = trim($_POST['phone']);
    $address      = trim($_POST['address']);
    $city         = trim($_POST['city']);
    $nid          = trim($_POST['nid']);
    $date_joined  = $_POST['date_joined'] ?: date('Y-m-d');
    $status       = $_POST['status'] ?? 'Current';

    // Validation
    $errors = [];
    if (empty($first_name)) $errors[] = "First name is required.";
    if (empty($last_name))  $errors[] = "Last name is required.";
    if (empty($phone))      $errors[] = "Phone number is required.";

    // Save
    if (empty($errors)) {

        if ($volunteer_id) {
            // UPDATE
            $stmt = $pdo->prepare("
                UPDATE Volunteer SET 
                first_name = ?, last_name = ?, dob = ?, email = ?, phone = ?, 
                address = ?, city = ?, nid = ?, date_joined = ?, status = ?
                WHERE volunteer_id = ?
            ");

            $stmt->execute([
                $first_name, $last_name, $dob, $email, $phone,
                $address, $city, $nid, $date_joined, $status,
                $volunteer_id
            ]);

            $message = "Volunteer updated successfully!";

        } else {
            // INSERT
            $stmt = $pdo->prepare("
                INSERT INTO Volunteer
                (first_name, last_name, dob, email, phone, address, city, nid, date_joined, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $first_name, $last_name, $dob, $email, $phone,
                $address, $city, $nid, $date_joined, $status
            ]);

            $volunteer_id = $pdo->lastInsertId();
            $message = "Volunteer added successfully!";
        }

        // If user clicked "Save & View All"
        if (isset($_POST['save_and_view'])) {
            header("Location: view.php?success=1");
            exit();
        }

        $success = $message;

        // Reload volunteer data
        $stmt = $pdo->prepare("SELECT * FROM Volunteer WHERE volunteer_id = ?");
        $stmt->execute([$volunteer_id]);
        $volunteer = $stmt->fetch();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - NGO Management</title>
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
                <h2><?= $volunteer_id ? 'Edit Volunteer' : 'Add New Volunteer' ?></h2>
                <a href="view.php" class="btn btn-secondary">Back to List</a>
            </div>

            <!-- Success Message -->
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <!-- Error Messages -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>


            <div class="card">
                <div class="card-body">

                    <form method="POST">

                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">First Name *</label>
                                <input type="text" class="form-control" required 
                                       name="first_name" value="<?= $volunteer['first_name'] ?? '' ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Last Name *</label>
                                <input type="text" class="form-control" required 
                                       name="last_name" value="<?= $volunteer['last_name'] ?? '' ?>">
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control"
                                       name="email" value="<?= $volunteer['email'] ?? '' ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" class="form-control"
                                       name="dob" value="<?= $volunteer['dob'] ?? '' ?>">
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label">Phone *</label>
                                <input type="text" class="form-control" required
                                       name="phone" value="<?= $volunteer['phone'] ?? '' ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Address</label>
                                <input type="text" class="form-control"
                                       name="address" value="<?= $volunteer['address'] ?? '' ?>">
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control"
                                       name="city" value="<?= $volunteer['city'] ?? '' ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">NID</label>
                                <input type="text" class="form-control"
                                       name="nid" value="<?= $volunteer['nid'] ?? '' ?>">
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label">Date Joined</label>
                                <input type="date" class="form-control"
                                       name="date_joined"
                                       value="<?= $volunteer['date_joined'] ?? date('Y-m-d') ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="Current"   <?= ($volunteer['status'] ?? '') == 'Current' ? 'selected' : '' ?>>Current</option>
                                    <option value="On-leave"  <?= ($volunteer['status'] ?? '') == 'On-leave' ? 'selected' : '' ?>>On-leave</option>
                                </select>
                            </div>
                        </div>

                      <!-- Read-only Info for Edit Mode -->
                            <?php if ($volunteer_id): ?>
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Volunteer ID</label>
                                        <input type="text" class="form-control" value="<?php echo $volunteer_id; ?>" readonly>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Buttons -->
                            <div class="mt-4">
                                <button type="submit" name="save" class="btn btn-primary">
                                    <?php echo $volunteer_id ? 'Update' : 'Save'; ?> Volunteer
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
