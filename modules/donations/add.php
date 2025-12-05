<?php
require_once '../../config.php';
checkLogin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $donation_type = $_POST['donation_type'];
    $amount = $_POST['amount'];
    $donor_id = $_POST['donor_id'];
    $payment_method = $_POST['payment_method'];
    $item_name = $_POST['item_name'] ?? '';
    $quantity = $_POST['quantity'] ?? 0;
    $description = $_POST['description'] ?? '';
    
    // Insert donation. CashInventory and GoodsInventory handled by triggers
    $stmt = $pdo->prepare("
        INSERT INTO Donation (donor_id, donation_type, amount, payment_method, item_name, quantity, description, donation_date)
        VALUES (?, ?, ?, ?, ?, ?, ?, CURDATE())
    ");
    
    $stmt->execute([$donor_id, $donation_type, $amount, $payment_method, $item_name, $quantity, $description]);
    

    
    header("Location: view.php?success=1");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Donation - NGO Management</title>
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
                <h2>Add New Donation</h2>
                
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">Donation Type</label>
                                <select class="form-select" name="donation_type" id="donation_type" required>
                                    <option value="Money">Money</option>
                                    <option value="Goods">Goods</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Donor</label>
                                <select class="form-select" name="donor_id" required>
                                    <option value="">Select Donor</option>
                                    <?php
                                    $stmt = $pdo->query("SELECT donor_id, first_name, last_name FROM Donor");
                                    while ($row = $stmt->fetch()) {
                                        echo '<option value="' . $row['donor_id'] . '">' . $row['first_name'] . ' ' . $row['last_name'] . '</option>';
                                    }
                                    ?>
                                </select>
                                <div class="mt-1">
                                    <small>
                                        <a href="../donors/manage.php" target="_blank" class="text-decoration-none">
                                            + Add New Donor
                                        </a>
                                    </small>
                                </div>
                            </div>
                            
                            <div id="money_fields">
                                <div class="mb-3">
                                    <label class="form-label">Amount ($)</label>
                                    <input type="number" class="form-control" name="amount" placeholder="Enter amount">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Payment Method</label>
                                    <select class="form-select" name="payment_method">
                                        <option value="Cash">Cash</option>
                                        <option value="Bank Transfer">Bank Transfer</option>
                                        <option value="Check">Check</option>
                                        <option value="Online">Online Payment</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description (Optional)</label>
                                    <textarea class="form-control" name="description" rows="3" placeholder="Enter donation description, purpose, or any special notes..."></textarea>
                                </div>
                            </div>
                            
                            <div id="goods_fields" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label">Item Name</label>
                                    <input type="text" class="form-control" name="item_name" placeholder="e.g., Rice, Blankets">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Quantity</label>
                                    <input type="number" class="form-control" name="quantity" placeholder="Enter quantity">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description (Optional)</label>
                                    <textarea class="form-control" name="description" rows="3" placeholder="Enter item details, condition, specifications, or any notes..."></textarea>
                                    <div class="form-text">For goods donations, this description will appear in inventory records</div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Add Donation</button>
                            <a href="view.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.getElementById('donation_type').addEventListener('change', function() {
            var type = this.value;
            if (type == 'Money') {
                document.getElementById('money_fields').style.display = 'block';
                document.getElementById('goods_fields').style.display = 'none';
            } else {
                document.getElementById('money_fields').style.display = 'none';
                document.getElementById('goods_fields').style.display = 'block';
            }
        });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>