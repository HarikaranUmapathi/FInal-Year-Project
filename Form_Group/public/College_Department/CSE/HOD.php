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
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      min-height: 100vh;
    }

    .header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 2.5rem 2rem;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }

    .header-content {
      max-width: 1400px;
      margin: 0 auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 1rem;
    }

    .header-title {
      display: flex;
      flex-direction: column;
      gap: 0.25rem;
    }

    .header-title h1 {
      font-size: 2rem;
      font-weight: 700;
    }

    .header-title p {
      font-size: 1rem;
      opacity: 0.95;
    }

    .header-actions {
      display: flex;
      gap: 1rem;
      align-items: center;
    }

    .login-btn, .back-btn {
      background: white;
      color: #667eea;
      padding: 0.75rem 1.5rem;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s;
      border: none;
      cursor: pointer;
    }

    .login-btn:hover, .back-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    .container {
      max-width: 1400px;
      margin: 0 auto;
      padding: 2rem 1rem;
    }

    .chart-section {
      background: white;
      border-radius: 12px;
      padding: 2rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
      border: 1px solid #e5e7eb;
      margin-bottom: 2rem;
    }

    .section-title {
      font-size: 1.75rem;
      font-weight: 700;
      color: #1f2937;
      margin-bottom: 1rem;
      text-align: center;
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
      margin-bottom: 2rem;
    }

    .stats-card {
      background: #f9fafb;
      border-radius: 8px;
      padding: 1.5rem;
      text-align: center;
      border: 1px solid #e5e7eb;
    }

    .stats-value {
      font-size: 2rem;
      font-weight: 700;
      color: #667eea;
      margin-bottom: 0.5rem;
    }

    .stats-label {
      color: #6b7280;
      font-weight: 500;
    }

    @media (max-width: 768px) {
      .header {
        padding: 2rem 1rem;
      }

      .header-title h1 {
        font-size: 1.5rem;
      }

      .header-actions {
        flex-direction: column;
        width: 100%;
      }

      .container {
        padding: 1rem;
      }

      .stats-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <header class="header">
    <div class="header-content">
      <div class="header-title">
        <h1>CSE Department</h1>
        <p>HOD Analytics Dashboard</p>
      </div>
      <div class="header-actions">
        <a href="../../Login.php" class="login-btn">Logout</a>
        <a href="2CSE.html" class="back-btn">Back to Department</a>
      </div>
    </div>
  </header>

  <div class="container">
    <div class="stats-grid">
      <div class="stats-card">
        <div class="stats-value"><?php echo $totalResponses; ?></div>
        <div class="stats-label">Total Responses</div>
      </div>
      <div class="stats-card">
        <div class="stats-value">CSE</div>
        <div class="stats-label">Department</div>
      </div>
      <div class="stats-card">
        <div class="stats-value">HOD</div>
        <div class="stats-label">Access Level</div>
      </div>
      <div class="stats-card">
        <div class="stats-value">Real-time</div>
        <div class="stats-label">Data Updates</div>
      </div>
    </div>

    <div class="chart-section">
      <h2 class="section-title">CSE Department — Aggregated Feedback</h2>
      <?php if ($totalResponses > 0): ?>
        <canvas id="deptChart" height="120"></canvas>
      <?php else: ?>
        <p class="text-gray-600 text-center py-8">No feedback responses for CSE yet.</p>
      <?php endif; ?>
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
          backgroundColor: 'rgba(102, 126, 234, 0.7)',
          borderColor: 'rgba(102, 126, 234, 1)',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            suggestedMax: 5,
            ticks: { stepSize: 1 }
          }
        },
        plugins: {
          legend: {
            display: true,
            position: 'top'
          }
        }
      }
    });
  </script>
  <?php endif; ?>
</body>
</html>