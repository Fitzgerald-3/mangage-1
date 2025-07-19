<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Nananom Farms</title>
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
                        <a class="nav-link active" href="contact.php">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="enquiry.php">Enquiry</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="booking.php">Book Service</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contact Section -->
    <section class="py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center mb-5">
                    <h1 class="display-5 fw-bold mb-3">Contact Us</h1>
                    <p class="lead">Get in touch with our team. We'd love to hear from you!</p>
                </div>
            </div>

            <div class="row">
                <!-- Contact Form -->
                <div class="col-lg-8">
                    <div class="card shadow-lg">
                        <div class="card-header bg-success text-white">
                            <h3 class="mb-0"><i class="fas fa-envelope me-2"></i>Send us a Message</h3>
                        </div>
                        <div class="card-body p-5">
                            <div id="alertContainer"></div>
                            
                            <form id="contactForm">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Full Name *</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email Address *</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone" name="phone">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="subject" class="form-label">Subject *</label>
                                        <input type="text" class="form-control" id="subject" name="subject" required>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="message" class="form-label">Message *</label>
                                    <textarea class="form-control" id="message" name="message" rows="6" 
                                              placeholder="Tell us how we can help you..." required></textarea>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-success btn-lg px-5">
                                        <i class="fas fa-paper-plane me-2"></i>Send Message
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="col-lg-4">
                    <div class="card shadow mb-4">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-phone fa-2x text-success mb-3"></i>
                            <h5>Phone</h5>
                            <p class="mb-0">+233 20 123 4567</p>
                        </div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-envelope fa-2x text-success mb-3"></i>
                            <h5>Email</h5>
                            <p class="mb-0">info@nananom-farms.com</p>
                        </div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-map-marker-alt fa-2x text-success mb-3"></i>
                            <h5>Location</h5>
                            <p class="mb-0">Kumasi, Ghana</p>
                        </div>
                    </div>

                    <div class="card shadow">
                        <div class="card-body p-4">
                            <h5 class="mb-3"><i class="fas fa-clock me-2"></i>Business Hours</h5>
                            <p class="mb-1"><strong>Monday - Friday:</strong><br>8:00 AM - 6:00 PM</p>
                            <p class="mb-0"><strong>Saturday - Sunday:</strong><br>Closed</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Map Section -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-map me-2"></i>Find Us</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="embed-responsive" style="height: 300px;">
                                <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                                    <div class="text-center">
                                        <i class="fas fa-map-marked-alt fa-3x text-success mb-3"></i>
                                        <h5>Our Location</h5>
                                        <p class="text-muted">Kumasi, Ghana</p>
                                        <p class="small">Interactive map coming soon</p>
                                    </div>
                                </div>
                            </div>
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