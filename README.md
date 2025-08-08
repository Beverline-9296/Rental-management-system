# 🏠 Rental Management System

A comprehensive web-based rental property management system built with Laravel, designed to streamline property management for landlords and provide a user-friendly experience for tenants.

## 📋 Table of Contents

- [About](#about)
- [Features](#features)
- [Technology Stack](#technology-stack)
- [Installation](#installation)
- [Usage](#usage)
- [User Roles](#user-roles)
- [Screenshots](#screenshots)
- [Contributing](#contributing)
- [License](#license)

## 🎯 About

The Rental Management System is a full-featured web application that helps landlords manage their rental properties efficiently while providing tenants with easy access to their rental information and payment history. The system handles everything from property listings and tenant assignments to payment tracking and arrears calculations.

## ✨ Features

### 🏘️ Property Management
- **Property Listings**: Create and manage multiple properties with detailed information
- **Unit Management**: Add multiple units per property with individual specifications
- **Property Images**: Upload and manage property photos
- **Location Tracking**: Store detailed address and location information

### 👥 Tenant Management
- **Tenant Profiles**: Comprehensive tenant information management
- **Lease Assignments**: Assign tenants to specific units with start/end dates
- **Tenant Dashboard**: Personal dashboard for tenants to view their rental information

### 💰 Financial Management
- **Payment Tracking**: Record and track all rental payments
- **Arrears Calculation**: Automatic calculation of outstanding rent based on full months
- **Payment History**: Detailed payment history for both landlords and tenants
- **Multiple Payment Methods**: Support for various payment methods

### 📊 Dashboard & Reporting
- **Landlord Dashboard**: Overview of properties, units, and financial summaries
- **Tenant Dashboard**: Personal rental information and payment history
- **Real-time Calculations**: Dynamic rent due and arrears calculations
- **Property Statistics**: Occupancy rates and financial metrics

## 🛠️ Technology Stack

- **Backend**: Laravel 12.x (PHP 8.2+)
- **Frontend**: Blade Templates, Tailwind CSS, JavaScript
- **Database**: MySQL
- **Authentication**: Laravel Breeze
- **File Storage**: Laravel Storage (Local/Cloud)
- **Icons**: Font Awesome
- **Styling**: Tailwind CSS with custom components

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

## 👤 User Roles

### 🏠 Landlord
- Manage multiple properties and units
- Track tenant assignments and lease terms
- Record and monitor rental payments
- View financial summaries and arrears
- Generate reports on property performance

### 🏡 Tenant
- View personal rental information
- Check payment history and outstanding balances
- Access unit details and property information
- View lease terms and important dates

## 🔧 Key Features Implemented

### Recent Updates
- ✅ **Arrears Calculation**: Fixed calculation logic to use full months only
- ✅ **Dashboard Fixes**: Resolved undefined variable errors in tenant/landlord dashboards
- ✅ **Payment Display**: Corrected recent payments display in tenant dashboard
- ✅ **Form Validation**: Fixed property update form validation issues
- ✅ **Route Optimization**: Improved route definitions and controller methods

### Financial Calculations
- **Rent Due**: Calculated based on full months from lease start date
- **Arrears**: Outstanding amount based on payments vs. rent due
- **Payment Tracking**: Comprehensive payment history with multiple payment methods

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
