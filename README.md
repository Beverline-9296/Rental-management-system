# 🏠 Rental Management System

A comprehensive Laravel-based rental property management system with M-Pesa integration and USSD support, designed for landlords and tenants in Kenya.

## 📋 Table of Contents

- [About](#about)
- [Features](#features)
- [Technology Stack](#technology-stack)
- [Installation](#installation)
- [Configuration](#configuration)
- [API Documentation](#api-documentation)
- [M-Pesa Integration](#m-pesa-integration)
- [USSD Integration](#ussd-integration)
- [Database Schema](#database-schema)
- [Usage](#usage)
- [User Roles](#user-roles)
- [Testing](#testing)
- [Deployment](#deployment)
- [Contributing](#contributing)
- [License](#license)

## 🎯 About

The Rental Management System is a full-featured web application that helps landlords manage their rental properties efficiently while providing tenants with easy access to their rental information and payment history. The system handles everything from property listings and tenant assignments to payment tracking and arrears calculations.

## ✨ Features

### 🏘️ Property Management
- **Multi-Property Support**: Create and manage unlimited properties
- **Unit Management**: Add multiple units per property with individual rent amounts
- **Property Images**: Upload and manage property photos
- **Location & Address**: Detailed location and address information
- **Property Types**: Support for apartments, houses, studios, etc.
- **Amenities Tracking**: JSON-based amenities storage

### 👥 Tenant Management
- **Comprehensive Profiles**: Full tenant information with emergency contacts
- **Lease Management**: Flexible tenant assignments with start/end dates
- **Role-Based Access**: Separate dashboards for landlords and tenants
- **Tenant Communications**: Built-in messaging system
- **Profile Photos**: User profile image management

### 💰 Payment & Financial Management
- **M-Pesa Integration**: Complete STK Push payment system
- **Payment Tracking**: Comprehensive payment history and status
- **Arrears Calculation**: Smart arrears calculation based on full months
- **Multiple Payment Types**: Rent, deposits, and other payment categories
- **Real-time Status**: Live payment status checking
- **Export Functionality**: Excel export for payment records

### 📱 Mobile & USSD Integration
- **USSD Support**: Mobile money integration via USSD
- **Mobile-Responsive**: Fully responsive design for mobile devices
- **Real-time Notifications**: Payment confirmations and updates

### 🔧 Maintenance & Communication
- **Maintenance Requests**: Tenant-initiated maintenance requests
- **Status Tracking**: Request status management for landlords
- **Messaging System**: Built-in communication between landlords and tenants
- **Activity Logging**: Comprehensive system activity tracking

### 📊 Dashboard & Analytics
- **Landlord Dashboard**: Property overview, financial summaries, tenant management
- **Tenant Dashboard**: Personal rental info, payment history, unit details
- **Real-time Calculations**: Dynamic rent due and arrears calculations
- **Financial Reports**: Detailed financial analytics and reporting

## 🛠️ Technology Stack

### Backend
- **Framework**: Laravel 12.x
- **PHP Version**: 8.2+
- **Authentication**: Laravel Breeze with role-based access
- **Queue System**: Database-based job queues
- **File Storage**: Laravel Storage with symlink support
- **Excel Export**: Maatwebsite/Excel package

### Frontend
- **Templates**: Blade templating engine
- **CSS Framework**: Tailwind CSS 3.x
- **JavaScript**: Alpine.js, Axios for AJAX
- **Icons**: Font Awesome integration
- **Build Tool**: Vite for asset compilation

### Database
- **Primary**: SQLite (configurable to MySQL/PostgreSQL)
- **Migrations**: 18 comprehensive migration files
- **Relationships**: Complex relational data structure
- **Indexing**: Optimized database indexes for performance

### External Integrations
- **M-Pesa API**: Safaricom STK Push integration
- **USSD Gateway**: Mobile money USSD integration
- **HTTP Client**: Laravel HTTP client for API calls

### Development Tools
- **Testing**: PHPUnit with Feature and Unit tests
- **Code Quality**: Laravel Pint for code formatting
- **Debugging**: Laravel Pail for log monitoring
- **Development**: Laravel Sail for Docker support

## 🚀 Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL
- Node.js & NPM (for asset compilation)

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/rental-management-system.git
   cd rental-management-system
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database**
   - Update `.env` file with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=rental_system
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. **Run migrations and seeders**
   ```bash
   php artisan migrate --seed
   ```

7. **Create storage symlink**
   ```bash
   php artisan storage:link
   ```

8. **Compile assets**
   ```bash
   npm run dev
   ```

9. **Start the development server**
   ```bash
   php artisan serve
   ```

The application will be available at `http://localhost:8000`

## ⚙️ Configuration

### Environment Variables

Copy `.env.example` to `.env` and configure the following:

#### Database Configuration
```env
DB_CONNECTION=sqlite  # or mysql, pgsql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rental_system
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

#### M-Pesa Configuration
```env
# M-Pesa STK Push Settings
MPESA_ENVIRONMENT=sandbox  # or production
MPESA_CONSUMER_KEY=your_consumer_key
MPESA_CONSUMER_SECRET=your_consumer_secret
MPESA_SHORTCODE=174379  # Test shortcode for sandbox
MPESA_PASSKEY=your_passkey
MPESA_CALLBACK_URL=https://yourdomain.com/api/mpesa/callback
MPESA_TIMEOUT_URL=https://yourdomain.com/api/mpesa/timeout
```

#### Queue Configuration
```env
QUEUE_CONNECTION=database
```

#### Mail Configuration
```env
MAIL_MAILER=log  # or smtp, mailgun, etc.
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
```

### File Permissions
Ensure proper permissions for storage and bootstrap/cache:
```bash
chmod -R 775 storage bootstrap/cache
```

## 📖 Usage

### Default Login Credentials
After running the seeders, you can use these default accounts:

**Landlord Account:**
- Email: `landlord@example.com`
- Password: `password`

**Tenant Account:**
- Email: `tenant@example.com`
- Password: `password`

### Getting Started
1. **For Landlords**: Log in and start by adding your properties and units
2. **For Tenants**: Log in to view your rental information and payment history
3. **Admin**: Manage user roles and system settings

## 📚 API Documentation

### Authentication
All API endpoints require authentication except for callbacks and public routes.

#### Headers Required
```http
Authorization: Bearer {token}
Content-Type: application/json
X-CSRF-TOKEN: {csrf_token}
```

### Property Management API

#### Get Available Units
```http
GET /api/properties/{property}/available-units
```
**Response:**
```json
{
  "units": [
    {
      "id": 1,
      "unit_number": "A1",
      "rent_amount": "25000.00",
      "status": "available"
    }
  ]
}
```

### M-Pesa Payment API

#### Initiate STK Push
```http
POST /api/mpesa/stk-push
```
**Request Body:**
```json
{
  "phone_number": "254708374149",
  "amount": 25000,
  "unit_id": 1,
  "payment_type": "rent"
}
```

**Response:**
```json
{
  "success": true,
  "message": "STK Push initiated successfully",
  "checkout_request_id": "ws_CO_DMZ_123456789_12345678901234567890",
  "transaction_id": 1
}
```

#### Check Payment Status
```http
POST /api/mpesa/check-status
```
**Request Body:**
```json
{
  "checkout_request_id": "ws_CO_DMZ_123456789_12345678901234567890"
}
```

**Response:**
```json
{
  "success": true,
  "status": "success",
  "receipt_number": "SBX12345678",
  "amount": 25000
}
```

### USSD Integration API

#### USSD Callback
```http
POST /api/ussd/callback
```
**Request Body:**
```json
{
  "sessionId": "session123",
  "serviceCode": "*123#",
  "phoneNumber": "254708374149",
  "text": "1*2*25000"
}
```

#### USSD Test Endpoints
```http
POST /api/ussd/test
POST /api/ussd/debug
```

### M-Pesa Callback Endpoints

#### Payment Callback
```http
POST /api/mpesa/callback
```

#### Payment Timeout
```http
POST /api/mpesa/timeout
```

## 💳 M-Pesa Integration

### Features
- **STK Push**: Initiate payments directly from the application
- **Real-time Status**: Check payment status in real-time
- **Sandbox Support**: Full sandbox environment support with smart detection
- **Production Ready**: Seamless switch to production environment
- **Callback Handling**: Automatic payment confirmation via callbacks
- **Receipt Generation**: Automatic receipt number generation

### Sandbox Configuration
For testing, use these credentials in your `.env` file:

```env
MPESA_ENVIRONMENT=sandbox
MPESA_CONSUMER_KEY=your_sandbox_consumer_key
MPESA_CONSUMER_SECRET=your_sandbox_consumer_secret
MPESA_SHORTCODE=174379
MPESA_PASSKEY=bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919
```

### Test Phone Numbers
- `254708374149` - Simulates successful payment
- `254711111111` - Simulates successful payment

### Sandbox Workaround
The system includes intelligent sandbox detection that handles M-Pesa sandbox quirks:
- Automatically detects sandbox environment
- Handles ResultCode '1032' as success after 2 minutes
- Generates proper receipt numbers with 'SBX' prefix for sandbox

### Production Setup
1. Apply for Go-Live on Safaricom Developer Portal
2. Get production credentials
3. Update environment variables
4. Set `MPESA_ENVIRONMENT=production`

## 📞 USSD Integration

### Supported Operations
- Balance inquiry
- Payment initiation
- Transaction history
- Account management

### USSD Flow
1. User dials USSD code
2. System processes request via `UssdController`
3. Response sent back to user's phone
4. Transaction logged in database

### USSD Menu Structure
```
*123# (Main Menu)
├── 1. Check Balance
├── 2. Make Payment
│   ├── 1. Rent Payment
│   └── 2. Deposit Payment
├── 3. Transaction History
└── 4. Account Info
```

## 🗄️ Database Schema

### Core Tables

#### Users Table
```sql
- id (Primary Key)
- name (String)
- email (String, Unique)
- role (Enum: landlord, tenant)
- phone_number (String)
- id_number (String)
- address (Text)
- emergency_contact (String)
- emergency_phone (String)
- profile_photo_path (String)
- date_of_birth (Date)
- occupation (String)
- bio (Text)
```

#### Properties Table
```sql
- id (Primary Key)
- landlord_id (Foreign Key -> users.id)
- name (String)
- description (Text)
- location (String)
- address (String)
- property_type (String)
- image (String)
- amenities (JSON)
- notes (Text)
```

#### Units Table
```sql
- id (Primary Key)
- property_id (Foreign Key -> properties.id)
- unit_number (String)
- unit_type (String)
- bedrooms (Integer)
- bathrooms (Integer)
- size_sqft (Decimal)
- rent_amount (Decimal)
- deposit_amount (Decimal)
- status (Enum: available, occupied, maintenance)
- features (JSON)
- notes (Text)
```

#### Tenant Assignments Table
```sql
- id (Primary Key)
- tenant_id (Foreign Key -> users.id)
- unit_id (Foreign Key -> units.id)
- landlord_id (Foreign Key -> users.id)
- start_date (Date)
- end_date (Date)
- monthly_rent (Decimal)
- status (Enum: active, inactive, terminated)
```

#### Payments Table
```sql
- id (Primary Key)
- tenant_id (Foreign Key -> users.id)
- unit_id (Foreign Key -> units.id)
- property_id (Foreign Key -> properties.id)
- amount (Decimal)
- payment_date (Date)
- payment_method (String)
- payment_type (Enum: rent, deposit, other)
- mpesa_transaction_id (Foreign Key)
- notes (Text)
- recorded_by (Foreign Key -> users.id)
```

#### M-Pesa Transactions Table
```sql
- id (Primary Key)
- tenant_id (Foreign Key -> users.id)
- unit_id (Foreign Key -> units.id)
- property_id (Foreign Key -> properties.id)
- phone_number (String)
- amount (Decimal)
- checkout_request_id (String, Unique)
- merchant_request_id (String)
- mpesa_receipt_number (String)
- transaction_date (Timestamp)
- status (Enum: pending, success, failed, cancelled)
- result_code (String)
- result_desc (Text)
- account_reference (String)
- transaction_desc (Text)
- payment_type (String)
- callback_data (JSON)
```

#### Messages Table
```sql
- id (Primary Key)
- sender_id (Foreign Key -> users.id)
- receiver_id (Foreign Key -> users.id)
- subject (String)
- message (Text)
- is_read (Boolean)
- priority (Enum: low, medium, high)
```

#### Maintenance Requests Table
```sql
- id (Primary Key)
- tenant_id (Foreign Key -> users.id)
- unit_id (Foreign Key -> units.id)
- property_id (Foreign Key -> properties.id)
- title (String)
- description (Text)
- priority (Enum: low, medium, high, urgent)
- status (Enum: pending, in_progress, completed, cancelled)
- assigned_to (Foreign Key -> users.id)
- completed_at (Timestamp)
```

#### Activity Logs Table
```sql
- id (Primary Key)
- user_id (Foreign Key -> users.id)
- action (String)
- description (Text)
- ip_address (String)
- user_agent (Text)
- properties (JSON)
```

### Relationships
- **One-to-Many**: User → Properties (Landlord)
- **One-to-Many**: Property → Units
- **Many-to-Many**: Users ↔ Units (via tenant_assignments)
- **One-to-Many**: User → Payments (Tenant)
- **One-to-One**: Payment → M-Pesa Transaction
- **One-to-Many**: User → Messages (Sender/Receiver)
- **One-to-Many**: Unit → Maintenance Requests

## 👤 User Roles

### 🏠 Landlord
- Manage multiple properties and units
- Track tenant assignments and lease terms
- Record and monitor rental payments
- View financial summaries and arrears
- Generate reports on property performance

### 🏡 Tenant
- View personal rental information and payment history
- Make payments via M-Pesa STK Push
- Submit and track maintenance requests
- Communicate with landlords through messaging system
- Export payment records to Excel
- Access unit details and lease information

## 🧪 Testing

### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run with coverage
php artisan test --coverage
```

### Test Categories
- **Feature Tests**: End-to-end functionality testing
- **Unit Tests**: Individual component testing
- **M-Pesa Tests**: Payment integration testing
- **Authentication Tests**: User role and permission testing

### M-Pesa Testing
Use the built-in test endpoints for M-Pesa integration:
```bash
# Test successful payment
GET /api/mpesa/test/success

# Test failed payment
GET /api/mpesa/test/failure

# Test timeout scenario
GET /api/mpesa/test/timeout

# Test sandbox detection
GET /api/mpesa/test/sandbox
```

## 🚀 Deployment

### Production Requirements
- PHP 8.2+ with required extensions
- MySQL 8.0+ or PostgreSQL 13+
- Redis (recommended for caching and sessions)
- SSL certificate for HTTPS
- Queue worker process

### Environment Setup
1. **Server Configuration**
   ```bash
   # Install PHP extensions
   sudo apt-get install php8.2-mysql php8.2-xml php8.2-curl php8.2-zip php8.2-gd

   # Configure web server (Apache/Nginx)
   # Point document root to /public directory
   ```

2. **Database Migration**
   ```bash
   php artisan migrate --force
   php artisan db:seed --force
   ```

3. **Queue Worker**
   ```bash
   # Start queue worker
   php artisan queue:work --daemon

   # Or use supervisor for process management
   sudo supervisorctl start laravel-worker:*
   ```

4. **Caching & Optimization**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan optimize
   ```

### Security Considerations
- Enable HTTPS for all M-Pesa callbacks
- Use strong database passwords
- Configure proper file permissions
- Enable Laravel's security features
- Regular security updates

### Monitoring & Logging
- Monitor M-Pesa transaction logs
- Set up error tracking (Sentry, Bugsnag)
- Monitor queue job failures
- Regular database backups

## 🔧 Key Features Implemented

### Recent Updates
- ✅ **M-Pesa Integration**: Complete STK Push payment system with sandbox support
- ✅ **USSD Integration**: Mobile money integration via USSD gateway
- ✅ **Sandbox Detection**: Smart M-Pesa sandbox handling with automatic success detection
- ✅ **Payment Processing**: Background job processing for pending M-Pesa transactions
- ✅ **Arrears Calculation**: Fixed calculation logic to use full months only
- ✅ **Dashboard Fixes**: Resolved undefined variable errors in tenant/landlord dashboards
- ✅ **Excel Export**: Payment history export functionality
- ✅ **Activity Logging**: Comprehensive system activity tracking

### Financial Calculations
- **Rent Due**: Calculated based on full months from lease start date
- **Arrears**: Smart arrears calculation (rent due minus rent paid, excluding deposits)
- **Payment Tracking**: Multi-type payment support (rent, deposits, other)
- **M-Pesa Integration**: Real-time payment processing with callback handling

### System Architecture
- **Role-Based Access**: Separate interfaces for landlords and tenants
- **Queue System**: Background processing for M-Pesa transactions
- **Responsive Design**: Mobile-first approach with Tailwind CSS
- **API-First**: RESTful API design for external integrations

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🐛 Bug Reports & Feature Requests

If you discover any bugs or have feature requests, please create an issue on the GitHub repository.

## 📞 Support

For support and questions, please open an issue or contact the development team.

---

**Built with ❤️ using Laravel**

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
