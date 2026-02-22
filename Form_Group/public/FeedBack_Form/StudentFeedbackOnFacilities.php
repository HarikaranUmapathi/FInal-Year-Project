<?php
require_once __DIR__ . '/../../config/db.php';
session_start();

$success = false;
$message = "";

// Create table
$sql = "CREATE TABLE IF NOT EXISTS feedback_facilities (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255),
  name VARCHAR(255),
  branch VARCHAR(100),
  year VARCHAR(50),
  section VARCHAR(50),
  reg_no VARCHAR (50),
  mobile VARCHAR(20),
  feedback_type VARCHAR(50),
  q1 INT, q2 INT, q3 INT, q4 INT, q5 INT, q6 INT,
  q7 INT, q8 INT, q9 INT, q10 INT, q11 INT, q12 INT,
  submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conn, $sql);

$questions = [
    "Classroom facilities",
    "Laboratory facilities",
    "Library facilities",
    "Internet & Wi-Fi",
    "Reference books",
    "Faculty support",
    "Canteen hygiene",
    "Drinking water quality",
    "Washroom cleanliness",
    "Sports facilities",
    "Campus safety",
    "Overall infrastructure"
];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $name = trim($_POST['name'] ?? ($_SESSION['User'] ?? ''));
    $branch = trim($_POST['branch'] ?? ($_SESSION['dept'] ?? ''));
    $year = trim($_POST['year'] ?? ($_SESSION['year'] ?? ''));
    $section = trim($_POST['section'] ?? ($_SESSION['section'] ?? ''));
    $reg_no = trim($_POST['reg_no'] ?? ($_SESSION['reg_no'] ?? ''));
    $mobile = trim($_POST['mobile'] ?? '');
    $feedback_type = 'Facilities';

    $answers = [];
    for ($i = 1; $i <= 12; $i++) {
        $answers[$i] = intval($_POST["q$i"] ?? 0);
    }

    if (!$email || !$name || !$branch || !$year || !$mobile || in_array(0, $answers, true)) {
        $message = "Please fill all required fields";
        $success = false;
    } else {
        $stmt = mysqli_prepare($conn,
            "INSERT INTO feedback_facilities
            (email,name,branch,year,section,reg_no,feedback_type,mobile,q1,q2,q3,q4,q5,q6,q7,q8,q9,q10,q11,q12)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssssssssiiiiiiiiiiii",
            $email, $name, $branch, $year, $section, $reg_no, $feedback_type, $mobile,
            $answers[1], $answers[2], $answers[3], $answers[4], $answers[5], $answers[6],
            $answers[7], $answers[8], $answers[9], $answers[10], $answers[11], $answers[12]
        );

        if (mysqli_stmt_execute($stmt)) {
            $success = true;
            $message = "✅ Feedback submitted successfully!";
        } else {
            $message = "❌ Database error";
            $success = false;
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Facilities Feedback Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .header h1 {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .header p {
            font-size: 1rem;
            opacity: 0.95;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 1rem;
            margin-bottom: 3rem;
        }
        
        .card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            padding: 2.5rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }
        
        .required {
            color: #ef4444;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
            font-family: inherit;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background-color: #f9f7ff;
        }
        
        .rating-group {
            background: #f9fafb;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            border-left: 4px solid #667eea;
        }
        
        .rating-question {
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
            font-size: 0.95rem;
        }
        
        .rating-options {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .rating-options input[type="radio"] {
            display: none;
        }
        
        .rating-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border: 2px solid #e5e7eb;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 700;
            background: white;
            color: #666;
        }
        
        .rating-badge:hover {
            border-color: #667eea;
            transform: scale(1.05);
        }
        
        .rating-options input[type="radio"]:checked + .rating-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: #667eea;
        }
        
        .section-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #333;
            margin: 2rem 0 1.5rem 0;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #667eea;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-weight: 500;
            border-left: 4px solid;
        }
        
        .alert-success {
            background: #d1fae5;
            border-color: #10b981;
            color: #047857;
        }
        
        .alert-error {
            background: #fee2e2;
            border-color: #ef4444;
            color: #b91c1c;
        }
        
        .btn-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .btn {
            flex: 1;
            padding: 0.9rem 2rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: #e5e7eb;
            color: #333;
        }
        
        .btn-secondary:hover {
            background: #d1d5db;
        }
        
        .back-link {
            display: inline-block;
            color: white;
            text-decoration: none;
            font-weight: 600;
            margin-top: 1rem;
            transition: all 0.2s;
        }
        
        .back-link:hover {
            opacity: 0.8;
        }
        
        @media (max-width: 768px) {
            .header h1 {
                font-size: 1.8rem;
            }
            
            .card {
                padding: 1.5rem;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .btn-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏢 Facilities Feedback Form</h1>
        <p>Help us improve our campus facilities and infrastructure</p>
    </div>

    <div class="container">
        <div class="card">
            <?php if ($message): ?>
                <div class="alert <?= $success ? 'alert-success' : 'alert-error' ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form method="POST" onsubmit="return validateForm()">
                <!-- Student Details Section -->
                <h2 class="section-title">👨‍🎓 Your Details</h2>
                
                <div class="form-group">
                    <label for="email">Email <span class="required">*</span></label>
                    <input type="email" id="email" name="email" required placeholder="your.email@example.com">
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Student Name <span class="required">*</span></label>
                        <input type="text" id="name" name="name" required placeholder="Full name">
                    </div>

                    <div class="form-group">
                        <label for="branch">Branch/Department <span class="required">*</span></label>
                        <input type="text" id="branch" name="branch" required placeholder="e.g., CSE, ECE">
                    </div>

                    <div class="form-group">
                        <label for="year">Year <span class="required">*</span></label>
                        <select id="year" name="year" required>
                            <option value="">Select Year</option>
                            <option value="1st Year">1st Year</option>
                            <option value="2nd Year">2nd Year</option>
                            <option value="3rd Year">3rd Year</option>
                            <option value="4th Year">4th Year</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="mobile">Mobile No. <span class="required">*</span></label>
                        <input type="tel" id="mobile" name="mobile" required placeholder="10-digit mobile number">
                    </div>
                </div>

                <!-- Feedback Questions Section -->
                <h2 class="section-title">⭐ Please Rate the Following (1-5)</h2>
                <p style="color: #666; margin-bottom: 1.5rem; font-size: 0.9rem;">1 = Poor/Unsatisfactory, 5 = Excellent</p>

                <?php foreach ($questions as $i => $q): ?>
                    <div class="rating-group">
                        <div class="rating-question"><?= ($i+1) ?>. <?= htmlspecialchars($q) ?> <span class="required">*</span></div>
                        <div class="rating-options">
                            <?php for ($j = 1; $j <= 5; $j++): ?>
                                <input type="radio" id="q<?= ($i+1) ?>_<?= $j ?>" name="q<?= ($i+1) ?>" value="<?= $j ?>" required>
                                <label for="q<?= ($i+1) ?>_<?= $j ?>" class="rating-badge"><?= $j ?></label>
                            <?php endfor; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">Submit Feedback</button>
                    <button type="reset" class="btn btn-secondary">Clear Form</button>
                </div>
            </form>

            <a href="../../FormPage/Forms.html" class="back-link">← Back to Forms List</a>
        </div>
    </div>

    <script>
        function validateForm() {
            let allAnswered = true;
            for (let i = 1; i <= 12; i++) {
                const checked = document.querySelector(`input[name="q${i}"]:checked`);
                if (!checked) {
                    allAnswered = false;
                    break;
                }
            }
            
            if (!allAnswered) {
                alert('Please rate all facilities');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
