<?php
require_once __DIR__ . '/../../config/db.php';
session_start();

$studentNameValue = '';
if (isset($_SESSION['name']) && $_SESSION['Role'] === 'Student') {
    $studentNameValue = $_SESSION['name'];
}

$success = false;
$message = "";

// Ensure table exists
$conn->query("
CREATE TABLE IF NOT EXISTS guest_feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    guest_name VARCHAR(100),
    designation VARCHAR(100),
    organization VARCHAR(200),
    subject VARCHAR(200),
    objective TEXT,
    student_name VARCHAR(100),
    section VARCHAR(50),
    reg_no VARCHAR(50),
    feedback_type VARCHAR(50),
    q1 INT,
    q2 INT,
    q3 INT,
    q4 INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
");

/* Handle form submission */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $guestName = trim($_POST['guestName'] ?? '');
    $designation = trim($_POST['designation'] ?? '');
    $org = trim($_POST['org'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $objective = trim($_POST['objective'] ?? '');
    $studentName = trim($_POST['studentName'] ?? '');
    $q1 = intval($_POST['q1'] ?? 0);
    $q2 = intval($_POST['q2'] ?? 0);
    $q3 = intval($_POST['q3'] ?? 0);
    $q4 = intval($_POST['q4'] ?? 0);
    $section = '';
    $reg_no = '';
    $feedback_type = 'Guest';

    if (!$guestName || !$designation || !$org || !$subject || !$studentName || !$q1 || !$q2 || !$q3 || !$q4) {
        $message = "Please fill all required fields";
        $success = false;
    } else {
        $stmt = $conn->prepare("INSERT INTO guest_feedback
        (guest_name,designation,organization,subject,objective,student_name,section,reg_no,feedback_type,q1,q2,q3,q4)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");

        $stmt->bind_param("sssssssssiiii",
            $guestName,
            $designation,
            $org,
            $subject,
            $objective,
            $studentName,
            $section,
            $reg_no,
            $feedback_type,
            $q1,
            $q2,
            $q3,
            $q4
        );

        if ($stmt->execute()) {
            $message = "✅ Feedback submitted successfully!";
            $success = true;
        } else {
            $message = "❌ Error submitting feedback";
            $success = false;
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
    <title>Guest Lecture Feedback Form</title>
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
            max-width: 800px;
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
            min-height: 100px;
        }
        
        .rating-group {
            background: #f9fafb;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            border-left: 4px solid #667eea;
        }
        
        .rating-group > label {
            display: block;
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
        }
        
        .rating-options {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }
        
        .rating-options input[type="radio"] {
            display: none;
        }
        
        .rating-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 700;
            background: white;
            color: #666;
        }
        
        .rating-badge:hover {
            border-color: #667eea;
            transform: scale(1.05);
            background: #f0f4ff;
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
            
            .btn-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎤 Guest Lecture Feedback Form</h1>
        <p>Share your valuable feedback about the guest lecture</p>
    </div>

    <div class="container">
        <div class="card">
            <?php if ($message): ?>
                <div class="alert <?= $success ? 'alert-success' : 'alert-error' ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form method="POST" onsubmit="return validateForm()">
                <!-- Guest Details Section -->
                <h2 class="section-title">👤 Guest Speaker Details</h2>
                
                <div class="form-group">
                    <label for="guestName">Guest Name <span class="required">*</span></label>
                    <input type="text" id="guestName" name="guestName" required placeholder="Enter guest speaker's full name">
                </div>

                <div class="form-group">
                    <label for="designation">Designation <span class="required">*</span></label>
                    <input type="text" id="designation" name="designation" required placeholder="e.g., Senior Software Engineer, Manager">
                </div>

                <div class="form-group">
                    <label for="org">Organization <span class="required">*</span></label>
                    <input type="text" id="org" name="org" required placeholder="Enter organization name">
                </div>

                <div class="form-group">
                    <label for="subject">Subject/Topic <span class="required">*</span></label>
                    <input type="text" id="subject" name="subject" required placeholder="What was the lecture topic about?">
                </div>

                <div class="form-group">
                    <label for="objective">Objective of Session <span class="required">*</span></label>
                    <textarea id="objective" name="objective" required placeholder="Describe the main objective and purpose of this session"></textarea>
                </div>

                <!-- Student Details Section -->
                <h2 class="section-title">👨‍🎓 Your Details</h2>
                
                <div class="form-group">
                    <label for="studentName">Your Name <span class="required">*</span></label>
                    <input type="text" id="studentName" name="studentName" required placeholder="Your full name" value="<?= htmlspecialchars($studentNameValue) ?>">
                </div>

                <!-- Feedback Questions Section -->
                <h2 class="section-title">⭐ Please Rate the Following (1-5)</h2>
                <p style="color: #666; margin-bottom: 1.5rem; font-size: 0.9rem;">1 = Poor, 5 = Excellent</p>

                <div class="rating-group">
                    <label>Content Relevance <span class="required">*</span></label>
                    <div class="rating-options">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <input type="radio" id="q1_<?= $i ?>" name="q1" value="<?= $i ?>" required>
                            <label for="q1_<?= $i ?>" class="rating-badge"><?= $i ?></label>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="rating-group">
                    <label>Delivery Quality <span class="required">*</span></label>
                    <div class="rating-options">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <input type="radio" id="q2_<?= $i ?>" name="q2" value="<?= $i ?>" required>
                            <label for="q2_<?= $i ?>" class="rating-badge"><?= $i ?></label>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="rating-group">
                    <label>Industry Insights <span class="required">*</span></label>
                    <div class="rating-options">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <input type="radio" id="q3_<?= $i ?>" name="q3" value="<?= $i ?>" required>
                            <label for="q3_<?= $i ?>" class="rating-badge"><?= $i ?></label>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="rating-group">
                    <label>Overall Satisfaction <span class="required">*</span></label>
                    <div class="rating-options">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <input type="radio" id="q4_<?= $i ?>" name="q4" value="<?= $i ?>" required>
                            <label for="q4_<?= $i ?>" class="rating-badge"><?= $i ?></label>
                        <?php endfor; ?>
                    </div>
                </div>

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
            const q1 = document.querySelector('input[name="q1"]:checked');
            const q2 = document.querySelector('input[name="q2"]:checked');
            const q3 = document.querySelector('input[name="q3"]:checked');
            const q4 = document.querySelector('input[name="q4"]:checked');
            
            if (!q1 || !q2 || !q3 || !q4) {
                alert('Please rate all questions');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
