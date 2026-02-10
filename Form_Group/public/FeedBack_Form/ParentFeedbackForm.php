<?php
require_once __DIR__ . '/../../config/db.php';
session_start();

$success = false;
$message = "";

/* ===== CREATE TABLE IF NOT EXISTS ===== */
$sql = "CREATE TABLE IF NOT EXISTS parent_feedback (
  id INT AUTO_INCREMENT PRIMARY KEY,

  student_name VARCHAR(100),
  parent_name VARCHAR(100),
  branch_batch VARCHAR(100),
  phone VARCHAR(20),
  email VARCHAR(100),

  q1 INT, q2 INT, q3 INT, q4 INT, q5 INT,

  suggestion TEXT,
  submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conn, $sql);

/* ===== QUESTIONS ===== */
$questions = [
  "Institutional Discipline and Culture",
  "Infrastructure Facilities",
  "Communication from College",
  "Career Guidance and Placement",
  "Overall Rating of the College"
];

/* ===== FORM SUBMISSION ===== */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

  $student = $_POST['student_name'];
  $parent  = $_POST['parent_name'];
  $branch  = $_POST['branch_batch'];
  $phone   = $_POST['phone'];
  $email   = $_POST['email'];
  $suggestion = $_POST['suggestion'] ?? "";

  $answers = [];
  for ($i = 1; $i <= 5; $i++) {
    $answers[$i] = $_POST["q$i"] ?? null;
  }

  if (in_array(null, $answers, true)) {
    $message = "Please answer all questions";
  } else {

    $stmt = mysqli_prepare(
      $conn,
      "INSERT INTO parent_feedback
      (student_name,parent_name,branch_batch,phone,email,
       q1,q2,q3,q4,q5,suggestion)
      VALUES (?,?,?,?,?,?,?,?,?,?,?)"
    );

    mysqli_stmt_bind_param(
      $stmt,
      "sssssiiiiis",
      $student, $parent, $branch, $phone, $email,
      $answers[1], $answers[2], $answers[3], $answers[4], $answers[5],
      $suggestion
    );

    if (mysqli_stmt_execute($stmt)) {
      $success = true;
      $message = "Parent feedback submitted successfully!";
    } else {
      $message = "Database error!";
    }
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Parent Feedback</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-serif">

<div class="max-w-4xl mx-auto bg-white p-6 mt-6 shadow">

<h2 class="text-center font-bold text-xl">PARENT FEEDBACK FORM</h2>

<?php if ($message): ?>
<p class="text-center mt-3 font-semibold <?= $success ? 'text-green-600' : 'text-red-600' ?>">
  <?= htmlspecialchars($message) ?>
</p>
<?php endif; ?>

<form method="post" class="mt-6 space-y-4">

<input name="student_name" placeholder="Student Name" class="w-full border p-2" required>
<input name="parent_name" placeholder="Parent Name" class="w-full border p-2" required>
<input name="branch_batch" placeholder="Branch & Batch" class="w-full border p-2">
<input name="phone" placeholder="Phone Number" class="w-full border p-2">
<input name="email" placeholder="Email" class="w-full border p-2">

<p class="font-semibold mt-4">Rate (5-Excellent to 1-Poor)</p>

<?php foreach ($questions as $i => $q): ?>
<div class="border p-3">
  <p class="font-medium"><?= ($i+1) ?>. <?= $q ?></p>
  <?php for ($r = 5; $r >= 1; $r--): ?>
    <label class="mr-4">
      <input type="radio" name="q<?= $i+1 ?>" value="<?= $r ?>" required> <?= $r ?>
    </label>
  <?php endfor; ?>
</div>
<?php endforeach; ?>

<textarea name="suggestion" class="w-full border p-2 mt-3" placeholder="Suggestion if any"></textarea>

<button class="bg-blue-600 text-white px-6 py-2 mt-4">Submit</button>

</form>
</div>

</body>
</html>
