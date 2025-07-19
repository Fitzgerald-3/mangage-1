<?php
require_once '../backend/autoload.php';
require_once '../backend/src/Auth.php';
require_once '../backend/src/BookingManager.php';
require_once '../backend/src/EnquiryManager.php';

use App\Auth;
use App\BookingManager;
use App\EnquiryManager;

$auth = new Auth();
$auth->requireAuth();

$bookingManager = new BookingManager();
$enquiryManager = new EnquiryManager();

// Get statistics
$bookingStats = $bookingManager->getBookingStats();
$enquiryStats = $enquiryManager->getEnquiryStats();
$contactStats = $enquiryManager->getContactStats();

// Get recent data
$recentBookings = $bookingManager->getAllBookings(5);
$recentEnquiries = $enquiryManager->getAllEnquiries(5);

$user = $auth->getUser();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Nananom Farms</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block admin-sidebar">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h5><i class="fas fa-seedling me-2"></i>Nananom Farms</h5>
                        <small>Admin Panel</small>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="index.php">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="bookings.php">
                                <i class="fas fa-calendar-check me-2"></i>Bookings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="services.php">
                                <i class="fas fa-cogs me-2"></i>Services
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="enquiries.php">
                                <i class="fas fa-question-circle me-2"></i>Enquiries
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contact-messages.php">
                                <i class="fas fa-envelope me-2"></i>Contact Messages
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="users.php">
                                <i class="fas fa-users me-2"></i>Users
                            </a>
                        </li>
                    </ul>
                    
                    <hr class="my-3">
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="../index.php" target="_blank">
                                <i class="fas fa-external-link-alt me-2"></i>View Website
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                    <div class="text-muted">
                        Welcome back, <?php echo htmlspecialchars($user['full_name']); ?>!
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card admin-card border-left-primary">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total Bookings
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalBookings">
                                            <?php echo $bookingStats['total_bookings']; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card admin-card border-left-warning">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Pending Bookings
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="pendingBookings">
                                            <?php echo $bookingStats['pending_bookings']; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card admin-card border-left-info">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Total Enquiries
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalEnquiries">
                                            <?php echo $enquiryStats['total_enquiries']; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-question fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card admin-card border-left-success">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            New Messages
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="newMessages">
                                            <?php echo $contactStats['new_messages']; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-envelope fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="row">
                    <!-- Recent Bookings -->
                    <div class="col-lg-6 mb-4">
                        <div class="card admin-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">Recent Bookings</h6>
                                <a href="bookings.php" class="btn btn-sm btn-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <?php if (empty($recentBookings)): ?>
                                    <p class="text-muted text-center">No bookings yet.</p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Customer</th>
                                                    <th>Service</th>
                                                    <th>Date</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($recentBookings as $booking): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($booking['customer_name']); ?></td>
                                                        <td><?php echo htmlspecialchars($booking['service_name']); ?></td>
                                                        <td><?php echo date('M j', strtotime($booking['booking_date'])); ?></td>
                                                        <td>
                                                            <span class="badge bg-<?php echo $booking['status'] === 'confirmed' ? 'success' : ($booking['status'] === 'pending' ? 'warning' : 'secondary'); ?>">
                                                                <?php echo ucfirst($booking['status']); ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Enquiries -->
                    <div class="col-lg-6 mb-4">
                        <div class="card admin-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">Recent Enquiries</h6>
                                <a href="enquiries.php" class="btn btn-sm btn-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <?php if (empty($recentEnquiries)): ?>
                                    <p class="text-muted text-center">No enquiries yet.</p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Subject</th>
                                                    <th>Date</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($recentEnquiries as $enquiry): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($enquiry['name']); ?></td>
                                                        <td><?php echo htmlspecialchars(substr($enquiry['subject'], 0, 20)) . (strlen($enquiry['subject']) > 20 ? '...' : ''); ?></td>
                                                        <td><?php echo date('M j', strtotime($enquiry['created_at'])); ?></td>
                                                        <td>
                                                            <span class="badge bg-<?php echo $enquiry['status'] === 'resolved' ? 'success' : ($enquiry['status'] === 'new' ? 'warning' : 'info'); ?>">
                                                                <?php echo ucfirst(str_replace('_', ' ', $enquiry['status'])); ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
</html>