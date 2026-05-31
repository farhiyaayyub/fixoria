Fixoria Marketplace
> A two-sided local services marketplace connecting customers with verified professionals

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)

About the Project

Fixoria is a full-stack web marketplace that connects customers with local service professionals — think electricians, plumbers, repair technicians, and consultants. The platform handles both sides of the transaction: customers can browse, book, and rate providers, while providers manage their services, availability, and client communications.

Built with AI-assisted development tools, this project demonstrates end-to-end marketplace architecture including real-time messaging and payment flow.

 Features

For Customers
- Browse and search verified local service providers
- Book appointments directly through the platform
- Real-time messaging with providers
- Rate and review completed services
- Track booking history and status

For Service Providers
- Create and manage service listings
- Set availability and pricing
- Receive and respond to booking requests
- Real-time messaging with customers
- Dashboard with earnings and booking overview

Platform-wide
- **Secure Authentication** — Separate roles for customers and providers
- **Payment Integration** — Handle transactions within the platform
- **Ratings & Reviews System** — Build trust through verified feedback
- **Admin Panel** — Manage users, listings, and platform activity

 Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP (Procedural) |
| Database | MySQL |
| Frontend | HTML5, CSS3, JavaScript |
| Real-time | JavaScript polling / AJAX |
| Server | Apache (XAMPP) |

Screenshots

> Homepage & Service Listings

![Homepage](screenshots/homepage.png)

> Provider Dashboard

![Provider Dashboard](screenshots/provider-dashboard.png)

> Messaging System

![Messaging](screenshots/messaging.png)

 Getting Started

Prerequisites
- XAMPP (Apache + PHP + MySQL)
- PHP 7.4+
- MySQL 5.7+

Installation

1. Clone the repository bash
git clone https://github.com/eyaafar/fixoria-marketplace.git
```

2. Move to XAMPP `htdocs` bash
mv fixoria-marketplace /xampp/htdocs/

3. Import the database
- Open phpMyAdmin: `http://localhost/phpmyadmin`
- Create database: `fixoria_db`
- Import: `database/fixoria_db.sql`

4. Configure database connection
```php
// config/db.php
$host = 'localhost';
$dbname = 'fixoria_db';
$username = 'root';
$password = '';
```

5. Access the platform

http://localhost/fixoria-marketplace/

Test Accounts
| Role | Email | Password |
|---|---|---|
| Customer | customer@test.com | password123 |
| Provider | provider@test.com | password123 |
| Admin | admin@test.com | admin123 |


Project Structure


fixoria-marketplace/
├── config/
│   └── db.php              # Database connection
├── customer/
│   ├── dashboard.php       # Customer home
│   ├── browse.php          # Browse providers
│   ├── booking.php         # Book a service
│   └── messages.php        # Inbox
├── provider/
│   ├── dashboard.php       # Provider home
│   ├── services.php        # Manage listings
│   └── messages.php        # Client messages
├── admin/
│   └── dashboard.php       # Admin panel
├── assets/
│   ├── css/
│   └── js/
├── database/
│   └── fixoria_db.sql
└── index.php

What I Learned

- Designing a two-sided marketplace database schema
- Implementing real-time messaging using AJAX polling
- Building separate user role flows within a single codebase
- Handling payment logic and transaction tracking
- Creating a provider-facing dashboard with booking management

Developer

Farhiya Ayyub
BS Computer Science 
Mindanao State University – Sulu | Class of 2026

[![LinkedIn](https://img.shields.io/badge/LinkedIn-0077B5?style=flat&logo=linkedin&logoColor=white)](https://www.linkedin.com/in/farhiya-ayyub-920605412)
[![Portfolio](https://img.shields.io/badge/Portfolio-e63946?style=flat&logo=firefox&logoColor=white)](https://github.com/eyaafar)

> *Built with AI-assisted development tools, 2025.*
