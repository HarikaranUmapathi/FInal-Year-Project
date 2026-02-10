<?php
require_once __DIR__ . '/../../config/db.php';
session_start();

$success = false;
$message = "";

/* ===== CREATE TABLE IF NOT EXISTS ===== */
$sql = "CREATE TABLE IF NOT EXISTS feedback_facilities (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255),
  name VARCHAR(255),
  branch VARCHAR(100),
  year VARCHAR(50),
  mobile VARCHAR(20),
  q1 INT, q2 INT, q3 INT, q4 INT, q5 INT, q6 INT,
  q7 INT, q8 INT, q9 INT, q10 INT, q11 INT, q12 INT,
  submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conn, $sql);

/* ===== FORM SUBMISSION ===== */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

  $email  = $_POST['email'];
  $name   = $_POST['name'];
  $branch = $_POST['branch'];
  $year   = $_POST['year'];
  $mobile = $_POST['mobile'];

  $answers = [];
  for ($i = 1; $i <= 12; $i++) {
    $answers[$i] = $_POST["q$i"] ?? null;
  }

  if (in_array(null, $answers)) {
    $message = "Please answer all questions";
  } else {

    $stmt = mysqli_prepare(
      $conn,
      "INSERT INTO feedback_facilities
      (email,name,branch,year,mobile,q1,q2,q3,q4,q5,q6,q7,q8,q9,q10,q11,q12)
      VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
    );

    mysqli_stmt_bind_param(
      $stmt,
      "ssssiiiiiiiiiiii",
      $email, $name, $branch, $year, $mobile,
      $answers[1], $answers[2], $answers[3], $answers[4], $answers[5], $answers[6],
      $answers[7], $answers[8], $answers[9], $answers[10], $answers[11], $answers[12]
    );

    if (mysqli_stmt_execute($stmt)) {
      $success = true;
      $message = "Feedback submitted successfully!";
    } else {
      $message = "Database error!";
    }
  }
}

/* ===== FETCH AVERAGES ===== */
$avgQuery = "SELECT
  COUNT(*) AS total,
  AVG(q1) q1, AVG(q2) q2, AVG(q3) q3, AVG(q4) q4,
  AVG(q5) q5, AVG(q6) q6, AVG(q7) q7, AVG(q8) q8,
  AVG(q9) q9, AVG(q10) q10, AVG(q11) q11, AVG(q12) q12
  FROM feedback_facilities";

$result = mysqli_query($conn, $avgQuery);
$data = mysqli_fetch_assoc($result);

$averages = [];
for ($i = 1; $i <= 12; $i++) {
  $averages[] = round($data["q$i"], 2);
}

$questions = [
  "Classroom facilities",
  "Laboratory facilities",
  "Library facilities",
  "Internet & Wi-Fi",
  "Reference books",
  "Faculty support",
  "Canteen hygiene",
  "Drinking water",
  "Washroom cleanliness",
  "Sports facilities",
  "Campus safety",
  "Overall infrastructure"
];
?>

<!DOCTYPE html>
<html>
<head>
  <title>Student Feedback</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-100">

<div class="max-w-4xl mx-auto bg-white p-6 mt-6 shadow">

<h2 class="text-center font-bold text-xl">STUDENT FEEDBACK ON FACILITIES</h2>

<?php if ($message): ?>
<p class="text-center mt-3 font-semibold <?= $success ? 'text-green-600' : 'text-red-600' ?>">
  <?= $message ?>
</p>
<?php endif; ?>

<form method="post" class="mt-6 space-y-4">

<input name="email" placeholder="Email" class="w-full border p-2" required>
<input name="name" placeholder="Student Name" class="w-full border p-2" required>
<input name="branch" placeholder="Branch" class="w-full border p-2" required>
<input name="year" placeholder="Year" class="w-full border p-2" required>
<input name="mobile" placeholder="Mobile Number" class="w-full border p-2" required>

<p class="font-semibold mt-4">Rate (5-Excellent to 1-No Comment)</p>

<?php foreach ($questions as $i => $q): ?>
<div class="border p-3">
  <p class="font-medium"><?= ($i+1) ?>. <?= $q ?></p>
  <?php for ($r=5; $r>=1; $r--): ?>
    <label class="mr-4">
      <input type="radio" name="q<?= $i+1 ?>" value="<?= $r ?>" required> <?= $r ?>
    </label>
  <?php endfor; ?>
</div>
<?php endforeach; ?>

<button class="bg-blue-600 text-white px-6 py-2 mt-4">Submit</button>

</form>
</div>

<!-- ===== GRAPH ===== -->
<?php if ($data['total'] > 0): ?>
<div class="max-w-4xl mx-auto bg-white p-6 mt-6 shadow">
<h3 class="font-bold mb-2">Average Feedback Graph</h3>
<canvas id="chart"></canvas>
</div>

<script>
new Chart(document.getElementById("chart"), {
  type: "bar",
  data: {
    labels: <?= json_encode($questions) ?>,
    datasets: [{
      label: "Average Rating",
      data: <?= json_encode($averages) ?>,
      backgroundColor: "rgba(59,130,246,0.6)"
    }]
  },
  options: {
    scales: {
      y: {
        beginAtZero: true,
        max: 5
      }
    }
  }
});
</script>
<?php endif; ?>

</body>
</html>
