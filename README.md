# Nananom Farms - Palm Oil Marketing Management System

A full-stack web-based palm oil marketing management system for Nananom Farms, featuring service booking, customer enquiries, contact management, and a comprehensive admin panel.

## 🌟 Features

### Frontend (Customer-Facing)
- **Modern Responsive Design** - Built with Bootstrap 5 and custom CSS
- **Service Booking System** - Calendar integration with available time slots
- **General Enquiry Form** - Categorized enquiry types with validation
- **Contact Form** - Direct contact with company information
- **Service Showcase** - Dynamic service listing with pricing
- **Smooth User Experience** - AJAX form submissions and animations

### Backend (Admin Panel)
- **Dashboard** - Real-time statistics and recent activity overview
- **Booking Management** - View, manage, and update booking statuses
- **Service Management** - CRUD operations for services
- **Enquiry Management** - Handle and track customer enquiries
- **Contact Message Management** - Manage contact form submissions
- **User Management** - Admin and support agent roles
- **Authentication System** - Secure login/logout functionality

### Technical Features
- **Modular PHP Architecture** - Clean, maintainable codebase
- **MySQL Database** - Normalized database design
- **RESTful API Endpoints** - JSON-based API for frontend communication
- **Client-side Validation** - Real-time form validation
- **Server-side Security** - Input sanitization and SQL injection prevention
- **Responsive Design** - Works on desktop, tablet, and mobile devices

## 🛠️ Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript (ES6+), Bootstrap 5
- **Backend**: PHP 8.0+, MySQL 5.7+
- **Architecture**: MVC Pattern with namespaced classes
- **Icons**: Font Awesome 6
- **Development**: XAMPP/WAMP for local development

## 📋 Requirements

- PHP 8.0 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (for dependency management)
- Modern web browser

## 🚀 Installation & Setup

### 1. Clone the Repository
```bash
git clone https://github.com/your-username/nananom-farms.git
cd nananom-farms
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Database Setup
1. Create a new MySQL database named `nananom_farms`
2. Import the database schema:
```bash
mysql -u root -p nananom_farms < database/schema.sql
```

### 4. Configure Database Connection
Update the database configuration in `backend/config/database.php`:
```php
private $host = 'localhost';
private $dbname = 'nananom_farms';
private $username = 'your_username';
private $password = 'your_password';
```

### 5. Start Local Server
If using XAMPP/WAMP, place the project in the `htdocs` directory and access via:
```
http://localhost/nananom-farms/
```

For PHP built-in server:
```bash
php -S localhost:8000
```

## 🔐 Default Admin Credentials

- **Username**: admin
- **Password**: admin123

**Important**: Change these credentials immediately after first login in a production environment.

## 📁 Project Structure

```
nananom-farms/
├── admin/                  # Admin panel pages
│   ├── index.php          # Dashboard
│   ├── login.php          # Admin login
│   ├── logout.php         # Logout functionality
│   └── ...
├── api/                   # RESTful API endpoints
│   ├── booking.php        # Booking submissions
│   ├── contact.php        # Contact form
│   ├── enquiry.php        # General enquiries
│   └── ...
├── assets/                # Static assets
│   ├── css/               # Custom stylesheets
│   └── js/                # JavaScript files
├── backend/               # Backend PHP classes
│   ├── config/            # Configuration files
│   └── src/               # Source classes
├── database/              # Database files
│   └── schema.sql         # Database schema
├── index.php              # Homepage
├── booking.php            # Service booking page
├── contact.php            # Contact page
├── enquiry.php            # General enquiry page
└── composer.json          # PHP dependencies
```

## 🔧 Key Components

### Backend Classes
- **Auth** - Authentication and session management
- **BookingManager** - Service booking operations
- **ServiceManager** - Service CRUD operations
- **EnquiryManager** - Enquiry and contact management
- **Database** - Database connection and configuration

### API Endpoints
- `GET /api/get_services.php` - Fetch active services
- `POST /api/booking.php` - Submit service booking
- `POST /api/contact.php` - Submit contact message
- `POST /api/enquiry.php` - Submit general enquiry
- `GET /api/get_available_slots.php` - Get available time slots

## 🎨 Customization

### Styling
- Main styles: `assets/css/style.css`
- Color scheme can be modified via CSS custom properties
- Bootstrap classes for rapid styling changes

### Database
- Add new fields to existing tables
- Extend classes to support additional functionality
- Modify API endpoints for new features

### Business Logic
- Service types and pricing in Services table
- Business hours in Settings table
- Email templates can be added for notifications

## 🔒 Security Features

- SQL injection prevention using prepared statements
- XSS protection through input sanitization
- CSRF protection for forms
- Session-based authentication
- Role-based access control
- Input validation on both client and server side

## 📱 Responsive Design

The system is fully responsive and works on:
- Desktop computers (1200px+)
- Tablets (768px - 1199px)
- Mobile phones (< 768px)

## 🚀 Deployment

### Production Deployment
1. Upload files to web server
2. Configure database connection for production
3. Set up SSL certificate for HTTPS
4. Configure email settings for notifications
5. Set proper file permissions
6. Update admin credentials

### Environment Considerations
- Enable error logging in production
- Disable debug modes
- Configure proper backup procedures
- Set up monitoring and analytics

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📞 Support

For support and questions:
- Email: info@nananom-farms.com
- Phone: +233 20 123 4567
- Business Hours: Monday - Friday, 8:00 AM - 6:00 PM

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- Bootstrap team for the excellent CSS framework
- Font Awesome for the comprehensive icon library
- PHP community for excellent documentation and resources

---

**Built with ❤️ for Nananom Farms - Transforming Palm Oil Marketing Management**