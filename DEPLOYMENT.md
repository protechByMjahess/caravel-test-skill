# Laravel Project Deployment Guide

## 🚀 Server Deployment

### Prerequisites
- Ubuntu 20.04+ or CentOS 8+ server
- Root or sudo access
- Domain name pointing to server IP

### 1. Server Setup
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install nginx mysql-server php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-bcmath php8.2-gd php8.2-cli php8.2-common php8.2-opcache php8.2-readline php8.2-sqlite3 php8.2-tokenizer php8.2-xmlwriter php8.2-xsl php8.2-json php8.2-fileinfo php8.2-ctype php8.2-dom php8.2-filter php8.2-hash php8.2-iconv php8.2-mbstring php8.2-pcre php8.2-pdo php8.2-session php8.2-simplexml php8.2-spl php8.2-tokenizer php8.2-xml php8.2-xmlreader php8.2-xmlwriter php8.2-zip -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js and npm
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs

# Install Redis (optional)
sudo apt install redis-server -y
```

### 2. Database Setup
```bash
# Secure MySQL installation
sudo mysql_secure_installation

# Create database and user
sudo mysql -u root -p
CREATE DATABASE laravel_app;
CREATE USER 'laravel_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON laravel_app.* TO 'laravel_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3. Application Deployment
```bash
# Create application directory
sudo mkdir -p /var/www/laravel-app
sudo chown -R www-data:www-data /var/www/laravel-app

# Clone repository
cd /var/www/laravel-app
sudo -u www-data git clone https://github.com/yourusername/your-repo.git .

# Install dependencies
sudo -u www-data composer install --no-dev --optimize-autoloader
sudo -u www-data npm install
sudo -u www-data npm run build

# Set permissions
sudo chown -R www-data:www-data /var/www/laravel-app
sudo chmod -R 755 /var/www/laravel-app
sudo chmod -R 775 /var/www/laravel-app/storage
sudo chmod -R 775 /var/www/laravel-app/bootstrap/cache
```

### 4. Environment Configuration
```bash
# Copy environment file
sudo -u www-data cp .env.example .env

# Generate application key
sudo -u www-data php artisan key:generate

# Configure database
sudo -u www-data nano .env
```

**Required .env settings:**
```
APP_NAME="Laravel App"
APP_ENV=production
APP_KEY=base64:your-generated-key
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_app
DB_USERNAME=laravel_user
DB_PASSWORD=strong_password

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 5. Database Migration
```bash
# Run migrations
sudo -u www-data php artisan migrate --force

# Seed database (optional)
sudo -u www-data php artisan db:seed --force

# Clear and cache config
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
```

### 6. Nginx Configuration
```bash
# Create Nginx configuration
sudo nano /etc/nginx/sites-available/laravel-app
```

**Nginx configuration:**
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/laravel-app/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/laravel-app /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 7. SSL Certificate (Let's Encrypt)
```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx -y

# Obtain SSL certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Auto-renewal
sudo crontab -e
# Add: 0 12 * * * /usr/bin/certbot renew --quiet
```

### 8. Queue Worker Setup
```bash
# Create systemd service
sudo nano /etc/systemd/system/laravel-worker.service
```

**Service configuration:**
```ini
[Unit]
Description=Laravel Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/laravel-app/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
WorkingDirectory=/var/www/laravel-app

[Install]
WantedBy=multi-user.target
```

```bash
# Enable and start service
sudo systemctl enable laravel-worker
sudo systemctl start laravel-worker
```

---

## 🔄 CI/CD Pipeline Setup

### GitHub Actions Workflow
Create `.github/workflows/deploy.yml`:

```yaml
name: Deploy to Production

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.2'
        extensions: mbstring, xml, ctype, iconv, intl, pdo_mysql, dom, filter, gd, json, pdo, tokenizer
        
    - name: Install Composer dependencies
      run: composer install --no-dev --optimize-autoloader
      
    - name: Install NPM dependencies
      run: npm ci
      
    - name: Build assets
      run: npm run build
      
    - name: Run tests
      run: php artisan test
      
    - name: Deploy to server
      uses: appleboy/ssh-action@v0.1.5
      with:
        host: ${{ secrets.HOST }}
        username: ${{ secrets.USERNAME }}
        key: ${{ secrets.SSH_KEY }}
        script: |
          cd /var/www/laravel-app
          git pull origin main
          composer install --no-dev --optimize-autoloader
          npm ci
          npm run build
          php artisan migrate --force
          php artisan config:cache
          php artisan route:cache
          php artisan view:cache
          sudo systemctl reload nginx
          sudo systemctl restart laravel-worker
```

### Required GitHub Secrets
- `HOST`: Your server IP address
- `USERNAME`: Server username (usually `root` or `ubuntu`)
- `SSH_KEY`: Private SSH key for server access

---

## 🔒 Security Configuration

### 1. Firewall Setup
```bash
# Install UFW
sudo apt install ufw -y

# Configure firewall
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw allow 3306/tcp  # MySQL (if needed externally)
sudo ufw enable
```

### 2. Fail2Ban Installation
```bash
# Install Fail2Ban
sudo apt install fail2ban -y

# Configure for Nginx
sudo nano /etc/fail2ban/jail.local
```

**Fail2Ban configuration:**
```ini
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 3

[nginx-http-auth]
enabled = true
port = http,https
logpath = /var/log/nginx/error.log

[nginx-limit-req]
enabled = true
port = http,https
logpath = /var/log/nginx/error.log
maxretry = 10
```

```bash
# Start Fail2Ban
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

### 3. PHP Security
```bash
# Edit PHP configuration
sudo nano /etc/php/8.2/fpm/php.ini
```

**Key PHP security settings:**
```ini
expose_php = Off
allow_url_fopen = Off
allow_url_include = Off
display_errors = Off
log_errors = On
error_log = /var/log/php_errors.log
max_execution_time = 30
max_input_time = 30
memory_limit = 256M
post_max_size = 20M
upload_max_filesize = 20M
max_file_uploads = 20
```

### 4. MySQL Security
```bash
# Secure MySQL
sudo mysql_secure_installation

# Create MySQL configuration
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
```

**MySQL security settings:**
```ini
[mysqld]
bind-address = 127.0.0.1
local-infile = 0
skip-networking = 0
max_connections = 100
max_user_connections = 50
```

### 5. Environment Variables Security
```bash
# Set proper permissions
sudo chmod 600 /var/www/laravel-app/.env
sudo chown www-data:www-data /var/www/laravel-app/.env

# Create environment backup
sudo cp /var/www/laravel-app/.env /var/www/laravel-app/.env.backup
```

---

## 📊 Monitoring & Logging

### 1. Log Rotation
```bash
# Configure logrotate
sudo nano /etc/logrotate.d/laravel
```

**Logrotate configuration:**
```
/var/www/laravel-app/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 644 www-data www-data
    postrotate
        sudo systemctl reload php8.2-fpm
    endscript
}
```

### 2. System Monitoring
```bash
# Install monitoring tools
sudo apt install htop iotop nethogs -y

# Install Prometheus Node Exporter (optional)
wget https://github.com/prometheus/node_exporter/releases/download/v1.6.1/node_exporter-1.6.1.linux-amd64.tar.gz
tar xvfz node_exporter-1.6.1.linux-amd64.tar.gz
sudo mv node_exporter-1.6.1.linux-amd64/node_exporter /usr/local/bin/
sudo useradd --no-create-home --shell /bin/false node_exporter
sudo chown node_exporter:node_exporter /usr/local/bin/node_exporter
```

### 3. Application Monitoring
```bash
# Install Laravel Telescope (optional)
sudo -u www-data composer require laravel/telescope --dev
sudo -u www-data php artisan telescope:install
sudo -u www-data php artisan migrate

# Configure Laravel Pail for log monitoring
sudo -u www-data php artisan pail
```

### 4. Health Check Endpoint
Create `routes/web.php` health check:
```php
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'database' => DB::connection()->getPdo() ? 'connected' : 'disconnected',
        'cache' => Cache::store()->getStore() ? 'connected' : 'disconnected'
    ]);
});
```

---

## 💾 Backup Strategy

### 1. Database Backup
```bash
# Create backup script
sudo nano /usr/local/bin/backup-db.sh
```

**Database backup script:**
```bash
#!/bin/bash
BACKUP_DIR="/var/backups/laravel"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="laravel_app"
DB_USER="laravel_user"
DB_PASS="strong_password"

mkdir -p $BACKUP_DIR
mysqldump -u$DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/db_backup_$DATE.sql
gzip $BACKUP_DIR/db_backup_$DATE.sql

# Keep only last 7 days
find $BACKUP_DIR -name "db_backup_*.sql.gz" -mtime +7 -delete
```

```bash
# Make executable
sudo chmod +x /usr/local/bin/backup-db.sh

# Schedule daily backup
sudo crontab -e
# Add: 0 2 * * * /usr/local/bin/backup-db.sh
```

### 2. Application Backup
```bash
# Create application backup script
sudo nano /usr/local/bin/backup-app.sh
```

**Application backup script:**
```bash
#!/bin/bash
BACKUP_DIR="/var/backups/laravel"
APP_DIR="/var/www/laravel-app"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR
tar -czf $BACKUP_DIR/app_backup_$DATE.tar.gz -C $APP_DIR .

# Keep only last 7 days
find $BACKUP_DIR -name "app_backup_*.tar.gz" -mtime +7 -delete
```

### 3. Automated Backup to Cloud
```bash
# Install AWS CLI (for S3 backup)
sudo apt install awscli -y

# Configure AWS credentials
aws configure

# Create cloud backup script
sudo nano /usr/local/bin/backup-to-s3.sh
```

**S3 backup script:**
```bash
#!/bin/bash
BACKUP_DIR="/var/backups/laravel"
S3_BUCKET="your-backup-bucket"
DATE=$(date +%Y%m%d_%H%M%S)

# Upload to S3
aws s3 cp $BACKUP_DIR/db_backup_$DATE.sql.gz s3://$S3_BUCKET/database/
aws s3 cp $BACKUP_DIR/app_backup_$DATE.tar.gz s3://$S3_BUCKET/application/
```

---

## 🚨 Maintenance Commands

### Daily Maintenance
```bash
# Clear caches
sudo -u www-data php artisan cache:clear
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache

# Check queue status
sudo systemctl status laravel-worker

# Monitor disk space
df -h

# Check system resources
htop
```

### Weekly Maintenance
```bash
# Update system packages
sudo apt update && sudo apt upgrade -y

# Clean old logs
sudo journalctl --vacuum-time=7d

# Check application logs
sudo tail -f /var/www/laravel-app/storage/logs/laravel.log
```

### Emergency Procedures
```bash
# Restart services
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
sudo systemctl restart laravel-worker

# Rollback deployment
cd /var/www/laravel-app
git log --oneline -10
git reset --hard <commit-hash>

# Restore from backup
cd /var/backups/laravel
gunzip db_backup_YYYYMMDD_HHMMSS.sql.gz
mysql -u laravel_user -p laravel_app < db_backup_YYYYMMDD_HHMMSS.sql
```

---

## 📋 Quick Checklist

### Pre-deployment
- [ ] Server hardened with firewall
- [ ] SSL certificate installed
- [ ] Database secured
- [ ] Environment variables configured
- [ ] Backup strategy implemented

### Post-deployment
- [ ] Application accessible via HTTPS
- [ ] Queue worker running
- [ ] Monitoring configured
- [ ] Logs being collected
- [ ] Backups scheduled
- [ ] Health check endpoint working

### Security Checklist
- [ ] Firewall configured
- [ ] Fail2Ban installed
- [ ] PHP security settings applied
- [ ] MySQL secured
- [ ] File permissions set correctly
- [ ] Environment file protected
- [ ] Regular security updates scheduled

---

**Note:** Replace placeholder values (yourdomain.com, strong_password, etc.) with your actual configuration values before deployment.
