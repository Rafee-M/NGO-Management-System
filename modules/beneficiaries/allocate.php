<?php
require_once '../../config.php';
checkLogin();

if (!checkPermission('Manager') || !checkPermission('CEO')) {
    header("Location: view.php");
    exit();
}

// Get beneficiary ID from URL
$beneficiary_id = $_GET['id'] ?? 0;
if (!$beneficiary_id) {
    header("Location: view.php");
    exit();
}

// Fetch beneficiary details
$stmt = $pdo->prepare("SELECT * FROM Beneficiary WHERE beneficiary_id = ?");
$stmt->execute([$beneficiary_id]);
$beneficiary = $stmt->fetch();

if (!$beneficiary) {
    header("Location: view.php");
    exit();
}

// Fetch active projects for dropdown
$projects_stmt = $pdo->query("
    SELECT project_id, project_name, allocated_budget, spent_budget 
    FROM Project 
    WHERE status IN ('Active', 'Planning')
    ORDER BY project_name
");

// Fetch available goods for goods allocation
$goods_stmt = $pdo->query("
    SELECT item_id, item_name, available_quantity 
    FROM GoodsInventory 
    WHERE available_quantity > 0 AND status = 'In Stock'
    ORDER BY item_name
");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $project_id = $_POST['project_id'];
    $allocation_type = $_POST['allocation_type'];
    $status = $_POST['status'] ?? 'Pending';
    
    try {
        $pdo->beginTransaction();
        
        if ($allocation_type == 'Cash') {
            $cash_amount = (int)$_POST['cash_amount'];
            
            // Check project budget
            $stmt = $pdo->prepare("
                SELECT allocated_budget, spent_budget 
                FROM Project 
                WHERE project_id = ? FOR UPDATE
            ");
            $stmt->execute([$project_id]);
            $project = $stmt->fetch();
            
            if (!$project) {
                throw new Exception("Project not found");
            }
            
            $remaining_budget = $project['allocated_budget'] - $project['spent_budget'];
            if ($cash_amount > $remaining_budget) {
                throw new Exception("Project has insufficient budget. Available: $" . $remaining_budget);
            }
            
            // Insert cash allocation
            $stmt = $pdo->prepare("
                INSERT INTO Beneficiary_Allocation 
                (project_id, beneficiary_id, allocation_type, cash_amount, status, allocation_date)
                VALUES (?, ?, 'Cash', ?, ?, CURDATE())
            ");
            $stmt->execute([$project_id, $beneficiary_id, $cash_amount, $status]);
            
            // Update project spent budget
            $stmt = $pdo->prepare("
                UPDATE Project 
                SET spent_budget = spent_budget + ?
                WHERE project_id = ?
            ");
            $stmt->execute([$cash_amount, $project_id]);
            
        } elseif ($allocation_type == 'Goods') {
            $item_id = (int)$_POST['item_id'];
            $quantity = (int)$_POST['quantity'];
            
            // Check goods availability
            $stmt = $pdo->prepare("
                SELECT available_quantity 
                FROM GoodsInventory 
                WHERE item_id = ? FOR UPDATE
            ");
            $stmt->execute([$item_id]);
            $goods = $stmt->fetch();
            
            if (!$goods) {
                throw new Exception("Item not found");
            }
            
            if ($quantity > $goods['available_quantity']) {
                throw new Exception("Insufficient quantity. Available: " . $goods['available_quantity']);
            }
            
            // Insert goods allocation
            $stmt = $pdo->prepare("
                INSERT INTO Beneficiary_Allocation 
                (project_id, beneficiary_id, allocation_type, item_id, quantity, status, allocation_date)
                VALUES (?, ?, 'Goods', ?, ?, ?, CURDATE())
            ");
            $stmt->execute([$project_id, $beneficiary_id, $item_id, $quantity, $status]);
            
            // Update goods inventory
            $stmt = $pdo->prepare("
                UPDATE GoodsInventory 
                SET available_quantity = available_quantity - ?
                WHERE item_id = ?
            ");
            $stmt->execute([$quantity, $item_id]);
        }
        
        $pdo->commit();
        header("Location: view.php?success=Allocation+completed+successfully");
        exit();
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $error_message = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Allocate to Beneficiary - NGO Management</title>
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
                <h2>Allocate to Beneficiary</h2>
                
                <!-- Beneficiary Info Card -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Beneficiary Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($beneficiary['first_name'] . ' ' . $beneficiary['last_name']); ?></p>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($beneficiary['phone']); ?></p>
                                <p><strong>City:</strong> <?php echo htmlspecialchars($beneficiary['city']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Occupation:</strong> <?php echo htmlspecialchars($beneficiary['occupation']); ?></p>
                                <p><strong>Family Size:</strong> <?php echo htmlspecialchars($beneficiary['family_size']); ?></p>
                                <p><strong>Status:</strong> <span class="badge bg-<?php echo $beneficiary['status'] == 'Active' ? 'success' : 'danger'; ?>"><?php echo htmlspecialchars($beneficiary['status']); ?></span></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Project *</label>
                                    <select class="form-select" name="project_id" required>
                                        <option value="">Select Project</option>
                                        <?php
                                        while ($project = $projects_stmt->fetch()) {
                                            $remaining = $project['allocated_budget'] - $project['spent_budget'];
                                            echo '<option value="' . $project['project_id'] . '">' . 
                                                 htmlspecialchars($project['project_name']) . 
                                                 ' (Budget: $' . number_format($remaining) . ' available)' .
                                                 '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Allocation Status *</label>
                                    <select class="form-select" name="status" required>
                                        <option value="Pending" selected>Pending</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Allocation Type *</label>
                                <select class="form-select" name="allocation_type" id="allocation_type" required>
                                    <option value="Cash">Cash</option>
                                    <option value="Goods">Goods</option>
                                </select>
                            </div>
                            
                            <!-- Cash Allocation Fields -->
                            <div id="cash_fields">
                                <div class="mb-3">
                                    <label class="form-label">Cash Amount ($) *</label>
                                    <input type="number" class="form-control" name="cash_amount" 
                                           id="cash_amount" min="1" value="" placeholder="Enter amount">
                                    <div class="form-text">Enter the cash amount to allocate</div>
                                </div>
                            </div>
                            
                            <!-- Goods Allocation Fields -->
                            <div id="goods_fields" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Item *</label>
                                            <select class="form-select" name="item_id" id="item_id">
                                                <option value="">Select Item</option>
                                                <?php
                                                while ($goods = $goods_stmt->fetch()) {
                                                    echo '<option value="' . $goods['item_id'] . '" data-quantity="' . $goods['available_quantity'] . '">' . 
                                                         htmlspecialchars($goods['item_name']) . 
                                                         ' (' . $goods['available_quantity'] . ' available)' .
                                                         '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Quantity *</label>
                                            <input type="number" class="form-control" name="quantity" 
                                                   id="quantity" min="1" value="" placeholder="Enter quantity">
                                            <div class="form-text" id="quantity_help">Select an item first</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">Allocate</button>
                                <a href="view.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const allocationType = document.getElementById('allocation_type');
            const cashFields = document.getElementById('cash_fields');
            const goodsFields = document.getElementById('goods_fields');
            const itemSelect = document.getElementById('item_id');
            const quantityInput = document.getElementById('quantity');
            const quantityHelp = document.getElementById('quantity_help');
            
            // Function to update form fields based on allocation type
            function updateAllocationFields() {
                const type = allocationType.value;
                
                if (type === 'Cash') {
                    cashFields.style.display = 'block';
                    goodsFields.style.display = 'none';
                } else {
                    cashFields.style.display = 'none';
                    goodsFields.style.display = 'block';
                }
            }
            
            // Update quantity help text when item is selected
            itemSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const maxQuantity = selectedOption.getAttribute('data-quantity');
                
                if (maxQuantity) {
                    quantityInput.max = maxQuantity;
                    quantityHelp.textContent = 'Maximum quantity: ' + maxQuantity;
                } else {
                    quantityHelp.textContent = 'Select an item first';
                }
            });
            
            // Validate goods allocation before submission
            document.querySelector('form').addEventListener('submit', function(e) {
                const type = allocationType.value;
                
                if (type === 'Cash') {
                    const cashAmount = document.getElementById('cash_amount').value;
                    if (!cashAmount || cashAmount <= 0) {
                        e.preventDefault();
                        alert('Please enter a valid cash amount');
                        return false;
                    }
                } else if (type === 'Goods') {
                    const itemId = itemSelect.value;
                    const quantity = quantityInput.value;
                    
                    if (!itemId) {
                        e.preventDefault();
                        alert('Please select an item');
                        return false;
                    }
                    
                    if (!quantity || quantity <= 0) {
                        e.preventDefault();
                        alert('Please enter a valid quantity');
                        return false;
                    }
                    
                    const maxQuantity = parseInt(itemSelect.options[itemSelect.selectedIndex].getAttribute('data-quantity'));
                    if (parseInt(quantity) > maxQuantity) {
                        e.preventDefault();
                        alert('Quantity exceeds available stock. Maximum: ' + maxQuantity);
                        return false;
                    }
                }
            });
            
            // Initial update
            updateAllocationFields();
            
            // Update on allocation type change
            allocationType.addEventListener('change', updateAllocationFields);
        });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>