<?php
require_once __DIR__ . '/../config/db.php';
session_start();



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name     = trim($_POST['Name']);
    $password = trim($_POST['Password']);
    $_SESSION['name'] = $name;
    if ($name !== '' && $password !== '') {

        $row = null;

        // 1️⃣ First check student table
        $stmt = $conn->prepare("SELECT * FROM student WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        // 2️⃣ If not found, check faculty table
        if (!$row) {
            $stmt = $conn->prepare("SELECT * FROM faculty WHERE name = ?");
            $stmt->bind_param("s", $name);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
        }

        // 3️⃣ If user found and password matches
        if ($row && password_verify($password, $row['password'])) {

            $_SESSION['User'] = $row['name'];
            $_SESSION['Role'] = $row['role'];
            // Store additional details for Students
            if ($row['role'] === 'Student') {
                $_SESSION['dept'] = $row['dept'] ?? ''; // Column is dept
                $_SESSION['year'] = $row['years'] ?? '';   // Column is years
                $_SESSION['section'] = $row['section'] ?? '';
                $_SESSION['reg_no'] = $row['reg_no'] ?? '';
            } else {
                // For Faculty/Admin, we might process differently
                 $_SESSION['dept'] = $row['dept'] ?? '';
            }

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
    <title>Login - Feedback System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            background: white;
            border-radius: 16px;
            padding: 3rem 2rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            margin: 2rem;
        }

        .title {
            font-size: 2rem;
            font-weight: 700;
            color: #1f2937;
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            background: white;
            transition: all 0.3s;
        }

        .select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .login-btn {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 1rem;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(102, 126, 234, 0.4);
        }

        .subtitle {
            text-align: center;
            color: #6b7280;
            font-size: 0.875rem;
            margin-top: 1.5rem;
        }

        @media (max-width: 640px) {
            .container {
                padding: 2rem 1.5rem;
                margin: 1rem;
            }

            .title {
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="title">Welcome Back</h1>
        <form method="post">
            <div class="form-group">
                <label class="label" for="name">Username</label>
                <input type="text" id="name" name="Name" class="input" required placeholder="Enter your username">
            </div>

            <div class="form-group">
                <label class="label" for="password">Password</label>
                <input type="password" id="password" name="Password" class="input" required placeholder="Enter your password">
            </div>

            <div class="form-group">
                <label class="label" for="role">Login As</label>
                <select name="Role" id="role" class="select" required>
                    <option value="Student">Student</option>
                    <option value="Faculty">Faculty</option>
                    <option value="Admin">Admin</option>
                </select>
            </div>

            <button type="submit" class="login-btn">Sign In</button>
        </form>

        <p class="subtitle">Access your personalized feedback dashboard</p>
    </div>
</body>
</html>