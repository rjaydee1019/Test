# HR & Employee Records System

PHP 8+ and MySQL/MariaDB starter app for XAMPP.

## Features
- Dashboard with total/active employee and department counts.
- Login with password hashing, session ID regeneration, and CSRF validation.
- Roles: admin, hr, viewer; administrator user creation and account activation/deactivation.
- Employee create/read/update/delete, search, department/status filters.
- Photo and document upload validation by MIME type and 5 MB size limit.
- Printable employee list and filtered reports (browser Print / Save as PDF).
- Audit trail for authentication, user administration, employee changes, and backup.
- JSON backup and restore for users, employee records, and audit logs.

## Install (XAMPP)
1. Copy the project to `C:\xampp\htdocs\hr-records`.
2. Start Apache and MySQL in XAMPP.
3. Open `http://localhost/phpmyadmin` and import `schema.sql`.
4. Check `config.php` database settings. Defaults are host `127.0.0.1`, database `hr_records`, user `root`, blank password (local XAMPP only).
5. Ensure PHP extensions `mysqli` and `fileinfo` are enabled and `uploads/` is writable.
6. Visit `http://localhost/hr-records/install.php` and create the first admin with a unique password of at least 12 characters.
7. Sign in at `http://localhost/hr-records/login.php`. Delete `install.php` or restrict access after setup.

## Roles
- **admin**: employee management, user management, audit logs, backup and restore.
- **hr**: employee management and reports.
- **viewer**: read-only employee records and reports.

## Testing checklist
1. Import schema and confirm `users`, `employees`, and `audit_logs` tables exist.
2. Create admin and test successful and failed login.
3. As admin, create HR and viewer accounts from **Users**; verify each role's permissions.
4. Add employee and confirm dashboard/list counts update.
5. Try duplicate employee number and confirm it is rejected.
6. Edit a record and upload valid JPG/PNG/WEBP photo and PDF/JPG/PNG document; test invalid type and a file over 5 MB.
7. Search by name/employee number/department and filter status.
8. Generate filtered report and use Print / Save as PDF.
9. Check audit logs for login and employee create/update/delete.
10. Download a JSON backup and verify it is non-empty. Back up the `uploads/` folder separately.
11. In a test database, restore a trusted JSON backup and confirm users, employees, and audit logs return. Restore replaces current data.
12. Test viewer role cannot modify records or access admin pages.
13. Logout and confirm protected pages redirect to login.

## Backup and restore
The administrator-only Backup/Restore page exports a JSON backup of the three database tables and accepts a backup in the same application format. Restore replaces the current users, employee records, and audit logs; take a fresh backup first and use only a trusted file. Uploaded files are not embedded in the JSON and the `uploads/` directory must be backed up/restored separately. After restoring, sign out and log in again using an account present in the restored backup.

## Production and privacy notes
This is a starter application, not a certified HRIS. Employee data is sensitive. Before production, use HTTPS and least-privilege database credentials; add login rate limiting, password reset, pagination, retention controls, and restore drills; store uploads outside the public web root and deliver files through authenticated download endpoints; configure encrypted off-site backups; and review Philippine Data Privacy Act (RA 10173) obligations with your Data Protection Officer. Never commit real employee data, production credentials, or backups to GitHub.

## Schema
- `users`: identity, password hash, role and active flag.
- `employees`: employee profile and attachment paths.
- `audit_logs`: actions, user, entity, IP and timestamp.