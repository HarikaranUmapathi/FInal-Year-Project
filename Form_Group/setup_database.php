<?php
require_once 'config/db.php';

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS users_detail";
if (mysqli_query($conn, $sql)) {
    echo "Database 'users_detail' created successfully.\n";
} else {
    echo "Error creating database: " . mysqli_error($conn) . "\n";
}

// Select the database
mysqli_select_db($conn, 'users_detail');

// Create tables
$sql_students = "CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL,
    dept VARCHAR(100),
    years VARCHAR(10),
    section VARCHAR(10),
    reg_no VARCHAR(50)
)";

$sql_faculty = "CREATE TABLE IF NOT EXISTS faculty (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL,
    dept VARCHAR(100)
)";

if (mysqli_query($conn, $sql_students)) {
    echo "Students table created successfully.\n";
} else {
    echo "Error creating students table: " . mysqli_error($conn) . "\n";
}

if (mysqli_query($conn, $sql_faculty)) {
    echo "Faculty table created successfully.\n";
} else {
    echo "Error creating faculty table: " . mysqli_error($conn) . "\n";
}

mysqli_close($conn);
?>