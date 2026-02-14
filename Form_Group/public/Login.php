<?php
require_once '../config/db.php';
session_start();



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name     = trim($_POST['Name']);
    $password = trim($_POST['Password']);
    $_SESSION['name'] = $name;
    if ($name !== '' && $password !== '') {

        $row = null;

        // 1️⃣ First check student table
        $stmt = $conn->prepare("SELECT * FROM students WHERE Name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        // 2️⃣ If not found, check faculty table
        if (!$row) {
            $stmt = $conn->prepare("SELECT * FROM faculty WHERE Name = ?");
            $stmt->bind_param("s", $name);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
        }

        // 3️⃣ If user found and password matches
        if ($row && password_verify($password, $row['password'])) {

            $_SESSION['User'] = $row['name'];
            $_SESSION['Role'] = $row['role'];

            // 4️⃣ Role-based redirect
            switch ($row['role']) {
                case 'Student':
                    header("Location: Department.html");
                    break;

                case 'Faculty':
                    header("Location: Department.html");
                    break;

                case 'Admin':
                    header("Location: Admin.html");
                    break;

                default:
                    echo "<script>alert('Invalid role in database');</script>";
                    break;
            }
            exit();

        } else {
            echo "<script>alert('Invalid username or password');</script>";
        }

    } else {
        echo "<script>alert('Please fill in all fields');</script>";
    }
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