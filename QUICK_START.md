# 🚀 Quick Start Guide

## Immediate Setup (3 steps)

### Step 1: Upload Files
Upload all project files to your web server directory.

### Step 2: Set Permissions
```bash
chmod 755 /path/to/your/project
chmod 777 data/ (will be created automatically)
```

### Step 3: Configure Authentication
Visit: `http://yoursite.com/setup.php`

**That's it!** Your system is ready to use.

---

## 🔧 Custom Authentication Setup

### Default Login (if you skip setup.php):
- **Username:** admin
- **Password:** admin123

### Custom Setup Options:
1. **Your Admin Credentials** - Set your own username/password
2. **Security Settings** - Configure password requirements
3. **Session Management** - Set timeout and login attempts

---

## 📋 Quick Access URLs

After setup, access these URLs:

| Function | URL |
|----------|-----|
| **Main Website** | `http://yoursite.com/` |
| **Admin Login** | `http://yoursite.com/admin/login.php` |
| **Setup Configuration** | `http://yoursite.com/setup.php` |
| **Installation Check** | `http://yoursite.com/install.php` |

---

## ⚡ Testing the System

### 1. Check Installation
Visit: `http://yoursite.com/install.php`
- Verifies all requirements are met
- Tests file storage functionality

### 2. Browse the Website
Visit: `http://yoursite.com/`
- View available services
- Test booking functionality
- Submit enquiries and contact forms

### 3. Access Admin Panel
Visit: `http://yoursite.com/admin/login.php`
- Login with your credentials
- Manage bookings and enquiries
- Configure services

---

## 🗂️ File Structure (Important)

```
your-website/
├── 📁 data/              # Auto-created JSON storage
├── 📁 admin/             # Admin panel
├── 📁 api/               # API endpoints
├── 📁 backend/           # Core logic
├── 📄 setup.php          # Initial configuration
├── 📄 index.php          # Main website
└── 📄 install.php        # System check
```

---

## 🔒 Security Notes

- Change default admin credentials immediately
- Keep the `data/` directory secure (not web accessible if possible)
- Regular backups of the `data/` folder
- Update PHP to latest version

---

## 🆘 Troubleshooting

### Common Issues:

**"Permission Denied" Error:**
```bash
chmod 777 data/
```

**"Setup Already Completed":**
Delete `data/setup_status.json` to re-run setup

**"File Not Found" Errors:**
Run `install.php` to check missing files

**Login Issues:**
Re-run `setup.php` to reset admin account

---

## 🎯 Next Steps

1. **Customize Services** - Edit default services in admin panel
2. **Brand the Site** - Update colors, logos, and content
3. **Email Setup** - Configure email notifications (optional)
4. **Backup Strategy** - Set up regular data backups

---

**Need Help?** Check `README_FILE_BASED.md` for detailed documentation.