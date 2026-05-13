==========================================================
  SkillBased — Job Discovery & Skill Match (PHP + MySQL)
==========================================================

📁 PROJECT STRUCTURE
----------------------------------------------------------
skillbased/
├── index.php                  Landing page
├── login.php                  Login (user + admin)
├── register.php               Signup
├── logout.php
├── dashboard.php              User dashboard
├── profile.php                Profile management
├── jobs.php                   Job listings + filters
├── apply_job.php              Job details + apply
├── search_jobs.php            AJAX search endpoint
├── config/
│   └── db.php                 MySQL connection
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── functions.php          Helpers + skill match
├── admin/
│   ├── dashboard.php
│   ├── add_job.php            Add / edit job
│   ├── manage_jobs.php
│   ├── manage_users.php
│   └── applicants.php
├── assets/
│   ├── css/style.css
│   ├── js/main.js
│   ├── images/
│   └── uploads/
│       ├── resumes/
│       └── profiles/
└── database/
    └── skillbased_db.sql      Database schema + sample data

==========================================================
🚀 SETUP — XAMPP LOCALHOST
==========================================================

1) Install XAMPP and start Apache + MySQL.
2) Copy the entire `skillbased` folder to:
        C:\xampp\htdocs\skillbased
3) Open http://localhost/phpmyadmin
4) Click "Import" → choose `database/skillbased_db.sql` → Go.
   (This creates the database `skillbased_db` with sample data.)
5) Open http://localhost/skillbased in your browser.

==========================================================
🔐 DEFAULT LOGINS
==========================================================
Admin:
   Email:    admin@gmail.com
   Password: admin123

Sample User (or register a new one):
   Email:    user@gmail.com
   Password: user123

==========================================================
✨ FEATURES
==========================================================
• Modern responsive landing page (gradient + glassmorphism)
• User signup/login with password hashing & session
• Resume + profile photo upload (PDF/DOC/Image)
• Job listings with search & filters (skill / location / type)
• Skill matching engine — % match between user & job skills
• Recommended jobs sorted by match score
• One-click apply with success toast
• Admin panel: add/edit/delete jobs, manage users, applicants
• Dark mode toggle, animated counters, typing animation
• AJAX live job search
• Bootstrap 5 + Bootstrap Icons
• Profile completion percentage
• Prepared statements & input sanitization

==========================================================
🛠 NOTES
==========================================================
• Make sure folders `assets/uploads/resumes/` and
  `assets/uploads/profiles/` are writable.
• PHP 7.4+ recommended.
• Default admin password verification supports both the
  hashed value in DB and the literal "admin123" as a
  convenience fallback for first-time setup.
