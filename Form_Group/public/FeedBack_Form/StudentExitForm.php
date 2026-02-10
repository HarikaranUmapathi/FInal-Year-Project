<?php
require_once "../../config/db.php";

$message = "";
$success = false;

/* ===== CREATE TABLE ===== */
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS student_exit_feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255),
    name VARCHAR(255),
    branch VARCHAR(100),
    year VARCHAR(20),
    mobile VARCHAR(20),

    q1 INT, q2 INT, q3 INT, q4 INT, q5 INT,
    q6 INT, q7 INT, q8 INT, q9 INT, q10 INT,
    q11 INT, q12 INT,

    suggestion TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$questions = [
  "Syllabus coverage",
  "Quality of teaching",
  "Laboratory facilities",
  "Library facilities",
  "Industry exposure",
  "Placement support",
  "Career guidance",
  "Soft skill training",
  "Infrastructure",
  "Campus environment",
  "Administrative support",
  "Overall satisfaction"
];

/* ===== FORM SUBMIT ===== */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = $_POST['email'];
    $name = $_POST['name'];
    $branch = $_POST['branch'];
    $year = $_POST['year'];
    $mobile = $_POST['mobile'];
    $suggestion = $_POST['suggestion'];

    $answers = [];
    for ($i=1; $i<=12; $i++) {
        $answers[$i] = $_POST["q$i"] ?? null;
    }

    if (in_array(null, $answers, true)) {
        $message = "❌ Please answer all questions";
    } else {

        $stmt = mysqli_prepare($conn,
            "INSERT INTO student_exit_feedback
            (email,name,branch,year,mobile,
             q1,q2,q3,q4,q5,q6,q7,q8,q9,q10,q11,q12,
             suggestion)
            VALUES (?,?,?,?,?,
                    ?,?,?,?,?,?,?,?,?,?,?,
                    ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "sssssiiiiiiiiiiiis",
            $email,$name,$branch,$year,$mobile,
            $answers[1],$answers[2],$answers[3],$answers[4],$answers[5],
            $answers[6],$answers[7],$answers[8],$answers[9],$answers[10],
            $answers[11],$answers[12],
            $suggestion
        );

        if (mysqli_stmt_execute($stmt)) {
            $success = true;
            $message = "✅ Feedback submitted successfully";
        } else {
            $message = "❌ Database error";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Exit Feedback</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-serif">

<div class="max-w-4xl mx-auto bg-white p-6 mt-6 shadow">
<h2 class="text-center font-bold text-xl">STUDENT EXIT FEEDBACK</h2>

<?php if ($message): ?>
<p class="text-center font-semibold <?= $success?'text-green-600':'text-red-600' ?>">
<?= $message ?>
</p>
<?php endif; ?>

<form method="post" class="space-y-3 mt-4">

<input name="email" required placeholder="Email" class="w-full border p-2">
<input name="name" required placeholder="Student Name" class="w-full border p-2">
<input name="branch" required placeholder="Branch" class="w-full border p-2">
<input name="year" required placeholder="Year of Passing" class="w-full border p-2">
<input name="mobile" required placeholder="Mobile" class="w-full border p-2">

<?php foreach ($questions as $i=>$q): ?>
<div class="border p-3">
<p><?= ($i+1).". ".$q ?></p>
<?php for ($r=5;$r>=1;$r--): ?>
<label class="mr-3">
<input type="radio" name="q<?= $i+1 ?>" value="<?= $r ?>" required> <?= $r ?>
</label>
<?php endfor; ?>
</div>
<?php endforeach; ?>

<textarea name="suggestion" class="w-full border p-2" placeholder="Suggestion"></textarea>

<button class="bg-blue-600 text-white px-6 py-2 mt-3">Submit</button>
</form>
</div>

</body>
</html>
