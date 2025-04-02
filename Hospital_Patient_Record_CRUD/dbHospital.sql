CREATE DATABASE dbhospital;

USE dbhospital;

CREATE TABLE patients (
    patient_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL ,
    date_of_birth DATE NOT NULL,
    contact_number VARCHAR(20) NOT NULL,
    email VARCHAR(100) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    blood_type VARCHAR(3) DEFAULT NULL,
    admission_date DATE NOT NULL,
    discharge_date DATE DEFAULT NULL,
    isActive TINYINT(1) DEFAULT 1
);