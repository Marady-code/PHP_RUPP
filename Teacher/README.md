# Teacher Management System

A comprehensive web application for managing teachers, their teaching hours, and payments.

## Features

- Teacher Management (CRUD operations)
- Teaching Hours Tracking
- Payment Management
- Deleted Records Management
- Responsive Design
- Modern UI/UX
- Secure Authentication
- Data Validation
- Error Handling

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web Server (Apache/Nginx)
- Composer (for dependency management)

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/teacher-management.git
cd teacher-management
```

2. Create a MySQL database and import the schema:
```bash
mysql -u root -p < config/schema.sql
```

3. Configure the database connection:
Edit `config/config.php` and update the database credentials:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'teachers');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

4. Set up the web server:
- Point your web server's document root to the project directory
- Ensure the web server has write permissions for the `uploads` directory

5. Access the application:
Open your web browser and navigate to:
```
http://localhost/teacher-management
```

## Usage

### Teacher Management
- Add new teachers with their details
- View and edit teacher information
- Track teacher status (active/inactive)
- Manage teacher qualifications and specializations

### Teaching Hours
- Record teaching hours for each teacher
- Track hours by subject and class
- Generate reports and summaries
- Export data for analysis

### Payment Management
- Record teacher payments
- Track payment status
- Generate payment reports
- Export payment data

### Deleted Records
- View and restore deleted records
- Maintain data integrity
- Track deletion history

## Security Features

- Input validation and sanitization
- Prepared statements for database queries
- Password hashing
- Session management
- CSRF protection
- XSS prevention

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

For support, email support@example.com or open an issue in the GitHub repository.

## Acknowledgments

- Bootstrap for the UI framework
- Font Awesome for icons
- All contributors who have helped improve this project 