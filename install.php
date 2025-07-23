<?php
/**
 * Installation Check Script for Nananom Farms Palm Oil Management System
 * This script checks if the system is properly set up and ready to use.
 */

$checks = [];
$errors = [];
$warnings = [];

// Check PHP version
if (version_compare(PHP_VERSION, '8.0.0', '>=')) {
    $checks[] = "✅ PHP Version: " . PHP_VERSION . " (Required: 8.0+)";
} else {
    $errors[] = "❌ PHP Version: " . PHP_VERSION . " (Required: 8.0+)";
}

// Check required extensions
$required_extensions = ['json'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        $checks[] = "✅ PHP Extension: $ext";
    } else {
        $errors[] = "❌ PHP Extension: $ext (Required)";
    }
}

// Check file structure
$required_files = [
    'setup.php',
    'index.php',
    'booking.php',
    'contact.php',
    'enquiry.php',
    'backend/config/storage.php',
    'backend/src/Auth.php',
    'backend/src/ServiceManager.php',
    'backend/src/BookingManager.php',
    'backend/src/EnquiryManager.php',
    'admin/login.php',
    'admin/index.php',
    'api/booking.php',
    'api/get_services.php'
];

foreach ($required_files as $file) {
    if (file_exists($file)) {
        $checks[] = "✅ File exists: $file";
    } else {
        $errors[] = "❌ Missing file: $file";
    }
}

// Check data directory
$data_dir = 'data';
if (!file_exists($data_dir)) {
    $warnings[] = "⚠️ Data directory doesn't exist yet (will be created automatically)";
} else {
    if (is_writable($data_dir)) {
        $checks[] = "✅ Data directory is writable: $data_dir";
    } else {
        $errors[] = "❌ Data directory is not writable: $data_dir";
    }
}

// Check if setup has been run
require_once 'backend/config/storage.php';
$storage = new FileStorage();
$setupStatus = $storage->query('setup_status');

if (!empty($setupStatus) && $setupStatus[0]['completed'] === true) {
    $checks[] = "✅ System setup completed";
    $setup_completed = true;
} else {
    $warnings[] = "⚠️ System setup not completed - run setup.php";
    $setup_completed = false;
}

// Test file storage functionality
try {
    $test_data = ['test' => 'data', 'timestamp' => time()];
    $storage->insert('installation_test', $test_data);
    $retrieved = $storage->selectOne('installation_test', ['test' => 'data']);
    
    if ($retrieved && $retrieved['test'] === 'data') {
        $checks[] = "✅ File storage system working";
        // Clean up test data
        $storage->delete('installation_test', ['test' => 'data']);
    } else {
        $errors[] = "❌ File storage system not working properly";
    }
} catch (Exception $e) {
    $errors[] = "❌ File storage error: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation Check - Nananom Farms</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }
        .install-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .install-header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .status-item {
            padding: 0.5rem 0;
            border-bottom: 1px solid #eee;
        }
        .status-item:last-child {
            border-bottom: none;
        }
        .btn-action {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="install-card">
                    <div class="install-header">
                        <h1><i class="fas fa-seedling me-2"></i>Nananom Farms</h1>
                        <p class="mb-0">Installation System Check</p>
                    </div>
                    
                    <div class="card-body p-4">
                        <?php if (empty($errors)): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>System Ready!</strong> All requirements are met.
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Issues Found!</strong> Please resolve the errors below.
                            </div>
                        <?php endif; ?>

                        <!-- Errors -->
                        <?php if (!empty($errors)): ?>
                            <div class="mb-4">
                                <h5 class="text-danger">❌ Errors (Must Fix)</h5>
                                <?php foreach ($errors as $error): ?>
                                    <div class="status-item text-danger">
                                        <?php echo htmlspecialchars($error); ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Warnings -->
                        <?php if (!empty($warnings)): ?>
                            <div class="mb-4">
                                <h5 class="text-warning">⚠️ Warnings</h5>
                                <?php foreach ($warnings as $warning): ?>
                                    <div class="status-item text-warning">
                                        <?php echo htmlspecialchars($warning); ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Success Checks -->
                        <?php if (!empty($checks)): ?>
                            <div class="mb-4">
                                <h5 class="text-success">✅ Passed Checks</h5>
                                <?php foreach ($checks as $check): ?>
                                    <div class="status-item text-success">
                                        <?php echo htmlspecialchars($check); ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <hr>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6>Next Steps:</h6>
                                <ul class="list-unstyled">
                                    <?php if (!$setup_completed): ?>
                                        <li><i class="fas fa-arrow-right text-primary me-2"></i>Run setup.php</li>
                                    <?php endif; ?>
                                    <li><i class="fas fa-arrow-right text-primary me-2"></i>Access the website</li>
                                    <li><i class="fas fa-arrow-right text-primary me-2"></i>Login to admin panel</li>
                                </ul>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6>System Info:</h6>
                                <small class="text-muted">
                                    <strong>PHP:</strong> <?php echo PHP_VERSION; ?><br>
                                    <strong>Server:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?><br>
                                    <strong>Document Root:</strong> <?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'; ?>
                                </small>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <?php if (!$setup_completed): ?>
                                <a href="setup.php" class="btn btn-success btn-action">
                                    <i class="fas fa-cogs me-2"></i>Run Setup
                                </a>
                            <?php endif; ?>
                            <a href="index.php" class="btn btn-primary btn-action">
                                <i class="fas fa-home me-2"></i>View Website
                            </a>
                            <?php if ($setup_completed): ?>
                                <a href="admin/login.php" class="btn btn-success btn-action">
                                    <i class="fas fa-sign-in-alt me-2"></i>Admin Login
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>