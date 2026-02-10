<?php
require_once __DIR__ . '/../../config/db.php';
session_start();

/* ================= FETCH AVERAGES ================= */
$avgSql = "SELECT
COUNT(*) total,
AVG(q1) q1, AVG(q2) q2, AVG(q3) q3, AVG(q4) q4, AVG(q5) q5,
AVG(q6) q6, AVG(q7) q7, AVG(q8) q8, AVG(q9) q9, AVG(q10) q10,
AVG(q11) q11, AVG(q12) q12, AVG(q13) q13, AVG(q14) q14, AVG(q15) q15,
AVG(q16) q16, AVG(q17) q17, AVG(q18) q18, AVG(q19) q19, AVG(q20) q20,
AVG(q21) q21, AVG(q22) q22
FROM alumni_feedback";

$avgResult = mysqli_query($conn, $avgSql);
if (!$avgResult) {
    die("Average Query Error: " . mysqli_error($conn));
}

$data = mysqli_fetch_assoc($avgResult);

$averages = [];
for ($i = 1; $i <= 22; $i++) {
    $averages[] = round($data["q$i"] ?? 0, 2);
}

/* ================= FETCH INDIVIDUAL RESPONSES ================= */
$listSql = "SELECT name, email, mobile, submitted_at 
            FROM alumni_feedback 
            ORDER BY submitted_at DESC";

$listResult = mysqli_query($conn, $listSql);
if (!$listResult) {
    die("List Query Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Alumni Feedback Report</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-100 font-serif">

<!-- ================= GRAPH SECTION ================= -->
<div class="max-w-6xl mx-auto bg-white p-6 mt-6 shadow">

<h2 class="text-center font-bold text-xl mb-4">
ALUMNI FEEDBACK – AVERAGE RATINGS
</h2>

<?php if ($data['total'] == 0): ?>
<p class="text-center text-red-600 font-semibold">
No feedback data available
</p>
<?php else: ?>
<canvas id="feedbackChart" height="200"></canvas>
<?php endif; ?>

</div>

<!-- ================= TABLE SECTION ================= -->
<?php if ($data['total'] > 0): ?>
<div class="max-w-6xl mx-auto bg-white p-6 mt-6 shadow">

<h3 class="text-center font-bold text-lg mb-4">
Alumni Feedback Submissions
</h3>

<div class="overflow-x-auto">
<table class="w-full border border-collapse text-sm">
<thead class="bg-gray-200">
<tr>
    <th class="border p-2">S.No</th>
    <th class="border p-2">Alumni Name</th>
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

<!-- ================= CHART SCRIPT ================= -->
<script>
<?php if ($data['total'] > 0): ?>

const questions = [
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

const averages = <?= json_encode($averages) ?>;

new Chart(document.getElementById("feedbackChart"), {
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
