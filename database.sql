CREATE DATABASE IF NOT EXISTS lvhs_enrollment CHARACTER
SET
    utf8mb4;

USE lvhs_enrollment;

CREATE TABLE
    IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(120) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        role ENUM ('student', 'registrar') DEFAULT 'student',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

CREATE TABLE
    IF NOT EXISTS students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT UNIQUE NOT NULL,
        lrn VARCHAR(12),
        first_name VARCHAR(60),
        middle_name VARCHAR(60),
        last_name VARCHAR(60),
        sex VARCHAR(10),
        birthdate DATE,
        contact VARCHAR(20),
        address TEXT,
        guardian_name VARCHAR(120),
        relationship VARCHAR(40),
        guardian_contact VARCHAR(20),
        guardian_address TEXT,
        grade_level VARCHAR(10),
        student_type VARCHAR(20),
        school_year VARCHAR(9),
        prev_school VARCHAR(120),
        prev_school_address TEXT,
        status ENUM ('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
        remarks VARCHAR(255),
        photo VARCHAR(255) NULL,
        submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
    );

CREATE TABLE
    IF NOT EXISTS documents (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        doc_type VARCHAR(40),
        file_name VARCHAR(255),
        file_path VARCHAR(255),
        uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
    );

CREATE TABLE
    IF NOT EXISTS messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(80),
        email VARCHAR(120),
        message TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );