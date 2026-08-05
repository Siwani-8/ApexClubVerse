ApexClubVerse 

A centralized web portal for managing campus clubs at Apex College, Pokhara University.

 About The Project
ApexClubVerse is a unified online platform designed to bring all six Apex College clubs together in one place. Instead of scattered social media posts and paper forms, students can find everything they need about campus clubs right here — from club details and events to voting and intake applications.

Features
 Student
- Browse all six campus clubs and view details
- View upcoming events on the events feed
- Vote on which events clubs should perform next
- Submit club intake applications online

 Admin
- View and manage all club intake applications
- Accept or reject student applications
- Add and delete events
- View voting results
- Monitor all registered students

 Technologies Used

- **Frontend** — HTML, CSS, JavaScript
- **Backend** — PHP
- **Database** — MySQL
- **Local Server** — XAMPP
- **Version Control** — Git & GitHub

 Database

- Database name: `apex_club_db`
- Total tables: 11
  - clubs
  - events
  - event_editions
  - event_gallery
  - users
  - polls
  - poll_options
  - poll_votes
  - registrations
  - bod_members
  - boa_members

## Project Structure

```
apexclubverse/
├── index.php, clubs.php, events.php, ...   # Page entry points (web root)
├── admin.php, login.php, signup.php, ...
├── includes/            # Shared PHP: db, header, footer, helpers, mail
│   ├── db.php
│   ├── header.php / footer.php
│   ├── club_admin_helpers.php
│   ├── mail.php / send_verification_email.php
│   └── mail_config.example.php   # copy to mail_config.php (gitignored)
├── assets/css/          # Stylesheets
├── images/              # Site & event images (referenced by the database)
├── uploads/             # Admin-uploaded event images
├── lib/PHPMailer/       # PHPMailer library
├── database/schema.sql  # Full database schema + public seed data
└── scripts/             # One-off maintenance / migration scripts
```

## Setup (Local - XAMPP)

1. Clone the repository into `htdocs`.
2. Create the database: `CREATE DATABASE apex_club_db CHARACTER SET utf8mb4;`
3. Import `database/schema.sql` (phpMyAdmin or CLI).
4. Copy `includes/mail_config.example.php` to `includes/mail_config.php` and add your
   Gmail address + [App Password](https://myaccount.google.com/apppasswords).
5. Open `http://localhost/apexclubverse/`.

## Deployment (ProFreeHost or similar shared hosting)

1. Upload the entire project folder contents into `htdocs` (or `public_html`).
2. Create a MySQL database in the hosting control panel and import `database/schema.sql`.
3. Update the credentials in `includes/db.php` with the host, username, password and
   database name provided by the host.
4. Create `includes/mail_config.php` on the server (copy from the example file) with
   your SMTP credentials. Note: some free hosts block outbound SMTP (port 587); if
   verification emails fail, test SMTP availability with your host.
5. Email verification links are generated dynamically from the request host, so no
   URL configuration is needed.

Team Members:
- Monali Kharel 
- Shristi Shrestha 
- Siwani Koirala 
- Yunisha Shakya 
