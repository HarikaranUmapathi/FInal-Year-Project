-- Create database if not exists
CREATE DATABASE IF NOT EXISTS users_detail;

-- Use the database
USE users_detail;

-- Create students table
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL,
    dept VARCHAR(100),
    years VARCHAR(10),
    section VARCHAR(10),
    reg_no VARCHAR(50)
);

-- Create faculty table
CREATE TABLE IF NOT EXISTS faculty (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL,
    dept VARCHAR(100)
);