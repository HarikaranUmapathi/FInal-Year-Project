<?php
require_once __DIR__ . '/../../config/db.php';

/* ===== FETCH AVERAGES ===== */
$avgQuery = "SELECT
  COUNT(*) AS total,
  AVG(q1) q1, AVG(q2) q2, AVG(q3) q3, AVG(q4) q4, AVG(q5) q5
FROM parent_feedback";

$avgResult = mysqli_query($conn, $avgQuery);
$avgData = mysqli_fetch_assoc($avgResult);

$averages = [];
for ($i = 1; $i <= 5; $i++) {
  $averages[] = round($avgData["q$i"] ?? 0, 2);
}

$questions = [
  "Institutional Discipline and Culture",
  "Infrastructure Facilities",
  "Communication from College",
  "Career Guidance and Placement",
  "Overall Rating of the College"
];

/* ===== FETCH FEEDBACK DETAILS ===== */
$listQuery = "SELECT
  student_name,
  parent_name,
  branch_batch,
  phone,
  suggestion
FROM parent_feedback
ORDER BY submitted_at DESC";

$listResult = mysqli_query($conn, $listQuery);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Parent Feedback Graph</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-100 font-serif">

<!-- ===== GRAPH SECTION ===== -->
<div class="max-w-6xl mx-auto bg-white p-6 mt-6 shadow">

<h2 class="text-center font-bold text-xl mb-4">
Parent Feedback – Average Ratings
</h2>

<?php if (($avgData['total'] ?? 0) > 0): ?>
<canvas id="chart"></canvas>
<?php else: ?>
<p class="text-center text-red-600 font-semibold">No feedback data available</p>
<?php endif; ?>

</div>

<?php if (($avgData['total'] ?? 0) > 0): ?>
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
        max: 5,
        ticks: { stepSize: 1 }
      }
    }
  }
});
</script>
<?php endif; ?>

<!-- ===== TABLE SECTION ===== -->
<div class="max-w-6xl mx-auto bg-white p-6 mt-6 shadow overflow-x-auto">

<h3 class="font-bold text-lg mb-4 text-center">
Parent Feedback Details
</h3>

<?php if (mysqli_num_rows($listResult) > 0): ?>
<table class="w-full border border-gray-300 text-sm text-center">

<thead class="bg-gray-200">
<tr>
  <th class="border p-2">Student Name</th>
  <th class="border p-2">Parent Name</th>
  <th class="border p-2">Branch & Batch</th>
  <th class="border p-2">Mobile Number</th>
  <th class="border p-2">Suggestion</th>
</tr>
</thead>

<tbody>
<?php while ($row = mysqli_fetch_assoc($listResult)): ?>
<tr class="bg-white hover:bg-gray-50">
  <td class="border p-2"><?= htmlspecialchars($row['student_name']) ?></td>
  <td class="border p-2"><?= htmlspecialchars($row['parent_name']) ?></td>
  <td class="border p-2"><?= htmlspecialchars($row['branch_batch']) ?></td>
  <td class="border p-2"><?= htmlspecialchars($row['phone']) ?></td>
  <td class="border p-2 text-left">
    <?= nl2br(htmlspecialchars($row['suggestion'] ?: '—')) ?>
  </td>
</tr>
<?php endwhile; ?>
</tbody>

</table>
<?php else: ?>
<p class="text-center text-red-600 font-semibold">No feedback records found</p>
<?php endif; ?>

</div>

</body>
</html>
