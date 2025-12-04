# 🚀 Online Medical System - Deployment Guide

## 📋 Pre-Deployment Checklist

### 1. Choose a Hosting Provider
**Recommended hosting providers for PHP/MySQL projects:**
- **Shared Hosting:** Hostinger, Bluehost, SiteGround
- **VPS/Cloud:** DigitalOcean, Linode, AWS EC2
- **Free Options:** 000webhost, InfinityFree (for portfolio demos)

### 2. Hosting Requirements
- **PHP:** Version 7.4 or higher
- **MySQL:** Version 5.7 or higher
- **Apache/Nginx:** Web server with mod_rewrite enabled
- **SSL Certificate:** For HTTPS (recommended)
- **Storage:** At least 500MB for files and database

## 🔧 Deployment Steps

### Step 1: Prepare Your Files
1. **Update config.php:**
   ```php
   define('ENVIRONMENT', 'production');
   define('DB_SERVER', 'your-hosting-mysql-server');
   define('DB_USERNAME', 'your-db-username');
   define('DB_PASSWORD', 'your-db-password');
   define('DB_NAME', 'your-database-name');
   define('BASE_URL', 'https://yourdomain.com/');
   ```

2. **Create uploads directory:**
   ```bash
   mkdir uploads
   chmod 755 uploads
   ```

### Step 2: Upload Files
1. **Via FTP/SFTP:**
   - Use FileZilla, WinSCP, or hosting file manager
   - Upload all files to public_html or www directory
   - Maintain folder structure

2. **Via Git (if supported):**
   ```bash
   git clone https://github.com/yourusername/online-medical-system.git
   ```

### Step 3: Database Setup
1. **Create Database:**
   - Access hosting control panel (cPanel/Plesk)
   - Create new MySQL database
   - Create database user with full privileges

2. **Import Schema:**
   - Use phpMyAdmin or MySQL command line
   - Import `database_schema.sql`
   - Verify all tables are created

3. **Test Connection:**
   - Visit your site
   - Check if database connection works

### Step 4: Configure Domain & SSL
1. **Domain Setup:**
   - Point domain to hosting server
   - Update DNS records if needed

2. **SSL Certificate:**
   - Enable SSL in hosting control panel
   - Update .htaccess to force HTTPS

### Step 5: Final Testing
- [ ] Homepage loads correctly
- [ ] User registration works
- [ ] Login functionality works
- [ ] Appointment booking works
- [ ] Admin panel accessible
- [ ] PDF generation works
- [ ] Email notifications work (if configured)

## 🔒 Security Considerations

### Production Security Checklist
- [ ] Change default admin password
- [ ] Update all database credentials
- [ ] Enable HTTPS/SSL
- [ ] Set proper file permissions (644 for files, 755 for directories)
- [ ] Hide sensitive files via .htaccess
- [ ] Enable error logging instead of display
- [ ] Regular backups configured
- [ ] Update PHP to latest stable version

### File Permissions
```bash
# Set correct permissions
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod 600 config.php
chmod 600 includes/db_connect.php
```

## 📱 Portfolio Integration

### 1. Demo Credentials
Create demo accounts for portfolio visitors:

**Admin Demo:**
- Username: demo_admin
- Password: demo123

**Doctor Demo:**
- Username: demo_doctor
- Password: demo123

**Patient Demo:**
- Username: demo_patient
- Password: demo123

### 2. Portfolio Description
```markdown
## 🏥 Online Medical System (MediBook)

**Live Demo:** [https://yourdomain.com](https://yourdomain.com)
**GitHub:** [Repository Link](https://github.com/yourusername/online-medical-system)

### Technologies Used
- **Frontend:** HTML5, CSS3, JavaScript, Bootstrap
- **Backend:** PHP 8.0, MySQL
- **Libraries:** Dompdf (PDF generation), Font Awesome
- **Security:** Password hashing, SQL injection prevention, XSS protection

### Key Features
- Role-based authentication (Patient, Doctor, Admin)
- Appointment booking system
- Medical records management
- PDF report generation
- Responsive design
- Admin dashboard with analytics

### Demo Accounts
- **Admin:** demo_admin / demo123
- **Doctor:** demo_doctor / demo123  
- **Patient:** demo_patient / demo123
```

## 🚨 Troubleshooting

### Common Issues & Solutions

**Database Connection Error:**
- Verify database credentials in config.php
- Check if database server is accessible
- Ensure database user has proper privileges

**File Upload Issues:**
- Check uploads directory permissions (755)
- Verify PHP upload_max_filesize setting
- Ensure disk space is available

**PDF Generation Not Working:**
- Check if dompdf directory is uploaded
- Verify PHP memory_limit (minimum 128MB)
- Check file permissions on dompdf directory

**Session Issues:**
- Verify session.save_path is writable
- Check PHP session configuration
- Clear browser cookies/cache

## 📞 Support

For deployment assistance:
1. Check hosting provider documentation
2. Contact hosting support for server-specific issues
3. Review error logs for detailed error information

---

**Good luck with your deployment! 🎉**