# Laravel Rental Management System - Render Deployment Guide

This guide will walk you through deploying your Laravel rental management system to Render using Docker.

## Prerequisites

1. **Render Account**: Sign up at [render.com](https://render.com)
2. **GitHub Repository**: Your code should be in a GitHub repository
3. **Production Credentials**: M-Pesa, SMS, and email service credentials

## Step 1: Prepare Your Repository

Ensure these files are in your repository:
- ✅ `Dockerfile` (production-ready)
- ✅ `render.yaml` (Render configuration)
- ✅ `docker-compose.yml` (local testing)
- ✅ `.docker/apache.conf` (Apache configuration)
- ✅ `.docker/supervisord.conf` (Process management)
- ✅ `.docker/start.sh` (Startup script)
- ✅ `.env.render.example` (Environment variables template)

## Step 2: Test Locally (Optional but Recommended)

```bash
# Build and test the Docker container locally
docker-compose up --build

# Access your app at http://localhost:8000
# Access phpMyAdmin at http://localhost:8080
```

## Step 3: Deploy to Render

### Option A: Using render.yaml (Recommended)

1. **Connect Repository**:
   - Go to Render Dashboard
   - Click "New" → "Blueprint"
   - Connect your GitHub repository
   - Render will automatically detect `render.yaml`

2. **Configure Environment Variables**:
   The following variables need to be set manually in Render dashboard:

   **Email Configuration**:
   ```
   MAIL_USERNAME=your-email@gmail.com
   MAIL_PASSWORD=your-app-password
   MAIL_FROM_ADDRESS=your-email@gmail.com
   ```

   **M-Pesa Production**:
   ```
   MPESA_CONSUMER_KEY=your_production_consumer_key
   MPESA_CONSUMER_SECRET=your_production_consumer_secret
   MPESA_SHORTCODE=your_production_shortcode
   MPESA_PASSKEY=your_production_passkey
   MPESA_CALLBACK_URL=https://your-app-name.onrender.com/api/mpesa/callback
   ```

   **SMS Configuration**:
   ```
   SMS_API_KEY=your_production_sms_api_key
   SMS_USERNAME=your_production_sms_username
   SMS_FROM=your_sender_id
   ```

### Option B: Manual Setup

1. **Create Web Service**:
   - New → Web Service
   - Connect GitHub repository
   - Environment: Docker
   - Dockerfile Path: `Dockerfile`

2. **Create Database**:
   - New → PostgreSQL (or use external MySQL)
   - Name: `rental-mysql`

3. **Create Redis**:
   - New → Redis
   - Name: `rental-redis`

## Step 4: Configure Services

### Database Setup
- Render will automatically create database credentials
- The `render.yaml` will link these to your web service

### Redis Setup
- Redis connection will be automatically configured
- Used for caching, sessions, and queue jobs

### Environment Variables
All environment variables are defined in `render.yaml` and will be automatically set, except for the sensitive ones marked with `sync: false`.

## Step 5: Post-Deployment Setup

After successful deployment:

1. **Run Migrations**:
   ```bash
   # Render will automatically run migrations via start.sh
   # But you can also run manually if needed
   php artisan migrate --force
   ```

2. **Verify Services**:
   - Check web service logs for any errors
   - Test M-Pesa integration (use sandbox first)
   - Test SMS functionality
   - Test email notifications

3. **Set Up Monitoring**:
   - Monitor application logs in Render dashboard
   - Set up health checks if needed

## Step 6: Configure External Services

### M-Pesa Setup
1. Get production credentials from Safaricom
2. Update callback URL to: `https://your-app-name.onrender.com/api/mpesa/callback`
3. Test with small amounts first

### SMS Setup (Africa's Talking)
1. Get production API credentials
2. Configure sender ID
3. Test SMS delivery

### Email Setup
1. Configure SMTP credentials (Gmail, SendGrid, etc.)
2. Enable 2FA and use app passwords for Gmail
3. Test email delivery

## Step 7: Domain Configuration (Optional)

1. **Custom Domain**:
   - Go to your web service settings
   - Add custom domain
   - Configure DNS records

2. **SSL Certificate**:
   - Render provides free SSL certificates
   - Automatically configured for custom domains

## Troubleshooting

### Common Issues

1. **Build Failures**:
   - Check Dockerfile syntax
   - Ensure all dependencies are available
   - Review build logs in Render dashboard

2. **Database Connection Issues**:
   - Verify database service is running
   - Check environment variable configuration
   - Review database logs

3. **Queue Jobs Not Processing**:
   - Supervisor is configured to run queue workers
   - Check supervisor logs: `/var/log/supervisor/worker.log`

4. **File Permissions**:
   - Storage and cache directories are automatically configured
   - Check startup script logs if issues persist

### Useful Commands

```bash
# View application logs
tail -f /var/log/supervisor/apache2.out.log

# Check queue workers
tail -f /var/log/supervisor/worker.log

# Check scheduled tasks
tail -f /var/log/supervisor/schedule.log

# Clear caches (if needed)
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## Security Considerations

1. **Environment Variables**: Never commit sensitive data to repository
2. **Database**: Use strong passwords and restrict access
3. **API Keys**: Rotate keys regularly
4. **HTTPS**: Always use HTTPS in production
5. **Firewall**: Configure appropriate security groups

## Monitoring and Maintenance

1. **Logs**: Regularly check application and service logs
2. **Backups**: Set up database backups
3. **Updates**: Keep dependencies updated
4. **Performance**: Monitor resource usage and scale as needed

## Cost Optimization

1. **Starter Plan**: Good for development and small applications
2. **Scaling**: Monitor usage and upgrade plans as needed
3. **Database**: Consider external database providers for better pricing
4. **CDN**: Use CDN for static assets if needed

## Support

- **Render Documentation**: [render.com/docs](https://render.com/docs)
- **Laravel Documentation**: [laravel.com/docs](https://laravel.com/docs)
- **Application Logs**: Check Render dashboard for detailed logs

---

Your Laravel rental management system is now ready for production deployment on Render! 🚀
