<?php
require_once "../../config/db.php";

/* ===== FETCH AVERAGES ===== */
$avgSql = "SELECT
COUNT(*) total,
AVG(q1) q1, AVG(q2) q2, AVG(q3) q3, AVG(q4) q4, AVG(q5) q5,
AVG(q6) q6, AVG(q7) q7, AVG(q8) q8, AVG(q9) q9, AVG(q10) q10,
AVG(q11) q11, AVG(q12) q12
FROM student_exit_feedback";

$avgResult = mysqli_query($conn, $avgSql);
if (!$avgResult) {
    die("Average Query Error: " . mysqli_error($conn));
}

$avgData = mysqli_fetch_assoc($avgResult);

$averages = [];
for ($i = 1; $i <= 12; $i++) {
    $averages[] = round($avgData["q$i"] ?? 0, 2);
}

/* ===== FETCH INDIVIDUAL RESPONSES ===== */
$listSql = "SELECT name, email, mobile, submitted_at
            FROM student_exit_feedback
            ORDER BY submitted_at DESC";

$listResult = mysqli_query($conn, $listSql);
if (!$listResult) {
    die("List Query Error: " . mysqli_error($conn));
}

/* ===== QUESTIONS ===== */
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Exit Feedback Report</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-100 font-serif">

<!-- ===== GRAPH SECTION ===== -->
<div class="max-w-6xl mx-auto bg-white p-6 mt-6 shadow">
<h2 class="text-center font-bold text-xl mb-4">
STUDENT EXIT FEEDBACK – AVERAGE RATINGS
</h2>

<?php if ($avgData['total'] == 0): ?>
<p class="text-center text-red-600 font-semibold">
No feedback data available
</p>
<?php else: ?>
<canvas id="exitChart" height="200"></canvas>
<?php endif; ?>
</div>

<!-- ===== TABLE SECTION ===== -->
<?php if ($avgData['total'] > 0): ?>
<div class="max-w-6xl mx-auto bg-white p-6 mt-6 shadow">

<h3 class="text-center font-bold text-lg mb-4">
Student Exit Feedback Submissions
</h3>

<div class="overflow-x-auto">
<table class="w-full border border-collapse text-sm">
<thead class="bg-gray-200">
<tr>
    <th class="border p-2">S.No</th>
    <th class="border p-2">Student Name</th>
    <th class="border p-2">Email</th>
    <th class="border p-2">Mobile</th>
    <th class="border p-2">Submitted On</th>
</tr>
</thead>
<tbody>
<?php $i = 1; while ($row = mysqli_fetch_assoc($listResult)): ?>
<tr class="text-center">
    <td class="border p-2"><?= $i++ ?></td>
    <td class="border p-2"><?= htmlspecialchars($row['name']) ?></td>
    <td class="border p-2"><?= htmlspecialchars($row['email']) ?></td>
    <td class="border p-2"><?= htmlspecialchars($row['mobile']) ?></td>
    <td class="border p-2"><?= $row['submitted_at'] ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>

</div>
<?php endif; ?>

<!-- ===== CHART SCRIPT ===== -->
<script>
<?php if ($avgData['total'] > 0): ?>

const questions = <?= json_encode($questions) ?>;
const averages = <?= json_encode($averages) ?>;

new Chart(document.getElementById("exitChart"), {
  type: "bar",
  data: {
    labels: questions,
    datasets: [{
      label: "Average Rating",
      data: averages,
      backgroundColor: "rgba(59,130,246,0.6)"
    }]
  },
  options: {
    responsive: true,
    scales: {
      y: {
        beginAtZero: true,
        max: 5,
        ticks: { stepSize: 1 }
      }
    }
  }
});

<?php endif; ?>
</script>

</body>
</html>
