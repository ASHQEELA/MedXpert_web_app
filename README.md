# MedXpert

MedXpert is a healthcare web app for Bangladesh. Users book doctors, find blood donors, order medicines, and request ambulances from one dashboard. Admins manage all data from a separate panel.

## Features

**For users**
- Register and log in with a secure password hash
- Search doctors by name, specialization, or hospital, then book appointments
- Search blood donors by blood type
- Browse the pharmacy by category and order medicines
- Request an ambulance
- View your appointments and orders
- Edit your profile and upload a profile photo

**For admins**
- Dashboard overview
- Manage doctors, blood donors, and medicines
- Review and update appointments and orders

## Tech stack

- PHP 8 (procedural, mysqli prepared statements)
- MySQL or MariaDB
- HTML, CSS, and vanilla JavaScript
- Font Awesome icons

## Project structure

```
MedXpert_web_app/
├── index.php             Login and registration page
├── dashboard.php         User dashboard
├── profile.php           Profile page
├── my_appointments.php   User appointments
├── my_orders.php         User orders
├── modules/              consult, blood, pharmacy, ambulance
├── admin/                Admin panel pages
├── api/                  JSON endpoints (login, register, booking, orders)
├── config/               db.php, header.php, footer.php
├── assets/               css and js
├── uploads/              doctors, medicines, profiles
└── database.sql          Schema and sample data
```

## Database

`database.sql` creates 8 tables:

| Table | Purpose |
| --- | --- |
| users | Accounts with a `user` or `admin` role |
| doctors | Doctor profiles, fees, and availability |
| blood_donors | Donor records by blood type |
| medicines | Pharmacy catalog |
| appointments | Doctor bookings |
| medicine_orders | Order headers |
| order_items | Items in each order |
| ambulance_requests | Ambulance requests |

## Run locally

1. Install XAMPP, or any stack with PHP 8 and MySQL.
2. Copy the project folder into `htdocs` and name it `medxpert`.
3. Start Apache and MySQL.
4. Open phpMyAdmin and import `database.sql`.
5. Check the credentials in `config/db.php`. The defaults match XAMPP:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'medxpert');
```

6. Open `http://localhost/medxpert/`.

The sidebar links in `config/header.php` start with `/medxpert/`. Keep the folder name `medxpert`, or update those paths.

## Demo accounts

All sample accounts use the password `password`.

| Role | Email |
| --- | --- |
| Admin | admin@medxpert.com |
| User | john@example.com |
| User | sarah@example.com |

Change these passwords before you deploy publicly.

## Deploy for free

Use a PHP host with MySQL, such as InfinityFree.

1. Create a hosting account and a MySQL database.
2. Remove the `CREATE DATABASE` and `USE` lines from `database.sql`. Import the file into your new database with phpMyAdmin.
3. Update `config/db.php` with the host, user, password, and database name from your panel.
4. Upload the project files to `htdocs`.
5. If you upload to the root and not a `medxpert` folder, replace `/medxpert/` with `/` in `config/header.php`.
6. Set the `uploads` subfolders to permission 755.

## Security notes

- Do not commit real database credentials. Keep `config/db.php` out of public repositories.
- Change the demo passwords.
- Do not store real patient data on a free host.
## View Live
[Live](https://medxpert.infy.click/)
## Author

Ashiq, Daffodil International University
