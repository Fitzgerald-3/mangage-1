<?php
require_once 'backend/autoload.php';
require_once 'backend/src/ServiceManager.php';

use App\ServiceManager;

$serviceManager = new ServiceManager();
$services = $serviceManager->getAllServices(true);

// If service ID is provided in URL, pre-select it
$selectedServiceId = $_GET['service'] ?? null;
$selectedService = null;
if ($selectedServiceId) {
    $selectedService = $serviceManager->getServiceById($selectedServiceId);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Service - Nananom Farms</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-seedling me-2"></i>Nananom Farms
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Booking Form Section -->
    <section class="py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card shadow-lg">
                        <div class="card-header bg-success text-white">
                            <h3 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Book a Service</h3>
                        </div>
                        <div class="card-body p-5">
                            <div id="alertContainer"></div>
                            
                            <form id="bookingForm">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="customer_name" class="form-label">Full Name *</label>
                                        <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="customer_email" class="form-label">Email Address *</label>
                                        <input type="email" class="form-control" id="customer_email" name="customer_email" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="customer_phone" class="form-label">Phone Number *</label>
                                        <input type="tel" class="form-control" id="customer_phone" name="customer_phone" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="service_id" class="form-label">Service *</label>
                                        <select class="form-control" id="service_id" name="service_id" required>
                                            <option value="">Select a service</option>
                                            <?php foreach ($services as $service): ?>
                                                <option value="<?php echo $service['id']; ?>" 
                                                        <?php echo ($selectedServiceId == $service['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($service['name']); ?> - $<?php echo number_format($service['price'], 2); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="booking_date" class="form-label">Preferred Date *</label>
                                        <input type="date" class="form-control" id="booking_date" name="booking_date" 
                                               min="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="booking_time" class="form-label">Preferred Time *</label>
                                        <select class="form-control" id="booking_time" name="booking_time" required>
                                            <option value="">Select a date first</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="message" class="form-label">Additional Message (Optional)</label>
                                    <textarea class="form-control" id="message" name="message" rows="4" 
                                              placeholder="Any specific requirements or questions..."></textarea>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-success btn-lg px-5">
                                        <i class="fas fa-calendar-plus me-2"></i>Book Service
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Service Details -->
                    <?php if ($selectedService): ?>
                    <div class="card mt-4 shadow">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Service Details</h5>
                        </div>
                        <div class="card-body">
                            <h6><?php echo htmlspecialchars($selectedService['name']); ?></h6>
                            <p class="text-muted"><?php echo htmlspecialchars($selectedService['description']); ?></p>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Price:</strong> $<?php echo number_format($selectedService['price'], 2); ?>
                                </div>
                                <div class="col-md-6">
                                    <strong>Duration:</strong> <?php echo $selectedService['duration_minutes']; ?> minutes
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Business Hours -->
                    <div class="card mt-4 shadow">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Business Hours</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-1"><strong>Monday - Friday:</strong> 8:00 AM - 6:00 PM</p>
                            <p class="mb-0"><strong>Saturday - Sunday:</strong> Closed</p>
                            <hr>
                            <p class="text-muted small mb-0">
                                <i class="fas fa-info-circle me-1"></i>
                                All bookings are subject to confirmation. You will receive an email notification once your booking is confirmed.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-seedling me-2"></i>Nananom Farms</h5>
                    <p>Premium Palm Oil Marketing Solutions</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>&copy; 2024 Nananom Farms. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>