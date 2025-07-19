// Main JavaScript for Nananom Farms

document.addEventListener('DOMContentLoaded', function() {
    // Load services on homepage
    if (document.getElementById('services-container')) {
        loadServices();
    }

    // Form submissions
    setupFormHandlers();

    // Smooth scrolling for anchor links
    setupSmoothScrolling();
});

// Load services from backend
async function loadServices() {
    try {
        const response = await fetch('api/get_services.php');
        const services = await response.json();
        
        const container = document.getElementById('services-container');
        if (container && services.length > 0) {
            container.innerHTML = services.map(service => `
                <div class="col-lg-6 mb-4">
                    <div class="service-card card h-100">
                        <div class="card-header">
                            <h4 class="mb-0">${service.name}</h4>
                        </div>
                        <div class="card-body">
                            <p class="card-text">${service.description}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="service-price">$${parseFloat(service.price).toFixed(2)}</span>
                                <span class="text-muted">${service.duration_minutes} mins</span>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent">
                            <a href="booking.php?service=${service.id}" class="btn btn-success w-100">Book Now</a>
                        </div>
                    </div>
                </div>
            `).join('');
        }
    } catch (error) {
        console.error('Error loading services:', error);
    }
}

// Setup form handlers
function setupFormHandlers() {
    // Contact form
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            await handleFormSubmission(this, 'api/contact.php');
        });
    }

    // Enquiry form
    const enquiryForm = document.getElementById('enquiryForm');
    if (enquiryForm) {
        enquiryForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            await handleFormSubmission(this, 'api/enquiry.php');
        });
    }

    // Booking form
    const bookingForm = document.getElementById('bookingForm');
    if (bookingForm) {
        bookingForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            await handleFormSubmission(this, 'api/booking.php');
        });

        // Date change handler for available slots
        const dateInput = document.getElementById('booking_date');
        if (dateInput) {
            dateInput.addEventListener('change', loadAvailableSlots);
        }
    }
}

// Handle form submissions
async function handleFormSubmission(form, endpoint) {
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.innerHTML = '<span class="loading"></span> Sending...';
    submitBtn.disabled = true;

    try {
        const formData = new FormData(form);
        const response = await fetch(endpoint, {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            showAlert('success', result.message || 'Message sent successfully!');
            form.reset();
        } else {
            showAlert('danger', result.message || 'An error occurred. Please try again.');
        }
    } catch (error) {
        console.error('Form submission error:', error);
        showAlert('danger', 'Network error. Please check your connection and try again.');
    } finally {
        // Restore button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
}

// Load available time slots
async function loadAvailableSlots() {
    const dateInput = document.getElementById('booking_date');
    const timeSelect = document.getElementById('booking_time');
    
    if (!dateInput.value || !timeSelect) return;

    try {
        const response = await fetch(`api/get_available_slots.php?date=${dateInput.value}`);
        const slots = await response.json();
        
        timeSelect.innerHTML = '<option value="">Select a time</option>';
        slots.forEach(slot => {
            timeSelect.innerHTML += `<option value="${slot}">${slot}</option>`;
        });
    } catch (error) {
        console.error('Error loading available slots:', error);
    }
}

// Show alert messages
function showAlert(type, message) {
    const alertContainer = document.getElementById('alertContainer') || document.body;
    
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    if (alertContainer === document.body) {
        alert.style.position = 'fixed';
        alert.style.top = '20px';
        alert.style.right = '20px';
        alert.style.zIndex = '9999';
        alert.style.maxWidth = '400px';
    }
    
    alertContainer.appendChild(alert);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}

// Setup smooth scrolling
function setupSmoothScrolling() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Utility functions
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

// Admin panel specific functions
if (window.location.pathname.includes('/admin/')) {
    
    // Dashboard stats loading
    async function loadDashboardStats() {
        try {
            const response = await fetch('../api/dashboard_stats.php');
            const stats = await response.json();
            
            // Update stat cards
            document.getElementById('totalBookings').textContent = stats.bookings.total_bookings;
            document.getElementById('pendingBookings').textContent = stats.bookings.pending_bookings;
            document.getElementById('totalEnquiries').textContent = stats.enquiries.total_enquiries;
            document.getElementById('newEnquiries').textContent = stats.enquiries.new_enquiries;
        } catch (error) {
            console.error('Error loading dashboard stats:', error);
        }
    }

    // Status update functions
    async function updateStatus(type, id, status) {
        try {
            const response = await fetch('../api/update_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ type, id, status })
            });

            const result = await response.json();
            if (result.success) {
                showAlert('success', 'Status updated successfully');
                // Reload the page or update the UI
                location.reload();
            } else {
                showAlert('danger', result.message || 'Failed to update status');
            }
        } catch (error) {
            console.error('Status update error:', error);
            showAlert('danger', 'Network error occurred');
        }
    }

    // Load dashboard stats if on dashboard page
    if (document.getElementById('totalBookings')) {
        loadDashboardStats();
    }
}