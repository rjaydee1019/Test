CREATE DATABASE IF NOT EXISTS hr_records CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hr_records;
CREATE TABLE users (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 username VARCHAR(60) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 full_name VARCHAR(150) NOT NULL,
 role ENUM('admin','hr','viewer') NOT NULL DEFAULT 'viewer',
 is_active TINYINT(1) NOT NULL DEFAULT 1,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
CREATE TABLE employees (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 employee_no VARCHAR(40) NOT NULL UNIQUE,
 first_name VARCHAR(100) NOT NULL, middle_name VARCHAR(100), last_name VARCHAR(100) NOT NULL, suffix VARCHAR(20),
 sex ENUM('Male','Female','Other','Prefer not to say') NULL, birth_date DATE NULL,
 email VARCHAR(190), phone VARCHAR(40), address TEXT, department VARCHAR(120), position_title VARCHAR(120),
 employment_status ENUM('Permanent','Temporary','Contractual','Casual','Job Order','Contract of Service','Resigned','Retired','Separated') NOT NULL DEFAULT 'Permanent',
 date_hired DATE NULL, salary DECIMAL(12,2) NULL, photo_path VARCHAR(255), document_path VARCHAR(255), notes TEXT,
 created_by INT UNSIGNED NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
 INDEX idx_employee_name(last_name,first_name), INDEX idx_employee_department(department), INDEX idx_employee_status(employment_status)
) ENGINE=InnoDB;
CREATE TABLE audit_logs (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, user_id INT UNSIGNED NULL, username VARCHAR(60) NOT NULL,
 action VARCHAR(80) NOT NULL, entity_type VARCHAR(80), entity_id VARCHAR(80), details TEXT, ip_address VARCHAR(45),
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX idx_audit_created(created_at),
 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;