<?php
require_once 'backend/autoload.php';
require_once 'backend/config/storage.php';

// Check if setup has already been completed
$storage = new FileStorage();
$setupComplete = $storage->query('setup_status');

if (!empty($setupComplete) && $setupComplete[0]['completed'] === true) {
    die('Setup has already been completed. If you need to reconfigure, delete the data/setup_status.json file.');
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate inputs
        $username = trim($_POST['admin_username'] ?? '');
        $password = $_POST['admin_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $email = trim($_POST['admin_email'] ?? '');
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        
        if (empty($username) || empty($password) || empty($email) || empty($first_name) || empty($last_name)) {
            throw new Exception('All fields are required.');
        }
        
        if ($password !== $confirm_password) {
            throw new Exception('Passwords do not match.');
        }
        
        if (strlen($password) < 6) {
            throw new Exception('Password must be at least 6 characters long.');
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email address.');
        }
        
        // Create auth configuration
        $authConfig = [
            'default_admin' => [
                'username' => $username,
                'password' => $password,
                'email' => $email,
                'first_name' => $first_name,
                'last_name' => $last_name
            ],
            'password_requirements' => [
                'min_length' => intval($_POST['min_length'] ?? 6),
                'require_uppercase' => isset($_POST['require_uppercase']),
                'require_numbers' => isset($_POST['require_numbers']),
                'require_special_chars' => isset($_POST['require_special_chars'])
            ],
            'session_timeout' => intval($_POST['session_timeout'] ?? 3600),
            'max_login_attempts' => intval($_POST['max_login_attempts'] ?? 5)
        ];
        
        // Save auth configuration
        $storage->insert('auth_config', $authConfig);
        
        // Create admin user
        $adminUser = [
            'username' => $username,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'role' => 'admin',
            'status' => 'active',
            'login_attempts' => 0
        ];
        
        $storage->insert('users', $adminUser);
        
        // Mark setup as complete
        $storage->insert('setup_status', ['completed' => true, 'setup_date' => date('Y-m-d H:i:s')]);
        
        $message = 'Setup completed successfully! You can now <a href="admin/login.php">login to the admin panel</a>.';
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nananom Farms - System Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            min-height: 100vh;
        }
        .setup-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 2rem 0;
        }
        .setup-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .setup-header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .setup-body {
            padding: 2rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .btn-setup {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-setup:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }
        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .alert-danger {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container setup-container">
        <div class="setup-card">
            <div class="setup-header">
                <h1><i class="fas fa-seedling me-2"></i>Nananom Farms</h1>
                <p class="mb-0">Palm Oil Marketing Management System Setup</p>
            </div>
            
            <div class="setup-body">
                <?php if ($message): ?>
                    <div class="alert alert-success">
                        <?php echo $message; ?>
                    </div>
                <?php elseif ($error): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if (empty($message)): ?>
                <form method="POST" action="">
                    <h4 class="mb-4">Administrator Account Setup</h4>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                       value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                       value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="admin_email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="admin_email" name="admin_email" 
                               value="<?php echo htmlspecialchars($_POST['admin_email'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="admin_username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="admin_username" name="admin_username" 
                               value="<?php echo htmlspecialchars($_POST['admin_username'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="admin_password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="admin_password" name="admin_password" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h5 class="mb-3">Security Settings</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="min_length" class="form-label">Minimum Password Length</label>
                                <input type="number" class="form-control" id="min_length" name="min_length" 
                                       value="<?php echo htmlspecialchars($_POST['min_length'] ?? '6'); ?>" min="4" max="20">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="session_timeout" class="form-label">Session Timeout (seconds)</label>
                                <input type="number" class="form-control" id="session_timeout" name="session_timeout" 
                                       value="<?php echo htmlspecialchars($_POST['session_timeout'] ?? '3600'); ?>" min="300" max="86400">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="max_login_attempts" class="form-label">Maximum Login Attempts</label>
                        <input type="number" class="form-control" id="max_login_attempts" name="max_login_attempts" 
                               value="<?php echo htmlspecialchars($_POST['max_login_attempts'] ?? '5'); ?>" min="3" max="10">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Password Requirements</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="require_uppercase" name="require_uppercase" 
                                   <?php echo isset($_POST['require_uppercase']) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="require_uppercase">
                                Require uppercase letters
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="require_numbers" name="require_numbers"
                                   <?php echo isset($_POST['require_numbers']) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="require_numbers">
                                Require numbers
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="require_special_chars" name="require_special_chars"
                                   <?php echo isset($_POST['require_special_chars']) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="require_special_chars">
                                Require special characters
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success btn-setup">
                            <i class="fas fa-cogs me-2"></i>Complete Setup
                        </button>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>