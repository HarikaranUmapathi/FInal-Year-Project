<?php

require_once '../config/db.php';
session_start();
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name     = trim($_POST['Name']);
    $password = trim($_POST['Password']);
    $role     = $_POST['Role'];

    if ($name != '' && $password != '') {

        $sql = "SELECT * FROM student1 WHERE name=? AND role=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $name, $role);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {

            // PASSWORD CHECK
            if (password_verify($password, $row['Password'])) { 
                $_SESSION['User'] = $row['Name'];
                $_SESSION['Role'] = $row['Role'];
                
                header("Location: Department.php");
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