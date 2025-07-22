# Nananom Farms - Palm Oil Marketing Management System (File-Based)

A full-stack web-based palm oil marketing management system built without database dependencies, using JSON file storage with mysqli-style patterns.

## 🌟 Features

- **No Database Required** - Uses JSON file storage with mysqli-style API
- **Customizable Authentication** - Easy setup with your own admin credentials
- **Service Booking System** - Calendar integration and time slot management
- **Customer Enquiries** - General enquiries and contact message management
- **Admin Dashboard** - Complete management interface
- **Responsive Design** - Modern, mobile-friendly UI
- **Security Features** - Password requirements, login attempt limits, session management

## 🚀 Quick Setup

### 1. Initial Setup
1. Clone or download the project files
2. Navigate to your web server directory
3. Run the setup script: `http://yoursite.com/setup.php`
4. Configure your admin account and security settings

### 2. Custom Authentication Configuration
The setup script allows you to configure:
- Admin username and password
- Password requirements (length, uppercase, numbers, special chars)
- Session timeout duration
- Maximum login attempts before account lock

### 3. File Structure
```
project/
├── setup.php                 # Initial system setup
├── index.php                 # Main website
├── booking.php               # Service booking page
├── contact.php               # Contact form
├── enquiry.php               # General enquiries
├── admin/                    # Admin panel
│   ├── login.php            # Admin login
│   ├── index.php            # Admin dashboard
│   └── logout.php           # Logout functionality
├── api/                      # API endpoints
│   ├── booking.php          # Booking submissions
│   ├── contact.php          # Contact submissions
│   ├── enquiry.php          # Enquiry submissions
│   ├── get_services.php     # Service data
│   └── get_available_slots.php # Available time slots
├── backend/                  # Backend logic
│   ├── config/
│   │   └── storage.php      # File storage class
│   └── src/                 # Business logic classes
│       ├── Auth.php         # Authentication management
│       ├── BookingManager.php # Booking operations
│       ├── ServiceManager.php # Service operations
│       └── EnquiryManager.php # Enquiry operations
├── data/                     # JSON data files (auto-created)
│   ├── users.json           # User accounts
│   ├── services.json        # Available services
│   ├── bookings.json        # Customer bookings
│   ├── enquiries.json       # General enquiries
│   ├── contact_messages.json # Contact messages
│   └── auth_config.json     # Authentication settings
└── assets/                   # CSS, JS, images
```

## 🔧 Configuration

### Authentication Settings
After initial setup, you can modify authentication by editing `data/auth_config.json`:

```json
{
    "default_admin": {
        "username": "your_admin_username",
        "password": "your_password",
        "email": "admin@yoursite.com",
        "first_name": "Your",
        "last_name": "Name"
    },
    "password_requirements": {
        "min_length": 8,
        "require_uppercase": true,
        "require_numbers": true,
        "require_special_chars": false
    },
    "session_timeout": 3600,
    "max_login_attempts": 5
}
```

### Adding New Services
Services are automatically initialized with defaults. To add more, you can:
1. Use the admin panel (when implemented)
2. Manually edit `data/services.json`

Example service structure:
```json
{
    "id": 5,
    "name": "New Service",
    "description": "Service description",
    "price": 100.00,
    "duration_minutes": 60,
    "status": "active",
    "created_at": "2024-01-01 12:00:00",
    "updated_at": "2024-01-01 12:00:00"
}
```

## 🎯 Default Services

The system comes with pre-configured services:
1. **Palm Oil Consultation** - $150, 60 minutes
2. **Quality Testing** - $200, 90 minutes  
3. **Distribution Planning** - $300, 120 minutes
4. **Market Analysis** - $250, 90 minutes

## 📱 Usage

### Customer Features
- Browse available services
- Book appointments with time slot selection
- Submit general enquiries
- Contact form submissions
- Responsive mobile interface

### Admin Features
- Dashboard with statistics
- Manage bookings (view, update status, delete)
- Manage enquiries and contact messages
- User management
- Service management
- Authentication configuration

### Available Time Slots
Default booking slots: 9:00 AM, 10:00 AM, 11:00 AM, 12:00 PM, 2:00 PM, 3:00 PM, 4:00 PM, 5:00 PM

## 🔒 Security Features

- **Password Hashing** - Uses PHP's `password_hash()` with bcrypt
- **Session Management** - Configurable timeout and security
- **Login Attempt Limiting** - Prevents brute force attacks
- **Input Sanitization** - All user inputs are sanitized
- **XSS Protection** - Output escaping for security
- **CSRF Protection** - Can be added as needed

## 🗃️ Data Storage

### File Storage Class
The `FileStorage` class provides mysqli-style methods:
- `insert($table, $data)` - Add new records
- `select($table, $conditions, $orderBy, $limit, $offset)` - Query records
- `selectOne($table, $conditions)` - Get single record
- `update($table, $conditions, $data)` - Update records
- `delete($table, $conditions)` - Delete records
- `join($primary, $join, $primaryKey, $foreignKey)` - Join tables

### Data Backup
- Automatic backup functionality available
- Manual backups stored in `data/backups/`
- JSON format for easy restoration

## 🔄 Migration from Database

If you have an existing database version:
1. Export your data to JSON format
2. Place files in the `data/` directory
3. Ensure proper field mapping
4. Test functionality

## ⚡ Performance

- **Fast File Operations** - Optimized JSON read/write
- **Memory Efficient** - Loads only needed data
- **Caching Ready** - Can add file caching as needed
- **Scalable** - Suitable for small to medium datasets

## 🛠️ Maintenance

### Regular Tasks
1. **Backup Data** - Regular backup of `data/` directory
2. **Clear Logs** - Monitor and clear any log files
3. **Update Services** - Keep service information current
4. **Security Updates** - Keep PHP and server updated

### Troubleshooting
- **File Permissions** - Ensure `data/` directory is writable
- **JSON Errors** - Validate JSON files if issues occur
- **Session Issues** - Check PHP session configuration
- **Login Problems** - Use `setup.php` to reset admin account

## 📋 Requirements

- **PHP 8.0+** with JSON extension
- **Web Server** (Apache/Nginx)
- **Write Permissions** for data directory
- **Modern Browser** for admin interface

## 🎨 Customization

### Styling
- Bootstrap 5 for responsive design
- Custom CSS in embedded styles
- Font Awesome icons
- Easy color scheme modification

### Functionality
- Modular class structure
- Easy to extend with new features
- API-ready for integrations
- Clean separation of concerns

## 📞 Support

For issues or customization:
1. Check file permissions
2. Validate JSON data files
3. Review error logs
4. Reset via `setup.php` if needed

## 🔮 Future Enhancements

- Email notifications integration
- Calendar export functionality
- Advanced reporting features
- Multi-language support
- API authentication
- Advanced admin features

---

**Note**: This system is designed for environments where database setup is not possible or desired. For high-traffic sites, consider migrating to a database solution.