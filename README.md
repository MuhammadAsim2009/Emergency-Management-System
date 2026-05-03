# 🚨 Emergency Response Management System

A web-based Emergency Response Management System built with **PHP** and **MySQL** that allows citizens to report emergencies, responders to manage assignments, and admins to oversee the entire system.

---

## 📋 Table of Contents

- [About the Project](#about-the-project)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Getting Started](#getting-started)
  - [Prerequisites](#prerequisites)
  - [Installation](#installation)
- [Database Setup](#database-setup)
- [Usage](#usage)
- [Folder Structure](#folder-structure)
- [Contributing](#contributing)
- [License](#license)

---

## 📖 About the Project

The **Emergency Response Management System** is a PHP-based web application with three separate panels — **Admin**, **Citizen**, and **Responder** — each with dedicated functionality to handle emergency situations end-to-end.

> 💡 This project was built while learning PHP as a hands-on practice project.

---

## ✨ Features

### 👤 Citizen Panel
- Register & Login
- Report an emergency incident
- View personal emergency reports via dashboard
- Manage profile

### 🛡️ Responder Panel
- View assigned emergencies
- Mark tasks as completed
- Manage responder profile
- Dedicated responder dashboard

### ⚙️ Admin Panel
- Admin dashboard with overview
- View & manage all emergencies
- Add & manage responders
- Manage users
- Reports & analytics
- Create new emergency entries

---

## 🛠️ Tech Stack

| Technology | Usage |
|---|---|
| PHP | Backend / Server-side logic |
| MySQL | Database |
| HTML / CSS | Frontend structure & styling |
| Bootstrap | Responsive UI |
| JavaScript | Client-side interactivity |

---

## 🚀 Getting Started

### Prerequisites

Make sure you have the following installed:

- [XAMPP](https://www.apachefriends.org/) or [WAMP](https://www.wampserver.com/) (PHP + MySQL + Apache)
- PHP >= 7.4
- MySQL >= 5.7
- A web browser

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/MuhammadAsim2009/emergency-management-system.git
   ```

2. **Move the project to your server directory**
   - For XAMPP: `C:/xampp/htdocs/emergency_response_management_system`
   - For WAMP: `C:/wamp64/www/emergency_response_management_system`

3. **Start Apache and MySQL** from XAMPP/WAMP control panel

---

## 🗄️ Database Setup

1. Open your browser and go to `http://localhost/phpmyadmin`
2. Create a new database — e.g., `ems_db`
3. Import the SQL file:
   - Click on your database → **Import** → Select the `.sql` file from the `database/` folder → Click **Go**
4. Update the database connection in `include/db.php`:
   ```php
   $host = "localhost";
   $user = "root";
   $password = "";
   $database = "ems_db";
   ```

---

## 💻 Usage

1. Open your browser and visit:
   ```
   http://localhost/emergency_response_management_system
   ```
2. **Citizen** → Register/Login → Report an emergency or view your reports
3. **Responder** → Login → View assignments and mark tasks complete
4. **Admin** → Login → Manage users, responders, emergencies, and view analytics

---

## 📁 Folder Structure

```
emergency_response_management_system/
│
├── admin/                        # Admin panel
│   ├── add_responder.php
│   ├── admin_dashboard.php
│   ├── all_emergencies.php
│   ├── manage_users.php
│   ├── manage-emergencies.php
│   ├── new_emergency.php
│   ├── reports_analytics.php
│   ├── responders.php
│   ├── script.js
│   └── style.css
│
├── citizen/                      # Citizen panel
│   ├── partials/                 # Navbar & Sidebar components
│   ├── citizen_dashboard.php
│   ├── dashboard.php
│   ├── my_reports.php
│   ├── profile.php
│   ├── report_emergency.php
│   ├── script.js
│   └── style.css
│
├── responder/                    # Responder panel
│   ├── completed_tasks.php
│   ├── my_assignments.php
│   ├── responder_dashboard.php
│   └── responder_profile.php
│
├── database/                     # Database SQL file
│   └── ems_db.sql
│
├── include/                      # Shared backend files
│   └── db.php                    # Database connection
│
├── login.php                     # Login page
├── logout.php                    # Logout handler
├── register.php                  # Registration page
├── script.js                     # Global JavaScript
└── style.css                     # Global CSS
```

---

## 🤝 Contributing

Contributions are welcome! Feel free to fork this repo and submit a pull request.

1. Fork the project
2. Create your feature branch: `git checkout -b feature/YourFeature`
3. Commit your changes: `git commit -m 'feat: add YourFeature'`
4. Push to the branch: `git push origin feature/YourFeature`
5. Open a Pull Request

---

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

---

## 👤 Author

**Muhammad Asim**
- GitHub: [@MuhammadAsim2009](https://github.com/MuhammadAsim2009)

---

> ⭐ If you found this project helpful, please give it a star!
