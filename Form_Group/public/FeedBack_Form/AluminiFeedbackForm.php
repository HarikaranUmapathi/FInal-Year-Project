<?php
/* ================= DATABASE CONNECTION ================= */
require_once __DIR__ . '/../../config/db.php';
session_start();

$success = false;
$message = "";

/* ================= CREATE TABLE ================= */
$sql = "CREATE TABLE IF NOT EXISTS alumni_feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255),
    name VARCHAR(255),
    branch VARCHAR(100),
    pass_year VARCHAR(20),
    mobile VARCHAR(20),

    q1 INT, q2 INT, q3 INT, q4 INT, q5 INT,
    q6 INT, q7 INT, q8 INT, q9 INT, q10 INT,
    q11 INT, q12 INT, q13 INT, q14 INT, q15 INT,
    q16 INT, q17 INT, q18 INT, q19 INT, q20 INT,
    q21 INT, q22 INT,

    suggestion TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conn, $sql);

/* ================= QUESTIONS ================= */
$questions = [
    "Graduates succeed in their careers",
    "Graduates pursue higher studies",
    "Graduates contribute via entrepreneurship",
    "Students apply engineering knowledge",
    "Analyze complex problems",
    "Design sustainable solutions",
    "Conduct experiments and interpret data",
    "Apply modern engineering tools",
    "Address societal responsibilities",
    "Environmental awareness",
    "Ethical and professional responsibility",
    "Team work",
    "Communication skills",
    "Leadership qualities",
    "Life-long learning",
    "Use of CAE tools",
    "Exposure to advanced materials",
    "Department progress towards vision",
    "Mission alignment with vision",
    "Program objectives relevance",
    "PSOs alignment",
    "Confirmation of details"
];

/* ================= FORM SUBMISSION ================= */
$message = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST['email']);
    $name = trim($_POST['name']);
    $branch = trim($_POST['branch']);
    $pass_year = trim($_POST['pass_year']);
    $mobile = trim($_POST['mobile']);
    $suggestion = $_POST['suggestion'] ?? "";

    $answers = [];
    for ($i = 1; $i <= 22; $i++) {
        $answers[$i] = $_POST["q$i"] ?? null;
    }

    if (in_array(null, $answers, true)) {
        $message = "❌ Please answer all questions";
    } else {

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO alumni_feedback
            (email,name,branch,pass_year,mobile,
             q1,q2,q3,q4,q5,q6,q7,q8,q9,q10,
             q11,q12,q13,q14,q15,q16,q17,q18,q19,q20,q21,q22,
             suggestion)
            VALUES (?,?,?,?,?,
                    ?,?,?,?,?,?,?,?,?,?,
                    ?,?,?,?,?,?,?,?,?,?,?,?,
                    ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "sssssiiiiiiiiiiiiiiiiiiiiiis",
            $email,
            $name,
            $branch,
            $pass_year,
            $mobile,
            $answers[1], $answers[2], $answers[3], $answers[4], $answers[5],
            $answers[6], $answers[7], $answers[8], $answers[9], $answers[10],
            $answers[11], $answers[12], $answers[13], $answers[14], $answers[15],
            $answers[16], $answers[17], $answers[18], $answers[19], $answers[20],
            $answers[21], $answers[22],
            $suggestion
        );

        if (mysqli_stmt_execute($stmt)) {
            $success = true;
            $message = "✅ Feedback submitted successfully!";
        } else {
            $message = "❌ Database error: " . mysqli_stmt_error($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Alumni Feedback Form</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-serif">

<div class="max-w-5xl mx-auto bg-white p-6 mt-6 shadow">

<h2 class="text-center font-bold text-xl mb-2">
ALUMNI FEEDBACK FORM
</h2>

<?php if ($message): ?>
<p class="text-center font-semibold mb-4 <?= $success ? 'text-green-600' : 'text-red-600' ?>">
<?= htmlspecialchars($message) ?>
</p>
<?php endif; ?>

<form method="POST" class="space-y-3">

<input name="email" required placeholder="Email" class="w-full border p-2">
<input name="name" required placeholder="Alumni Name" class="w-full border p-2">
<input name="branch" required placeholder="Branch" class="w-full border p-2">
<input name="pass_year" required placeholder="Year of Passing" class="w-full border p-2">
<input name="mobile" required placeholder="Mobile Number" class="w-full border p-2">

<p class="font-semibold mt-4">Rate (5-Excellent to 1-Poor)</p>

<?php foreach ($questions as $i => $q): ?>
<div class="border p-3">
    <p class="font-medium"><?= ($i + 1) ?>. <?= htmlspecialchars($q) ?></p>
    <?php for ($r = 5; $r >= 1; $r--): ?>
        <label class="mr-4">
            <input type="radio" name="q<?= $i + 1 ?>" value="<?= $r ?>" required> <?= $r ?>
        </label>
    <?php endfor; ?>
</div>
<?php endforeach; ?>

<textarea name="suggestion" class="w-full border p-2 mt-3"
 placeholder="Suggestion if any"></textarea>

<div class="text-center">
<button class="bg-blue-600 text-white px-6 py-2 mt-4">
Submit Feedback
</button>
</div>

</form>

</div>

</body>
</html>
