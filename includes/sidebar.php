<div class="sidebar-nav">
    <div class="list-group">
        <a href="../../dashboard.php" class="list-group-item list-group-item-action">
            <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
        
        <div class="list-group-item disabled">DATA MANAGEMENT</div>
        
        <a href="../donors/view.php" class="list-group-item list-group-item-action">
            <i class="bi bi-cash-coin me-2"></i> Donors
        </a>
        
        <a href="../donations/view.php" class="list-group-item list-group-item-action">
            <i class="bi bi-cash-coin me-2"></i> Donations
        </a>
        
        <a href="../beneficiaries/view.php" class="list-group-item list-group-item-action">
            <i class="bi bi-people-fill me-2"></i> Beneficiaries
        </a>
        
        <?php if (checkPermission('Manager')): ?>
        <a href="../projects/view.php" class="list-group-item list-group-item-action">
            <i class="bi bi-clipboard-data me-2"></i> Projects
        </a>
        <?php endif; ?>
        
        <a href="../volunteers/view.php" class="list-group-item list-group-item-action">
            <i class="bi bi-person-badge me-2"></i> Volunteers
        </a>
        
        <a href="../inventory/view.php" class="list-group-item list-group-item-action">
            <i class="bi bi-box-seam me-2"></i> Inventory
        </a>
        
        <?php if (checkPermission('CEO')): ?>
        <div class="list-group-item disabled">ADMINISTRATION</div>
        
        <a href="../staff/view.php" class="list-group-item list-group-item-action">
            <i class="bi bi-person-lines-fill me-2"></i> Staff
        </a>
        
        <a href="../reports/financial.php" class="list-group-item list-group-item-action">
            <i class="bi bi-graph-up me-2"></i> Reports
        </a>
        <?php endif; ?>
    </div>
</div>