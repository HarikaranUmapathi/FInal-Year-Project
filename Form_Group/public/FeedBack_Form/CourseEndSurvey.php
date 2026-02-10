<?php
/* ================= DATABASE CONNECTION ================= */
$conn = mysqli_connect("localhost", "root", "");
if (!$conn) {
    die("❌ MySQL connection failed");
}

/* ================= CREATE DATABASE ================= */
mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS feedback_db");
mysqli_select_db($conn, "feedback_db");

/* ================= CREATE TABLE ================= */
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS course_end_survey (
    id INT AUTO_INCREMENT PRIMARY KEY,

    student_name VARCHAR(100),
    faculty_name VARCHAR(100),
    department VARCHAR(100),
    year_sem VARCHAR(50),

    objective TEXT,
    outcome TEXT,

    q1 INT, q2 INT, q3 INT, q4 INT, q5 INT,
    q6 INT, q7 INT, q8 INT, q9 INT, q10 INT,

    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$message = "";

/* ================= QUESTIONS ================= */
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

/* ================= FORM SUBMISSION ================= */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $student   = $_POST['student_name'];
    $faculty   = $_POST['faculty_name'];
    $dept      = $_POST['department'];
    $year      = $_POST['year_sem'];
    $objective = $_POST['objective'];
    $outcome   = $_POST['outcome'];

    $ans = [];
    for ($i = 1; $i <= 10; $i++) {
        $ans[$i] = $_POST["q$i"];
    }

    $stmt = mysqli_prepare($conn,
        "INSERT INTO course_end_survey
        (student_name,faculty_name,department,year_sem,objective,outcome,
         q1,q2,q3,q4,q5,q6,q7,q8,q9,q10)
        VALUES (?,?,?,?,?,
                ?,?,?,?,?,?,?,?,?,?,?)"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "ssssssiiiiiiiiii",
        $student, $faculty, $dept, $year, $objective, $outcome,
        $ans[1], $ans[2], $ans[3], $ans[4], $ans[5],
        $ans[6], $ans[7], $ans[8], $ans[9], $ans[10]
    );

    if (mysqli_stmt_execute($stmt)) {
        $message = "✅ Survey submitted successfully";
    } else {
        $message = "❌ Error submitting survey";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Course End Survey</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-serif">

<div class="max-w-5xl mx-auto bg-white p-6 mt-6 shadow">

<h2 class="text-center font-bold text-xl">COURSE END SURVEY</h2>

<?php if ($message): ?>
<p class="text-center font-semibold text-green-600 mt-2">
<?= $message ?>
</p>
<?php endif; ?>

<form method="post" class="mt-6 space-y-4">

<!-- BASIC DETAILS -->
<input name="student_name" required placeholder="Student Name" class="w-full border p-2">
<input name="faculty_name" required placeholder="Faculty Name" class="w-full border p-2">
<input name="department" required placeholder="Department" class="w-full border p-2">
<input name="year_sem" required placeholder="Year / Semester" class="w-full border p-2">

<!-- OBJECTIVE & OUTCOME -->
<textarea name="objective" rows="3"
 placeholder="General Objective of the Course"
 class="w-full border p-2"></textarea>

<textarea name="outcome" rows="5"
 placeholder="Course Outcomes"
 class="w-full border p-2"></textarea>

<p class="font-semibold">Course Evaluation (5 – Excellent | 1 – Poor)</p>

<!-- QUESTIONS -->
<?php foreach ($questions as $i => $q): ?>
<div class="border p-3">
    <p class="font-medium"><?= ($i+1) ?>. <?= $q ?></p>
    <?php for ($r = 5; $r >= 1; $r--): ?>
        <label class="mr-3">
            <input type="radio" name="q<?= $i+1 ?>" value="<?= $r ?>" required> <?= $r ?>
        </label>
    <?php endfor; ?>
</div>
<?php endforeach; ?>

<div class="text-center">
<button class="bg-blue-600 text-white px-6 py-2 mt-4 rounded">
Submit Survey
</button>
</div>

</form>
</div>

</body>
</html>
