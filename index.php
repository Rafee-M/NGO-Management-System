<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NGO Management System - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo h2 {
            color: #667eea;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo">
            <h2>NGO Management System</h2>
            <p class="text-muted">Sign in to your account</p>
        </div>
        
        <?php
        // Handle login
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            require_once 'config.php';
            
            $username = $_POST['username'];
            $password = $_POST['password'];
            
            // Simple authentication (for demo only)
            if ($username == 'ceo' && $password == 'ceo123') {
                $_SESSION['user_id'] = 1;
                $_SESSION['username'] = 'CEO';
                $_SESSION['user_level'] = 'CEO';
                $_SESSION['full_name'] = 'John Doe (CEO)';
                header("Location: dashboard.php");
                exit();
            } elseif ($username == 'manager' && $password == 'manager123') {
                $_SESSION['user_id'] = 2;
                $_SESSION['username'] = 'Manager';
                $_SESSION['user_level'] = 'Manager';
                $_SESSION['full_name'] = 'Jane Smith (Manager)';
                header("Location: dashboard.php");
                exit();
            } elseif ($username == 'employee' && $password == 'employee123') {
                $_SESSION['user_id'] = 3;
                $_SESSION['username'] = 'Employee';
                $_SESSION['user_level'] = 'Employee';
                $_SESSION['full_name'] = 'Bob Johnson (Employee)';
                header("Location: dashboard.php");
                exit();
            } else {
                echo '<div class="alert alert-danger">Invalid credentials!</div>';
            }
        }
        ?>
        
        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Sign In</button>
        </form>
        
        <div class="mt-4 text-center">
            <p class="text-muted mb-2">Demo Accounts:</p>
            <p class="mb-1"><strong>CEO:</strong> ceo / ceo123</p>
            <p class="mb-1"><strong>Manager:</strong> manager / manager123</p>
            <p class="mb-0"><strong>Employee:</strong> employee / employee123</p>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>