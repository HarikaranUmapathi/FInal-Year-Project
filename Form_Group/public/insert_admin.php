<?php
require_once __DIR__ . '/../config/db.php';

// Sample Admin
$adminName = 'Admin User';
$adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
$adminRole = 'Admin';
$adminDept = 'Admin';

$stmt = $conn->prepare("INSERT INTO faculty (name, password, role, dept) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $adminName, $adminPassword, $adminRole, $adminDept);
$stmt->execute();

echo "Admin user inserted successfully.";
?>