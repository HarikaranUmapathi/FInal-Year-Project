<?php
require_once __DIR__ . '/../config/db.php';

// Sample Student
$studentName = 'John Doe';
$studentPassword = password_hash('student123', PASSWORD_DEFAULT);
$studentRole = 'Student';
$studentDept = 'CSE';
$studentYears = '3';
$studentSection = 'A';
$studentRegNo = '12345';

$stmt = $conn->prepare("INSERT INTO students (name, password, role, dept, years, section, reg_no) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $studentName, $studentPassword, $studentRole, $studentDept, $studentYears, $studentSection, $studentRegNo);
$stmt->execute();

// Sample Faculty
$facultyName = 'Dr. Smith';
$facultyPassword = password_hash('faculty123', PASSWORD_DEFAULT);
$facultyRole = 'Faculty';
$facultyDept = 'CSE';

$stmt = $conn->prepare("INSERT INTO faculty (name, password, role, dept) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $facultyName, $facultyPassword, $facultyRole, $facultyDept);
$stmt->execute();

echo "Sample users inserted successfully.";
?>