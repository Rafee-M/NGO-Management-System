<?php
session_start();

define('DB_HOST', 'localhost');
define('DB_NAME', 'ngo_management');
define('DB_USER', 'root');
define('DB_PASS', '');

// Database connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(3, 2); // Error mode exception
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// User levels
$user_levels = [
    'Employee' => 1,
    'Manager' => 2,
    'CEO' => 3
];

// Function definitions
function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit();
    }
}

function checkPermission($required_level) {
    global $user_levels;
    
    if (!isset($_SESSION['user_level'])) {
        return false;
    }
    
    $user_level = $_SESSION['user_level'];
    $user_rank = $user_levels[$user_level];
    $required_rank = $user_levels[$required_level];
    
    return $user_rank >= $required_rank;
}
?>