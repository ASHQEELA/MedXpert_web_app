# MedXpert

MedXpert is a healthcare web app for Bangladesh. Users book doctors, find blood donors, order medicines, and request ambulances from one dashboard. Admins manage all data from a separate panel.

## Features

**For users**
- Register and log in with hashed passwords
- Search doctors by name, specialization, or hospital, then book an appointment
- See a "Verified" badge on doctors an admin has checked
- Search blood donors by blood type and call them directly
- Browse the pharmacy by category, see stock levels, and order medicines
- Request an ambulance or call the 999 emergency line in one tap
- Track your appointments and orders
- Edit your profile and upload a profile photo
- See your next appointment and latest order on the dashboard

**For admins**
- Dashboard with totals and recent activity
- Add, edit, and delete doctors, donors, and medicines, with photo upload
- Mark doctors as verified
- Review and update appointments and orders

**Design and accessibility**
- Teal and dark slate theme with flat colors
- Works on phones: slide-out menu, scrollable tables, large touch targets
- Toast messages instead of browser alerts
- Loading skeletons, empty states, and retry buttons on every list
- Text contrast of 4.5:1 or better on all pages, visible focus outlines, skip link, labelled form fields, keyboard-friendly dialogs

## Tech stack

- PHP 8 with mysqli prepared statements
- MySQL or MariaDB
- HTML, CSS, and vanilla JavaScript
- Inter font and Font Awesome icons from CDNs

## Project structure

```
MedXpert_web_app/
├── index.php              Login and registration
├── dashboard.php          User dashboard
├── profile.php            Profile and photo upload
├── my_appointments.php    User appointments
├── my_orders.php          User orders
├── modules/               consult, blood, pharmacy, ambulance
├── admin/                 Admin panel (doctors, donors, medicines, orders, appointments)
├── api/                   JSON endpoints (login, register, lists, booking, orders)
├── config/
│   ├── db.php             Database connection and helpers
│   ├── site.php           Footer contact details and emergency number
│   ├── header.php         Sidebar and page header
│   └── footer.php         Footer and scripts
├── assets/                css and js
├── uploads/               doctors, medicines, profiles, donors
├── migrations/            SQL updates for existing databases
└── database.sql           Schema and sample data
```

## Database

`database.sql` creates 8 tables and fills them with sample data.

| Table | Purpose |
| --- | --- |
| users | Accounts with a `user` or `admin` role |
| doctors | Profiles, fees, availability, and a `verified` flag |
| blood_donors | Donor records by blood type |
| medicines | Pharmacy catalog and stock |
| appointments | Doctor bookings |
| medicine_orders | Order headers |
| order_items | Items in each order |
| ambulance_requests | Ambulance requests |

If your database already exists from an older version, run `migrations/001_add_doctor_verified.sql` once in phpMyAdmin. The site works without it, but the verified badge and the admin checkbox appear only after you run it.

## Run locally

1. Install PHP 8 and MySQL, or install XAMPP.
2. Create a database and import `database.sql` with phpMyAdmin.
3. Set your credentials in `config/db.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'medxpert');
```

4. Make sure `uploads/donors/` exists. Create it if it is missing.
5. Start the app from the project folder:

```bash
php -S localhost:8000
```

6. Open `http://localhost:8000`.

The links in the app start from the site root (`/dashboard.php`). Serve the project from the root of a host or port. A subfolder such as `http://localhost/medxpert/` will break the links and styles.

## Demo accounts

All sample accounts use the password `password`.

| Role | Email |
| --- | --- |
| Admin | admin@medxpert.com |
| User | john@example.com |
| User | sarah@example.com |

Change these passwords before you deploy. To set a new one:

```bash
php -r "echo password_hash('YourNewPassword', PASSWORD_DEFAULT);"
```

Then run this in phpMyAdmin:

```sql
UPDATE users SET password = 'PASTE_HASH_HERE' WHERE email = 'admin@medxpert.com';
```

## Site settings

Open `config/site.php` to set the contact details shown in the footer. Empty values stay hidden.

```php
define('SITE_SUPPORT_EMAIL', 'support@example.com');
define('SITE_SUPPORT_PHONE', '+8801XXXXXXXXX');
define('SITE_EMERGENCY_NUMBER', '999');
```

## Security notes

- Do not commit real database credentials to a public repository.
- Change the demo passwords before you deploy.
- Do not store real patient data on a free host.
- Review the privacy sentence in `config/footer.php` so it matches how you handle data.

## Author

Ashiq, Daffodil International University
