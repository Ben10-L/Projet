# RemindMe - Task Management Application

RemindMe is a lightweight and elegant task management application inspired by iOS design principles. Built using PHP, MySQL, and modern CSS, it delivers a clean, responsive, and user-friendly interface for managing tasks efficiently.

## Features

- **User Authentication**
  - Secure login and signup system
  - Password hashing for security
  - Session management

- **Task Management**
  - Create, edit, and delete tasks
  - Mark tasks as complete/incomplete
  - Set due dates for tasks
  - Organize tasks into lists

- **List Management**
  - Create custom lists
  - Edit list names
  - Delete lists (with associated tasks)
  - Quick access to all lists

- **Smart Views**
  - Today's Tasks: View tasks due today
  - All Tasks: View all tasks across lists
  - List-specific views

- **Modern UI**
  - iOS-inspired design
  - Responsive layout
  - Smooth animations
  - Mobile-friendly interface

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache web server
- XAMPP (recommended for local development)

## Installation

1. **Clone the Repository**
   ```bash
   git clone https://github.com/Ben10-L/remindme.git

   cd remindme
   ```

2. **Database Setup**
   - Start your MySQL server
   - Create a new database named 'remindme'
   - Import the database structure:
     ```bash
     mysql -u root -p remindme < database.sql
     ```

3. **Configuration**
   - Open `includes/db.php`
   - Update database credentials if needed:
     ```php
     $host = 'localhost';
     $dbname = 'remindme';
     $username = 'root';
     $password = '';
     ```

4. **Web Server Setup**
   - Place the project in your web server's root directory
   - For XAMPP: `/Applications/XAMPP/xamppfiles/htdocs/remindme`
   - Ensure Apache and MySQL are running

5. **Access the Application**
   - Open your web browser
   - Navigate to `http://localhost/remindme`
   - Create a new account and start using the application

## Usage

1. **Creating an Account**
   - Click "Sign up" on the login page
   - Enter your desired username and password
   - Log in with your credentials

2. **Managing Lists**
   - Click "New List" to create a list
   - Use the edit/delete buttons to manage lists
   - Click on a list to view its tasks

3. **Managing Tasks**
   - Click "+" to add a new task
   - Set a title and optional due date
   - Check/uncheck tasks to mark them complete
   - Use edit/delete buttons to manage tasks

4. **Using Quick Views**
   - "Today's Tasks" shows tasks due today
   - "All Tasks" shows all your tasks
   - Click on any list to view its specific tasks

## File Structure

```
remindme/
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── script.js
├── includes/
│   ├── auth.php
│   ├── db.php
│   ├── list.php
│   ├── logout.php
│   └── task.php
├── database.sql
├── index.php
├── login.php
├── signup.php
└── dashboard.php
```

## Security Features

- Password hashing using PHP's password_hash()
- Prepared statements for all database queries
- Session-based authentication
- Input sanitization
- XSS protection

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

Need help? Feel free to open an issue, and we’ll be happy to assist.
