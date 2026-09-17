# 🧠 Wellbeing Management System

A web-based **Student Wellbeing Management System** designed to help educational institutions manage student wellbeing support, counselling, appointments, referrals, mentor observations, and administrative activities through a centralized platform.

The system provides separate dashboards and role-based functionality for **Students, Mentors, Counsellors, and Administrators**.

---

## 📌 Overview

The Wellbeing Management System provides a structured platform for managing student support services within an educational institution.

### 🎯 Main Objectives

- Manage student wellbeing-related activities digitally
- Provide students with access to appointments and support services
- Allow mentors to monitor and support assigned students
- Enable counsellors to manage counselling sessions and confidential notes
- Provide administrators with centralized system management
- Separate general mentor observations from confidential counselling information
- Provide role-based access to protect sensitive information

---

## 👥 User Roles

| Role | Main Responsibilities |
|------|-----------------------|
| 👨‍🎓 Student | Appointments, assessments, feedback, history, resources and profile |
| 👨‍🏫 Mentor | Assigned students, observations, referrals and appointments |
| 🧑‍⚕️ Counsellor | Counselling appointments, referrals, counselling notes and follow-ups |
| 👨‍💼 Admin | Users, students, mentors, counsellors, departments, appointments, referrals and system settings |

---

## ✨ Features

### 👨‍🎓 Student Module

- Student dashboard
- Appointment management and history
- Wellbeing self-assessment
- Feedback submission
- Notifications
- Wellbeing resources
- Profile management
- Referral information

### 👨‍🏫 Mentor Module

- Assigned student management
- Appointment viewing
- Student observations
- Referral creation
- Referral management
- Profile and settings

> **Privacy:** Mentor observations are stored separately from confidential counselling notes.

### 🧑‍⚕️ Counsellor Module

- Counselling appointment management
- Appointment status management
- Referral management
- Confidential counselling notes
- Follow-up management
- Student information
- Profile management
- Password change

### 👨‍💼 Admin Module

- Dashboard
- Student management
- Mentor management
- Counsellor management
- User management
- Department and class management
- Appointment management
- Referral management
- Reports
- System settings
- Profile management
- Database backup

---

## 🔐 Role-Based Access Control

The application uses role-based access control to restrict users to functionality appropriate for their role.

```text
                    ┌─────────────────────┐
                    │  Wellbeing System   │
                    └──────────┬──────────┘
                               │
             ┌─────────────────┼─────────────────┐
             │                 │                 │
        👨‍🎓 Student        👨‍🏫 Mentor       🧑‍⚕️ Counsellor
             │                 │                 │
      Student Features   Mentor Features   Counselling Features
             │                 │                 │
             └─────────────────┼─────────────────┘
                               │
                         👨‍💼 Administrator
                               │
                     System Management
```

---

## 🛠️ Technology Stack

### Frontend

- HTML5
- CSS3
- JavaScript
- Bootstrap 5.3.3
- Font Awesome 6.6.0
- Flatpickr
- Chart.js

### Backend

- PHP
- PDO
- MySQL / MariaDB

### Development Environment

- XAMPP
- Apache
- MySQL / MariaDB
- Linux / Kali Linux
- Git
- GitHub

---

## 🗂️ Project Structure

```text
wellbeing_system/
│
├── admin/
│   ├── dashboard.php
│   ├── students.php
│   ├── mentors.php
│   ├── counsellors.php
│   ├── appointments.php
│   ├── referrals.php
│   ├── departments.php
│   ├── classes.php
│   ├── reports.php
│   ├── settings.php
│   ├── backup.php
│   └── ...
│
├── mentor/
│   ├── dashboard.php
│   ├── students.php
│   ├── appointments.php
│   ├── observations.php
│   ├── referrals.php
│   ├── profile.php
│   └── ...
│
├── counsellor/
│   ├── dashboard.php
│   ├── appointments.php
│   ├── referrals.php
│   ├── counselling_notes.php
│   ├── followups.php
│   ├── profile.php
│   └── ...
│
├── student/
│   ├── dashboard.php
│   ├── appointments.php
│   ├── history.php
│   ├── assessment.php
│   ├── feedback.php
│   ├── notifications.php
│   ├── resources.php
│   ├── profile.php
│   └── ...
│
├── includes/
│   ├── config.php
│   ├── db.php
│   ├── auth.php
│   ├── session.php
│   ├── functions.php
│   ├── header.php
│   ├── footer.php
│   ├── admin_sidebar.php
│   ├── mentor_sidebar.php
│   ├── student_sidebar.php
│   └── ...
│
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
│
├── login.php
├── logout.php
└── README.md
```

---

## 🗄️ Database

The system uses a relational MySQL/MariaDB database.

### Main Tables

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

### Relationships

```text
Users
 │
 ├── Students
 ├── Mentors
 ├── Counsellors
 └── Administrators

Students
 │
 ├── Appointments
 ├── Referrals
 └── Mentor Observations

Mentors
 │
 ├── Students
 ├── Referrals
 └── Mentor Observations

Counsellors
 │
 ├── Appointments
 ├── Referrals
 ├── Counselling Notes
 └── Follow-ups
```

---

## 🔒 Privacy & Security

The system is designed with role separation and privacy in mind.

- Role-based access control
- Session-based authentication
- Password verification
- Password hashing
- Prepared SQL statements using PDO
- Confidential counselling notes separated from mentor observations
- Mentors cannot directly access confidential counselling notes
- Admin-only management functions
- Protected role-specific pages

---

## 🧪 Wellbeing Assessment

The Student module includes a self-reflection assessment.

Responses are scored as:

```text
Never              → 0
Sometimes          → 1
Often              → 2
Almost Always      → 3
```

The assessment provides a simple reflection score and is intended as a **wellbeing self-reflection tool**, not a medical diagnosis.

---

## 📅 Appointment Management

Appointments support the following statuses:

```text
Pending
   │
   ├── Approved
   │      │
   │      └── Completed
   │
   └── Rejected
```

Administrators can:

- Create appointments
- Edit appointments
- Delete appointments
- Change appointment status
- Assign students
- Assign counsellors
- Set appointment date and time
- Add appointment purpose

---

## 🔄 Referral Management

Mentors can create referrals for students who may require additional support.

A referral contains:

- Student
- Mentor
- Counsellor
- Reason
- Priority
- Status
- Counsellor notes

### Referral Status

```text
Pending
   │
   ├── Accepted
   │      │
   │      └── Completed
   │
   └── Rejected
```

---

## 💾 Database Backup

The Admin module provides a database backup feature using `mysqldump`.

Example workflow:

```text
Admin
  ↓
Settings
  ↓
Backup
  ↓
Generate SQL Backup
  ↓
Download .sql file
```

---

## 🚀 Installation

### 1. Clone the repository

```bash
git clone https://github.com/Brook-106/wellbeing_system.git
```

### 2. Move the project

For XAMPP on Linux:

```bash
sudo cp -r wellbeing_system /opt/lampp/htdocs/
```

Or clone directly:

```bash
cd /opt/lampp/htdocs
git clone https://github.com/Brook-106/wellbeing_system.git
```

### 3. Start XAMPP

```bash
sudo /opt/lampp/lampp start
```

Check the services:

```bash
sudo /opt/lampp/lampp status
```

### 4. Create the database

Open:

```text
http://localhost/phpmyadmin
```

Create:

```text
wellbeing_system
```

Import the project's SQL database file if provided.

### 5. Configure database connection

Update:

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

Adjust the credentials according to your local MySQL/MariaDB configuration.

### 6. Open the application

```text
http://localhost/wellbeing_system/
```

---

## 🔑 Authentication

Users log in through:

```text
/login.php
```

After successful authentication:

```text
Admin       → Admin Dashboard
Mentor      → Mentor Dashboard
Counsellor  → Counsellor Dashboard
Student     → Student Dashboard
```

---

## 🧰 Development

Check PHP syntax for a specific file:

```bash
php -l filename.php
```

Check all Admin files:

```bash
for file in admin/*.php; do
    php -l "$file"
done
```

Check Git status:

```bash
git status
```

---

## 📦 Git Workflow

After making changes:

```bash
git add .
```

Commit:

```bash
git commit -m "Update wellbeing system"
```

Push:

```bash
git push
```

The repository uses **SSH authentication** for GitHub.

---

## 📸 Screenshots

Screenshots can be added using a `screenshots/` directory.

Example:

```markdown
![Admin Dashboard](screenshots/admin-dashboard.png)
![Student Dashboard](screenshots/student-dashboard.png)
![Mentor Dashboard](screenshots/mentor-dashboard.png)
![Counsellor Dashboard](screenshots/counsellor-dashboard.png)
```

> Do not upload screenshots containing real passwords, private student information, email credentials, or confidential counselling notes.

---

## 🎓 Project Purpose

This project was developed as an academic software project to demonstrate:

- Web application development
- Database management
- PHP backend development
- Role-based authentication
- CRUD operations
- Session management
- Relational database design
- User interface design
- Privacy-aware information management
- System administration

---

## 🔮 Future Improvements

Possible future enhancements include:

- Real-time notifications
- Email notifications
- SMS notifications
- Advanced wellbeing analytics
- Appointment reminders
- Improved reporting
- Secure document management
- Mobile application
- Automated deployment
- Enhanced audit logging
- More comprehensive assessment management

---

## 👨‍💻 Developer

**Adithyan M P**

B.Sc. Computer Science (Honours)

Post Graduate Department of Computer Science  
Sree Sankara Vidyapeetom College, Valayanchirangara, Ernakulam

---

## 📜 License

This project is developed for educational and academic purposes.

---

⭐ If you find this project useful, consider giving the repository a star.
