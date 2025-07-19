<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>General Enquiry - Nananom Farms</title>
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
                    <li class="nav-item">
                        <a class="nav-link active" href="enquiry.php">Enquiry</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="booking.php">Book Service</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Enquiry Section -->
    <section class="py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center mb-5">
                    <h1 class="display-5 fw-bold mb-3">General Enquiry</h1>
                    <p class="lead">Have questions about our services? We're here to help!</p>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card shadow-lg">
                        <div class="card-header bg-success text-white">
                            <h3 class="mb-0"><i class="fas fa-question-circle me-2"></i>Make an Enquiry</h3>
                        </div>
                        <div class="card-body p-5">
                            <div id="alertContainer"></div>
                            
                            <form id="enquiryForm">
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
                                        <label for="subject" class="form-label">Enquiry Type *</label>
                                        <select class="form-control" id="subject" name="subject" required>
                                            <option value="">Select enquiry type</option>
                                            <option value="Service Information">Service Information</option>
                                            <option value="Pricing Inquiry">Pricing Inquiry</option>
                                            <option value="Business Partnership">Business Partnership</option>
                                            <option value="Consultation Request">Consultation Request</option>
                                            <option value="Technical Support">Technical Support</option>
                                            <option value="General Question">General Question</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="message" class="form-label">Your Enquiry *</label>
                                    <textarea class="form-control" id="message" name="message" rows="6" 
                                              placeholder="Please describe your enquiry in detail..." required></textarea>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-success btn-lg px-5">
                                        <i class="fas fa-paper-plane me-2"></i>Submit Enquiry
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- What to Expect -->
                    <div class="card mt-4 shadow">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>What to Expect</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 text-center mb-3">
                                    <i class="fas fa-clock fa-2x text-success mb-2"></i>
                                    <h6>Quick Response</h6>
                                    <p class="small text-muted">We typically respond within 24 hours</p>
                                </div>
                                <div class="col-md-4 text-center mb-3">
                                    <i class="fas fa-user-tie fa-2x text-success mb-2"></i>
                                    <h6>Expert Assistance</h6>
                                    <p class="small text-muted">Get help from our experienced team</p>
                                </div>
                                <div class="col-md-4 text-center mb-3">
                                    <i class="fas fa-handshake fa-2x text-success mb-2"></i>
                                    <h6>Personalized Solution</h6>
                                    <p class="small text-muted">Tailored advice for your specific needs</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Section -->
                    <div class="card mt-4 shadow">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-question me-2"></i>Frequently Asked Questions</h5>
                        </div>
                        <div class="card-body">
                            <div class="accordion" id="faqAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="faq1">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">
                                            What services do you offer?
                                        </button>
                                    </h2>
                                    <div id="collapse1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            We offer comprehensive palm oil marketing solutions including consultation, quality control assessment, market analysis, and supply chain optimization.
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="faq2">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
                                            How do I book a consultation?
                                        </button>
                                    </h2>
                                    <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            You can book a consultation through our booking form or contact us directly. We'll work with you to schedule a convenient time.
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="faq3">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
                                            What are your business hours?
                                        </button>
                                    </h2>
                                    <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            We operate Monday through Friday from 8:00 AM to 6:00 PM. We're closed on weekends but will respond to enquiries on the next business day.
                                        </div>
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