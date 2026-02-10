<?php
require_once __DIR__ . '/../../../config/db.php';

$department = 'CSE';

// Fetch aggregated averages for the department
$avgSql = "SELECT COUNT(*) as cnt,
  ROUND(AVG(q1),2) as q1, ROUND(AVG(q2),2) as q2, ROUND(AVG(q3),2) as q3, ROUND(AVG(q4),2) as q4,
  ROUND(AVG(q5),2) as q5, ROUND(AVG(q6),2) as q6, ROUND(AVG(q7),2) as q7, ROUND(AVG(q8),2) as q8,
  ROUND(AVG(q9),2) as q9, ROUND(AVG(q10),2) as q10, ROUND(AVG(q11),2) as q11, ROUND(AVG(q12),2) as q12
  FROM feedback_facilities WHERE UPPER(branch) = ?";

$stmt = mysqli_prepare($conn, $avgSql);
mysqli_stmt_bind_param($stmt, 's', $department);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($res);
$totalResponses = (int)($row['cnt'] ?? 0);
$averages = [];
for ($i = 1; $i <= 12; $i++) {
    $averages[] = isset($row["q$i"]) && $row["q$i"] !== null ? (float)$row["q$i"] : 0;
}

$questions = [
  "Classroom ambience and facilities (LCD projectors)",
  "Laboratories, computers and equipment",
  "Library facilities",
  "Internet and Wi-Fi facilities",
  "Availability of text & reference books",
  "Support from faculty and lab staff",
  "Hygiene and food quality in canteen",
  "Availability of RO drinking water",
  "Cleanliness of washrooms",
  "Indoor and outdoor sports facilities",
  "Safety and security in campus",
  "Overall infrastructure facilities"
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>CSE HOD - Feedback Averages</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-white font-sans p-6">
  <div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">CSE Department — Aggregated Feedback</h1>
      <a href="../../Login.php" class="text-sm">Login</a>
    </div>

    <?php if ($totalResponses > 0): ?>
      <p class="mb-4">Total responses: <strong><?php echo $totalResponses; ?></strong></p>
      <canvas id="deptChart" height="120"></canvas>
    <?php else: ?>
      <p class="text-gray-600">No feedback responses for CSE yet.</p>
    <?php endif; ?>

    <div class="mt-6">
      <a href="2CSE.html" class="underline">Back to department page</a>
    </div>
  </div>

  <?php if ($totalResponses > 0): ?>
  <script>
    const labels = <?php echo json_encode($questions); ?>;
    const data = <?php echo json_encode($averages); ?>;
    const ctx = document.getElementById('deptChart').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Average score',
          data: data,
          backgroundColor: 'rgba(16,185,129,0.7)',
          borderColor: 'rgba(16,185,129,1)',
          borderWidth: 1
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true,
            suggestedMax: 5,
            ticks: { stepSize: 1 }
          }
        }
      }
    });
  </script>
  <?php endif; ?>
</body>
</html>