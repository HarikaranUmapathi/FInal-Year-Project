<?php
require_once '../config/db.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['Role']) || $_SESSION['Role'] !== 'Admin') {
    header("Location: Login.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_student'])) {
        $name = trim($_POST['student_name']);
        $password = password_hash(trim($_POST['student_password']), PASSWORD_DEFAULT);
        $dept = trim($_POST['student_dept']);
        $years = trim($_POST['student_years']);
        $section = trim($_POST['student_section']);
        $reg_no = trim($_POST['student_reg_no']);
        $role = 'Student';

        $stmt = $conn->prepare("INSERT INTO students (name, password, role, dept, years, section, reg_no) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $name, $password, $role, $dept, $years, $section, $reg_no);
        if ($stmt->execute()) {
            $message = "Student added successfully!";
        } else {
            $message = "Error adding student.";
        }
    } elseif (isset($_POST['add_faculty'])) {
        $name = trim($_POST['faculty_name']);
        $password = password_hash(trim($_POST['faculty_password']), PASSWORD_DEFAULT);
        $dept = trim($_POST['faculty_dept']);
        $role = 'Faculty';

        $stmt = $conn->prepare("INSERT INTO faculty (name, password, role, dept) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $password, $role, $dept);
        if ($stmt->execute()) {
            $message = "Faculty added successfully!";
        } else {
            $message = "Error adding faculty.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Add Users - Admin</title>
</head>
<body class="bg-gray-100">

<div class="max-w-4xl mx-auto bg-white p-6 mt-6 shadow">

<h2 class="text-center font-bold text-xl mb-4">Add Users</h2>

<?php if($message){ ?>
<p class="text-green-600 text-center font-semibold mb-4"><?= $message ?></p>
<?php } ?>

<!-- Add Student Form -->
<div class="mb-8">
<h3 class="font-bold text-lg mb-2">Add Student</h3>
<form method="POST" class="space-y-4">
    <input name="student_name" placeholder="Student Name" class="border p-2 w-full" required>
    <input name="student_password" placeholder="Password" type="password" class="border p-2 w-full" required>
    <input name="student_dept" placeholder="Department" class="border p-2 w-full" required>
    <input name="student_years" placeholder="Year" class="border p-2 w-full" required>
    <input name="student_section" placeholder="Section" class="border p-2 w-full" required>
    <input name="student_reg_no" placeholder="Registration Number" class="border p-2 w-full" required>
    <button type="submit" name="add_student" class="bg-blue-600 text-white px-6 py-2 rounded">Add Student</button>
</form>
</div>

<!-- Add Faculty Form -->
<div>
<h3 class="font-bold text-lg mb-2">Add Faculty</h3>
<form method="POST" class="space-y-4">
    <input name="faculty_name" placeholder="Faculty Name" class="border p-2 w-full" required>
    <input name="faculty_password" placeholder="Password" type="password" class="border p-2 w-full" required>
    <input name="faculty_dept" placeholder="Department" class="border p-2 w-full" required>
    <button type="submit" name="add_faculty" class="bg-green-600 text-white px-6 py-2 rounded">Add Faculty</button>
</form>
</div>

<a href="Admin.html" class="text-blue-600 mt-6 block font-semibold">← Back to Admin Dashboard</a>

</div>

</body>
</html>