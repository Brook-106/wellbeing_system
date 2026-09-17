# Student Wellbeing Management System

A web-based system for managing student wellbeing services in a college environment. The application provides separate access for students, mentors, counsellors, and administrators.

## About the Project

The system brings common student support activities into one place. Students can manage appointments and access wellbeing resources, while mentors and counsellors can handle referrals and student support activities. Administrators can manage the main data and settings of the system.

The project was developed as an academic project using PHP, MySQL/MariaDB, Bootstrap and JavaScript.

## Main Modules

### Student

- Student dashboard
- View appointments
- Appointment history
- Wellbeing self-assessment
- Feedback
- Notifications
- Wellbeing resources
- Profile management
- Referral information

### Mentor

- View assigned students
- View appointments
- Add student observations
- Create referrals
- Manage referrals
- Profile management
- Settings

### Counsellor

- Manage counselling appointments
- View and handle referrals
- Add counselling notes
- Manage follow-ups
- View student details required for counselling
- Profile management
- Password change

### Admin

- Dashboard
- Student management
- Mentor management
- Counsellor management
- User management
- Department management
- Class management
- Appointment management
- Referral management
- Reports
- Settings
- Profile management
- Database backup

## Technology Used

**Frontend**
- HTML
- CSS
- JavaScript
- Bootstrap 5.3.3
- Font Awesome 6.6.0
- Chart.js
- Flatpickr

**Backend**
- PHP
- PDO

**Database**
- MySQL / MariaDB

**Tools**
- XAMPP
- Git
- GitHub
- Linux / Kali Linux

## Project Structure

```text
wellbeing_system/
│
├── admin/
├── mentor/
├── counsellor/
├── student/
├── includes/
├── assets/
│
├── login.php
├── logout.php
└── README.md
```

The main folders are separated by user role. Shared database, session, authentication and layout files are kept inside `includes/`.

## Database

The application uses a MySQL/MariaDB database named:

```text
wellbeing_system
```

Some of the main tables are:

```text
users
students
mentors
counsellors
departments
classes
appointments
referrals
counselling_notes
followups
mentor_observations
settings
```

## Access Control

Each role has its own section of the application. Pages check the logged-in user's session and role before allowing access.

Confidential counselling notes are kept separate from mentor observations. Mentors do not have access to confidential counselling notes.

## Appointment Status

Appointments can have the following statuses:

```text
Pending
Approved
Rejected
Completed
```

## Referral Status

Referrals can have the following statuses:

```text
Pending
Accepted
Rejected
Completed
```

## Wellbeing Assessment

The student assessment is a simple self-reflection tool. It uses four response options:

```text
Never
Sometimes
Often
Almost Always
```

The result is intended for personal reflection and is not a medical diagnosis.

## Database Backup

Administrators can create a database backup from the system. The backup feature uses `mysqldump` to generate an SQL file that can be downloaded.

## Requirements

Before running the project, make sure you have:

- PHP 8.0 or later
- MySQL or MariaDB
- Apache
- XAMPP
- A web browser

## Running Locally

Clone the repository:

```bash
git clone https://github.com/Brook-106/wellbeing_system.git
```

Move into the XAMPP web directory:

```bash
cd /opt/lampp/htdocs
```

If the repository was cloned somewhere else, place the project inside the XAMPP `htdocs` directory.

Start XAMPP:

```bash
sudo /opt/lampp/lampp start
```

Create the database in phpMyAdmin:

```text
http://localhost/phpmyadmin
```

Create a database named:

```text
wellbeing_system
```

Then configure the database connection in:

```text
includes/config.php
```

Example:

```php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'wellbeing_system');
define('DB_USER', 'root');
define('DB_PASS', '');
```

Finally open:

```text
http://localhost/wellbeing_system/
```

## Git

The repository uses Git for version control and GitHub for remote storage.

Typical workflow:

```bash
git status
git add .
git commit -m "Update project"
git push
```

The GitHub remote is configured using SSH.

## Security

The project includes basic security measures such as:

- Session-based authentication
- Role checks
- Password hashing
- Prepared statements with PDO
- Separate role-based pages
- Separation of mentor observations and counselling notes

For a production deployment, additional security controls, validation, logging and server configuration would be required.

## Project Purpose

This project was created as an academic project to practise PHP web development, database design, authentication, CRUD operations and role-based access control.

## Developer

**Adithyan M P**

B.Sc. Computer Science (Honours)

Sree Sankara Vidyapeetom College, Valayanchirangara, Ernakulam

## License

This project is intended for educational and academic use.
