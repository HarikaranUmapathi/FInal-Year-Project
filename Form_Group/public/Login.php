<?php
require_once '../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name     = trim($_POST['Name']);
    $password = trim($_POST['Password']);
    $role     = trim($_POST['Role']); // Role selected from form

    if ($name !== '' && $password !== '' && $role !== '') {

        // Determine which table to check based on role
        if ($role === 'Faculty') {
            $table = 'faculty1';
        } else {
            $table = 'student1';
        }

        // Prepare statement
        $sql = "SELECT * FROM $table WHERE Name = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $name);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        echo "<script>console.log('Password from DB: " . $row['Password'] . "');</script>";

        if ($row = mysqli_fetch_assoc($result)) {
            echo "<script>console.log('Password from DB: " . $row['Password'] . "');</script>";
            // Check password (bcrypt)
            if (password_verify($password, $row['Password'])) {

                // Set session
                $_SESSION['User'] = $row['Name'];
                $_SESSION['Role'] = $row['Role'];

                // Role-based redirect
                switch ($row['Role']) {
                    case 'Student':
                        header("Location: Department.html");
                        break;

                    case 'Faculty':
                        header("Location: Faculty.html");
                        break;

                    case 'Admin':
                        header("Location: Admin.html");
                        break;

                    default:
                        $error = "Invalid role in database";
                        break;
                }
                exit();

            } else {
                $error = "Invalid password";
            }

        } else {
            $error = "User not found";
        }

    } else {
        $error = "Please fill in all fields";
    }
}

// Display error if exists
if (isset($error)) {
    echo "<script>alert('$error');</script>";
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>Document</title>
</head>
<body>
    <form  method="post"  class="flex justify-center min-h-screen items-center">
        <div class="w-[250px] h-[300px] border-2 border-blue-400 flex flex-col gap-4 p-4 justify-center items-center">
            <h1 class="text-green-600 text-2xl">Login</h1>
            <div>
                <label>Name</label>
                <input class="border-2 border-red-400" type="text" required name="Name">
            </div>
            <div>
                <label>Password</label>
                <input type="password" class="border-2 border-red-400"  required name="Password">
            </div>
            <div class="flex flex-col pr-[25px]">
                <label>Role</label>
                <select name="Role" class="border-2 border-red-500 w-[190px]">
                    <option value="Faculty">Faculty</option>
                    <option value="Student">Student</option>
                    <option value="Admin">Admin</option>
                </select>
            </div>
            <div>
                <button type="submit" class="border-2 border-blue-400 p-1 bg-blue-700">Login</button>
            </div>
        </div>
        
    </form>
    
</body>
</html>