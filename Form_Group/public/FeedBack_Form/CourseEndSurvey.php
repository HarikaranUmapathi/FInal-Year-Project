<?php
require_once __DIR__ . '/../../config/db.php';
session_start();

$message = "";
$success = false;

// Create table
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS course_end_survey (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(100),
    faculty_name VARCHAR(100),
    department VARCHAR(100),
    year_sem VARCHAR(50),
    objective TEXT,
    outcome TEXT,
    feedback_type VARCHAR(50),
    q1 INT, q2 INT, q3 INT, q4 INT, q5 INT,
    q6 INT, q7 INT, q8 INT, q9 INT, q10 INT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$questions = [
    "Course objectives were clearly defined",
    "Course syllabus was relevant to the program",
    "Course content was well organized",
    "Course material was adequate and useful",
    "Teaching methodology was effective",
    "Faculty explained concepts clearly",
    "Faculty encouraged student participation",
    "Assessment methods were fair",
    "Course improved analytical/problem-solving skills",
    "Overall satisfaction with the course"
];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $student_name = trim($_POST['student_name'] ?? ($_SESSION['User'] ?? ''));
    $faculty_name = trim($_POST['faculty_name'] ?? '');
    $department = trim($_POST['department'] ?? ($_SESSION['dept'] ?? ''));
    $year_sem = trim($_POST['year_sem'] ?? ($_SESSION['year'] ?? ''));
    $objective = trim($_POST['objective'] ?? '');
    $outcome = trim($_POST['outcome'] ?? '');
    $feedback_type = 'CourseEnd';

    $answers = [];
    for ($i = 1; $i <= 10; $i++) {
        $answers[$i] = intval($_POST["q$i"] ?? 0);
    }

    if (!$student_name || !$faculty_name || !$department || !$year_sem || in_array(0, $answers, true)) {
        $message = "Please fill all required fields";
        $success = false;
    } else {
        // Build parameter arrays programmatically
        $params = [$student_name, $faculty_name, $department, $year_sem, $objective, $outcome, $feedback_type];
        for ($i = 1; $i <= 10; $i++) {
            $params[] = $answers[$i];
        }

        // Build type string: 7 strings + 10 integers
        $types = 'sssssss' . str_repeat('i', 10);

        $stmt = $conn->prepare(
            "INSERT INTO course_end_survey
            (student_name, faculty_name, department, year_sem, objective, outcome, feedback_type,
             q1, q2, q3, q4, q5, q6, q7, q8, q9, q10)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        if (!$stmt) {
            die("Prepare error: " . $conn->error);
        }

        // Build reference array for binding
        array_unshift($params, $types);
        $refs = [];
        foreach ($params as $key => $value) {
            $refs[$key] = &$params[$key];
        }
        
        call_user_func_array([$stmt, 'bind_param'], $refs);

        if ($stmt->execute()) {
            $success = true;
            $message = "✅ Course survey submitted successfully";
        } else {
            $message = "❌ Database error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course End Survey</title>
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
        
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
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
        <h1>📚 Course End Survey</h1>
        <p>Your feedback helps improve course delivery and content</p>
    </div>

    <div class="container">
        <div class="card">
            <?php if ($message): ?>
                <div class="alert <?= $success ? 'alert-success' : 'alert-error' ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form method="POST" onsubmit="return validateForm()">
                <!-- Course Details Section -->
                <h2 class="section-title">📖 Course Information</h2>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="student_name">Student Name <span class="required">*</span></label>
                        <input type="text" id="student_name" name="student_name" required placeholder="Your full name">
                    </div>

                    <div class="form-group">
                        <label for="faculty_name">Faculty Name <span class="required">*</span></label>
                        <input type="text" id="faculty_name" name="faculty_name" required placeholder="Instructor's name">
                    </div>

                    <div class="form-group">
                        <label for="department">Department <span class="required">*</span></label>
                        <input type="text" id="department" name="department" required placeholder="e.g., CSE">
                    </div>

                    <div class="form-group">
                        <label for="year_sem">Year / Semester <span class="required">*</span></label>
                        <select id="year_sem" name="year_sem" required>
                            <option value="">Select Year/Semester</option>
                            <option value="I-I">I-I</option>
                            <option value="I-II">I-II</option>
                            <option value="II-I">II-I</option>
                            <option value="II-II">II-II</option>
                            <option value="III-I">III-I</option>
                            <option value="III-II">III-II</option>
                            <option value="IV-I">IV-I</option>
                            <option value="IV-II">IV-II</option>
                        </select>
                    </div>
                </div>

                <!-- Course Description Section -->
                <h2 class="section-title">📝 Course Details</h2>

                <div class="form-group">
                    <label for="objective">Course Objectives (Optional)</label>
                    <textarea id="objective" name="objective" placeholder="General objectives of this course..."></textarea>
                </div>

                <div class="form-group">
                    <label for="outcome">Course Outcomes (Optional)</label>
                    <textarea id="outcome" name="outcome" placeholder="Expected learning outcomes..."></textarea>
                </div>

                <!-- Rating Section -->
                <h2 class="section-title">⭐ Course Evaluation (Rate 1-5)</h2>
                <p style="color: #666; margin-bottom: 1.5rem; font-size: 0.9rem;">1 = Poor/Strongly Disagree, 5 = Excellent/Strongly Agree</p>

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
                    <button type="submit" class="btn btn-primary">Submit Survey</button>
                    <button type="reset" class="btn btn-secondary">Clear Form</button>
                </div>
            </form>

            <a href="../../FormPage/Forms.html" class="back-link">← Back to Forms List</a>
        </div>
    </div>

    <script>
        function validateForm() {
            let allAnswered = true;
            for (let i = 1; i <= 10; i++) {
                const checked = document.querySelector(`input[name="q${i}"]:checked`);
                if (!checked) {
                    allAnswered = false;
                    break;
                }
            }
            
            if (!allAnswered) {
                alert('Please rate all questions');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
