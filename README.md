# Van Sales, Order Management & Accounting Mini-ERP

A comprehensive foundational system for FMCG/Distribution businesses built with PHP 8.x (PDO) and MySQL.

## 🚀 Features
- **Van Sales & POS**: Mobile-first interface for salesmen to process spot sales and inventory deduction.
- **GST Compliance**: Automatic tax splitting (CGST/SGST/IGST) for Indian taxation.
- **Double-Entry Accounting**: Automated and manual journal entries, Trial Balance, and GSTR-1 reports.
- **Inventory Management**: Warehouse to Van stock transfers with voucher generation.
- **Secure Authentication**: Role-Based Access Control (RBAC) with hashed passwords and CSRF protection.

---

## 🛠️ Installation & Publishing Steps

To publish this system to your server, follow these steps:

### 1. Database Setup
1. Create a new MySQL/MariaDB database (e.g., `mini_erp`).
2. Import the `database_schema.sql` file into your database.
3. Update `config.php` with your database credentials (`$dbHost`, `$dbName`, `$dbUser`, `$dbPass`).

### 2. File Upload
- Upload all files from this repository to your web server's root or a subdirectory (e.g., `/var/www/html/`).

### 3. Initialize Data
- Run the `seed_data.php` script once by visiting `yourdomain.com/seed_data.php` in your browser. This will populate the Chart of Accounts, sample products, and default users.
- **Important:** Delete `seed_data.php` from your server after successful initialization for security.

### 4. Configure Web Server
- Ensure your web server (Apache/Nginx) is configured to handle PHP 8.x.
- Set proper folder permissions (usually `755` for directories and `644` for files).

---

## 🔐 Default Login Credentials

After running `seed_data.php`, use the following accounts to test the system:

| Role | Username / Email | Password |
| :--- | :--- | :--- |
| **Super Admin** | `admin@erp.com` | `admin123` |
| **Accountant** | `acc@erp.com` | `acc123` |
| **Van Salesman** | `sales@erp.com` | `sales123` |

---

## 📂 Core Files Overview
- `login.php`: Secure entry point.
- `index.php`: Spot Sale / POS terminal for salesmen.
- `coa.php`: Chart of Accounts management.
- `stock_transfer.php`: Warehouse to Van inventory movement.
- `trial_balance.php`: Financial sanity check report.
- `gstr1_b2b.php`: GST compliance report.
- `vouchers.php`: Manual accounting entries (expenses, payments).
- `auth_check.php`: RBAC Middleware for session protection.
- `SalesTransactionManager.php`: The "brain" of the sales and accounting automation.

---
*Developed for FMCG/Distribution Excellence.*
