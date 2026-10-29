# HR & Employee Records System

PHP 8+ and MySQL/MariaDB starter app for XAMPP.

## Features
- Dashboard with total/active employee and department counts.
- Login with password hashing, session regeneration, and CSRF token validation.
- Roles: admin, hr, viewer.
- Employee create/read/update/delete, search, department/status filters.
- Photo and document upload validation by MIME type and 5 MB size limit.
- Printable employee list and filtered reports (browser Print / Save as PDF).
- Audit trail for authentication, employee changes, and backup downloads.
- SQL backup download; manual restore instructions.

## Install (XAMPP)
1. Copy the project to C:\xampp\htdocs\hr-records.
2. Start Apache and MySQL in XAMPP.
3. Open http://localhost/phpmyadmin and import schema.sql.
4. Check config.php database settings. Defaults are host 127.0.0.1, database hr_records, user root, blank password (local XAMPP only).
5. Ensure PHP extensions mysqli and fileinfo are enabled and uploads/ is writable.
6. Visit http://localhost/hr-records/install.php and create the first admin with a unique password of at least 12 characters.
7. Sign in at http://localhost/hr-records/login.php. Delete install.php or restrict access after setup.

## Roles
- admin: employee management, audit logs, backup.
- hr: employee management and reports.
- viewer: read-only employee records and reports.
This starter does not yet include a user management page. Additional accounts require an administrator-reviewed SQL operation; create password hashes with PHP password_hash(), never plaintext.

## Testing checklist
1. Import schema and confirm users, employees, audit_logs tables exist.
2. Create admin and test successful and failed login.
3. Add employee and confirm dashboard/list counts update.
4. Try duplicate employee number and confirm it is rejected.
5. Edit record and upload valid JPG/PNG/WEBP photo and PDF/JPG/PNG document; test invalid type and >5 MB file.
6. Search by name/employee number/department and filter status.
7. Generate filtered report and Print / Save as PDF.
8. Check audit logs for login and employee create/update/delete.
9. Download SQL backup and confirm it is non-empty; back up uploads separately.
10. Test viewer role cannot modify records or access admin pages.
11. Logout and confirm protected pages redirect to login.

## Production and privacy notes
This is a starter application, not a certified HRIS. Employee data is sensitive. Before production: use HTTPS and least-privilege database credentials; add rate limiting, user management, pagination, retention controls, and tested automated backups; store uploads outside the public web root and deliver through authenticated download endpoints; configure regular off-site encrypted backups; review Philippine Data Privacy Act (RA 10173) obligations with your Data Protection Officer. Never commit real employee data, production credentials, or backups to GitHub.

## Schema
- users: identity, password hash, role and active flag.
- employees: employee profile and attachment paths.
- audit_logs: actions, user, entity, IP and timestamp.