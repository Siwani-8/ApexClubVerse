# ApexClubVerse 

A centralized web portal for managing campus clubs at Apex College, Pokhara University.

## About The Project
ApexClubVerse is a unified online platform designed to bring all six Apex College clubs together in one place. Instead of scattered social media posts and paper forms, students can find everything they need about campus clubs right here — from club details and events to voting and intake applications.

### Features
**Student**
- Browse all six campus clubs and view details
- View upcoming events on the events feed
- Vote on which events clubs should perform next
- Submit club intake applications online

**Admin**
- View and manage all club intake applications
- Accept or reject student applications
- Add and delete events
- View voting results
- Monitor all registered students

### Technologies Used
- **Frontend** — HTML, CSS, JavaScript
- **Backend** — PHP 8+
- **Database** — MySQL / MariaDB
- **Local Server** — XAMPP
- **Hosting** — ProFreeHost (or any Apache shared host)
- **Version Control** — Git & GitHub

## Database

- Default local database name: `apex_club_db`
- **11 tables:** clubs, users, bod_members, boa_members, events, event_editions, event_gallery, polls, poll_options, poll_votes, registrations

## Project Structure

```
apexclubverse/                 ← upload these contents into htdocs / public_html
├── index.php, clubs.php, ...  # Public page entry points
├── admin.php, login.php, ...
├── includes/                  # Shared PHP (not web-accessible)
│   ├── config.php             # DB credentials + url()/media helpers
│   ├── db.php
│   ├── header.php / footer.php
│   ├── club_admin_helpers.php
│   ├── mail.php / send_verification_email.php
│   └── mail_config.example.php  # copy → mail_config.php (gitignored)
├── assets/css/                # Stylesheets
├── images/                    # Site & event images
│   ├── events/
│   └── members/
├── uploads/                   # Admin-uploaded event images
├── lib/PHPMailer/             # PHPMailer library
├── database/schema.sql        # Full schema + public seed data
├── scripts/                   # One-off maintenance scripts
└── .htaccess                  # Protects includes/, database/, scripts/
```

## Setup (Local – XAMPP)

1. Place this project folder inside `htdocs` (e.g. `htdocs/apexclubverse`).
2. Create the database: `CREATE DATABASE apex_club_db CHARACTER SET utf8mb4;`
3. Import `database/schema.sql` (phpMyAdmin or CLI).
4. Confirm DB settings in `includes/config.php` (defaults work for XAMPP).
5. Copy `includes/mail_config.example.php` → `includes/mail_config.php` and add your
   Gmail address + [App Password](https://myaccount.google.com/apppasswords).
   On ProFreeHost, make sure this file exists on the server (it is not in Git).
   If SMTP still fails, signup/login will show a **Verify my account now** button
   so users are not locked out. Check `includes/mail_error.log` on the server for the
   real SMTP error.
6. Open `http://localhost/apexclubverse/`.

## Deployment (ProFreeHost)

1. In the hosting panel, create a MySQL database and note the **host**, **username**,
   **password**, and **database name**.
2. Upload **the contents** of this project folder into `htdocs` (or `public_html`) —
   `index.php` should sit in the web root (or in a subfolder if you prefer).
3. Import `database/schema.sql` into your hosting database via phpMyAdmin.
4. Edit `includes/config.php` and set:
   ```php
   define('DB_HOST', 'sqlXXX.profreehost.com'); // or localhost — use panel value
   define('DB_USER', 'your_db_user');
   define('DB_PASS', 'your_db_password');
   define('DB_NAME', 'your_db_name');
   ```
5. Create `includes/mail_config.php` on the server (from the example file) with SMTP
   credentials. Some free hosts block outbound SMTP (port 587); test if verification
   emails fail.
6. URLs and verification links are built automatically from the request host and
   folder path — no hardcoded domain is required.

## Club admin logins (seeded in schema.sql)

Password for **all** club admins: `Admin@12345`

| Club ID | Club | Email | Password |
|--------:|------|-------|----------|
| 1 | Performing Arts (APAC) | `admin.performingarts@apexcollege.edu.np` | `Admin@12345` |
| 2 | Sports & Leadership | `admin.sports@apexcollege.edu.np` | `Admin@12345` |
| 3 | Travel & Tourism | `admin.travel@apexcollege.edu.np` | `Admin@12345` |
| 4 | Media & Marketing | `admin.media@apexcollege.edu.np` | `Admin@12345` |
| 5 | IT Club | `admin.it@apexcollege.edu.np` | `Admin@12345` |
| 6 | HEAT | `admin.heat@apexcollege.edu.np` | `Admin@12345` |

Accounts are pre-verified (`email_verified = 1`). Change these passwords after first login on a live host.

### Team Members
- Monali Kharel
- Shristi Shrestha
- Siwani Koirala
- Yunisha Shakya
