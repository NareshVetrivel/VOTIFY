/* ==========================================================
   VOTIFY
   Database Creation Script
========================================================== */

/* ==========================================
   CREATE DATABASE
========================================== */

CREATE DATABASE IF NOT EXISTS votify_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;


/* ==========================================
   USE DATABASE
========================================== */

USE votify_db;


/* ==========================================
   DROP OLD TABLE (Optional)
========================================== */

DROP TABLE IF EXISTS students;


/* ==========================================
   CREATE STUDENTS TABLE
========================================== */

CREATE TABLE students (

    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    full_name VARCHAR(100) NOT NULL,

    dob DATE NOT NULL,

    admission_no VARCHAR(30) NOT NULL UNIQUE,

    phone VARCHAR(10) NOT NULL UNIQUE,

    college_email VARCHAR(50) NOT NULL UNIQUE,

    department VARCHAR(10) NOT NULL,

    year VARCHAR(15) NOT NULL,

    gender VARCHAR(15) NOT NULL,

    password VARCHAR(255) NOT NULL,

    status ENUM(
        'Pending',
        'Approved'
    ) NOT NULL DEFAULT 'Pending',

    created_at TIMESTAMP
    DEFAULT CURRENT_TIMESTAMP

);

/* ==========================================
   CREATE ADMIN TABLE
========================================== */

CREATE TABLE admins (

    id INT AUTO_INCREMENT PRIMARY KEY,

    username VARCHAR(50) NOT NULL UNIQUE,

    password VARCHAR(255) NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

);

INSERT INTO admins
(
    username,
    password
)
VALUES
(
    'admin',
    '$2y$10$Rc.kNO6w5gzncOLa1/VM8.4EtGaJpWA2XzoOupTcj87UVz4umZBCq'
);

select * from admins;

/* ==========================================
   CREATE  ELECTION SETTINGS
========================================== */

CREATE TABLE election_settings (

    id INT PRIMARY KEY AUTO_INCREMENT,

    election_status ENUM('Ready','Started','Stopped')
    NOT NULL DEFAULT 'Ready',

    updated_at TIMESTAMP
    DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP

);

INSERT INTO election_settings
(election_status)

VALUES

('Ready');

/* ==========================================
   CREATE  ADMIN LOGS
========================================== */

CREATE TABLE admin_logs (

    id INT AUTO_INCREMENT PRIMARY KEY,

    admin_id INT NOT NULL,

    admin_username VARCHAR(100) NOT NULL,

    action VARCHAR(100) NOT NULL,

    description TEXT NOT NULL,

    ip_address VARCHAR(45) NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX (admin_id),

    INDEX (created_at)

);

INSERT INTO admin_logs (

    admin_id,
    admin_username,
    action,
    description,
    ip_address

)

VALUES (

    1,
    'Admin',
    'System Initialized',
    'Admin logging system has been initialized.',
    '127.0.0.1'

);

ALTER TABLE students

ADD COLUMN vote_status
ENUM(
'Voted',
'Unvoted'
)
NOT NULL
DEFAULT 'Unvoted';

	CREATE TABLE candidates (

		id INT AUTO_INCREMENT PRIMARY KEY,

		student_id INT NOT NULL UNIQUE,

		admission_no VARCHAR(30) NOT NULL,

		full_name VARCHAR(100) NOT NULL,

		department VARCHAR(100) NOT NULL,

		year ENUM('1st Year','2nd Year') NOT NULL,

		manifesto VARCHAR(255) DEFAULT NULL,

		photo VARCHAR(255) NOT NULL,

		status ENUM('Active','Inactive')
		DEFAULT 'Active',

		created_at TIMESTAMP
		DEFAULT CURRENT_TIMESTAMP,

		updated_at TIMESTAMP
		DEFAULT CURRENT_TIMESTAMP
		ON UPDATE CURRENT_TIMESTAMP,

		CONSTRAINT fk_candidate_student
		FOREIGN KEY (student_id)
		REFERENCES students(id)
		ON DELETE CASCADE

	);

    ALTER TABLE candidates
MODIFY COLUMN year ENUM('I Year','II Year') NOT NULL;

UPDATE election_settings
SET updated_at = DATE_SUB(NOW(), INTERVAL 2 HOUR)
WHERE id = 1;