<?php
require_once __DIR__ . '/config/db.php';

// SQL to create tables
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
    echo "Students table created successfully.<br>";
} else {
    echo "Error creating students table: " . mysqli_error($conn) . "<br>";
}

if (mysqli_query($conn, $sql_faculty)) {
    echo "Faculty table created successfully.<br>";
} else {
    echo "Error creating faculty table: " . mysqli_error($conn) . "<br>";
}

mysqli_close($conn);
?>