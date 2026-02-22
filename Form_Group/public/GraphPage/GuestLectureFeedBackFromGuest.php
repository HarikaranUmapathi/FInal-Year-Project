<?php
require_once __DIR__ . '/../../config/db.php';
session_start();

// Ensure table exists
$conn->query("
CREATE TABLE IF NOT EXISTS guest_feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    guest_name VARCHAR(100),
    designation VARCHAR(100),
    organization VARCHAR(200),
    subject VARCHAR(200),
    objective TEXT,
    student_name VARCHAR(100),
    section VARCHAR(50),
    reg_no VARCHAR(50),
    feedback_type VARCHAR(50),
    q1 INT,
    q2 INT,
    q3 INT,
    q4 INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

/* ================= FETCH AVERAGES ================= */
$avgSql = "SELECT 
    COUNT(*) total,
    AVG(q1) q1,
    AVG(q2) q2,
    AVG(q3) q3,
    AVG(q4) q4
FROM guest_feedback";

$avgResult = mysqli_query($conn, $avgSql);
if (!$avgResult) {
    die("Average Query Error: " . mysqli_error($conn));
}

$data = mysqli_fetch_assoc($avgResult);

/* ================= STORE IN ARRAY ================= */
$averages = [];
$questionLabels = ["Content Relevance", "Delivery Quality", "Industry Insights", "Overall Satisfaction"];
for ($i = 1; $i <= 4; $i++) {
    $averages[] = round($data["q$i"] ?? 0, 2);
}

/* ================= FETCH INDIVIDUAL RESPONSES ================= */
$listSql = "SELECT guest_name, designation, organization, subject, student_name, created_at, q1, q2, q3, q4, section, reg_no, feedback_type
            FROM guest_feedback 
            ORDER BY created_at DESC";

$listResult = mysqli_query($conn, $listSql);
if (!$listResult) {
    die("List Query Error: " . mysqli_error($conn));
}

$responses = [];
while ($row = mysqli_fetch_assoc($listResult)) {
    $sum = ($row['q1'] ?? 0) + ($row['q2'] ?? 0) + ($row['q3'] ?? 0) + ($row['q4'] ?? 0);
    $score = round(($sum / (4 * 5)) * 100, 1);
    $row['score'] = $score;
    $responses[] = $row;
}

$totalResponses = $data['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest Lecture Feedback Analytics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .header h1 {
            font-size: 2.2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .header p {
            font-size: 1rem;
            opacity: 0.95;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            margin-bottom: 3rem;
        }
        
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .stat-box .number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .stat-box .label {
            font-size: 0.9rem;
            opacity: 0.95;
        }
        
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .chart-container {
            position: relative;
            height: 350px;
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        }
        
        .chart-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #667eea;
        }
        
        .section-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #333;
            margin: 2rem 0 1.5rem 0;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #667eea;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5rem;
        }
        
        .data-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .data-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 700;
            font-size: 0.9rem;
        }
        
        .data-table td {
            padding: 0.9rem 1rem;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .data-table tbody tr:hover {
            background: #f9fafb;
        }
        
        .data-table tbody tr:nth-child(even) {
            background: #fafbfc;
        }
        
        .score-badge {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.85rem;
        }
        
        .score-excellent {
            background: #d1fae5;
            color: #047857;
        }
        
        .score-good {
            background: #dbeafe;
            color: #0369a1;
        }
        
        .score-average {
            background: #fef3c7;
            color: #b45309;
        }
        
        .score-poor {
            background: #fee2e2;
            color: #b91c1c;
        }
        
        .back-link {
            display: inline-block;
            color: white;
            text-decoration: none;
            font-weight: 600;
            margin-top: 1rem;
            transition: all 0.2s;
        }
        
        .back-link:hover {
            opacity: 0.8;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        .btn {
            display: inline-block;
            padding: 0.6rem 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 1rem;
            transition: all 0.3s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
        }
        
        @media (max-width: 768px) {
            .header h1 {
                font-size: 1.6rem;
            }
            
            .charts-grid {
                grid-template-columns: 1fr;
            }
            
            .chart-container {
                height: 300px;
            }
            
            .data-table {
                font-size: 0.85rem;
            }
            
            .data-table th, .data-table td {
                padding: 0.6rem;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 Guest Lecture Feedback Analytics</h1>
        <p>Comprehensive feedback analysis and insights</p>
    </div>

    <div class="container">
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-box">
                <div class="number"><?= $totalResponses ?></div>
                <div class="label">Total Responses</div>
            </div>
            <div class="stat-box">
                <div class="number"><?= number_format($averages[0], 1) ?>/5</div>
                <div class="label">Avg. Content Relevance</div>
            </div>
            <div class="stat-box">
                <div class="number"><?= number_format($averages[1], 1) ?>/5</div>
                <div class="label">Avg. Delivery Quality</div>
            </div>
            <div class="stat-box">
                <div class="number"><?= number_format(array_sum($averages) / count($averages), 1) ?>/5</div>
                <div class="label">Overall Average Rating</div>
            </div>
        </div>

        <!-- Charts Section -->
        <?php if ($totalResponses > 0): ?>
        <div class="card">
            <h2 class="section-title">📈 Feedback Analytics</h2>
            
            <div class="charts-grid">
                <!-- Bar Chart -->
                <div class="chart-container">
                    <div class="chart-title">Average Ratings by Category</div>
                    <canvas id="barChart"></canvas>
                </div>
                
                <!-- Radar Chart -->
                <div class="chart-container">
                    <div class="chart-title">Rating Distribution</div>
                    <canvas id="radarChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Feedback Responses Table -->
        <div class="card">
            <h2 class="section-title">📋 Detailed Feedback Responses</h2>
            
            <?php if (!empty($responses)): ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Guest Name</th>
                                <th>Designation</th>
                                <th>Organization</th>
                                <th>Subject</th>
                                <th>Student Name</th>
                                <th>Q1</th>
                                <th>Q2</th>
                                <th>Q3</th>
                                <th>Q4</th>
                                <th>Overall Score</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($responses as $r): 
                                $scoreClass = 'score-poor';
                                if ($r['score'] >= 80) {
                                    $scoreClass = 'score-excellent';
                                } elseif ($r['score'] >= 60) {
                                    $scoreClass = 'score-good';
                                } elseif ($r['score'] >= 40) {
                                    $scoreClass = 'score-average';
                                }
                            ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($r['guest_name']) ?></strong></td>
                                    <td><?= htmlspecialchars($r['designation']) ?></td>
                                    <td><?= htmlspecialchars($r['organization']) ?></td>
                                    <td><?= htmlspecialchars($r['subject']) ?></td>
                                    <td><?= htmlspecialchars($r['student_name'] ?? 'N/A') ?></td>
                                    <td style="text-align: center; font-weight: bold;"><?= $r['q1'] ?></td>
                                    <td style="text-align: center; font-weight: bold;"><?= $r['q2'] ?></td>
                                    <td style="text-align: center; font-weight: bold;"><?= $r['q3'] ?></td>
                                    <td style="text-align: center; font-weight: bold;"><?= $r['q4'] ?></td>
                                    <td>
                                        <span class="score-badge <?= $scoreClass ?>">
                                            <?= $r['score'] ?>%
                                        </span>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($r['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p style="color: #666; text-align: center; padding: 2rem;">No feedback responses yet.</p>
            <?php endif; ?>
        </div>
        <?php else: ?>
            <div class="card">
                <p style="color: #666; text-align: center; padding: 2rem; font-size: 1.1rem;">📭 No feedback data available yet.</p>
            </div>
        <?php endif; ?>

        <a href="../FeedBack_Form/GuestLectureFeedbackFromGuest.php" class="btn">← Back to Form</a>
    </div>

    <script>
        // Bar Chart
        new Chart(document.getElementById("barChart"), {
            type: "bar",
            data: {
                labels: <?= json_encode($questionLabels) ?>,
                datasets: [{
                    label: "Average Rating",
                    data: <?= json_encode($averages) ?>,
                    backgroundColor: [
                        "rgba(102, 126, 234, 0.8)",
                        "rgba(118, 75, 162, 0.8)",
                        "rgba(79, 172, 254, 0.8)",
                        "rgba(168, 85, 247, 0.8)"
                    ],
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 5,
                        ticks: {
                            stepSize: 1,
                            font: { weight: 600 }
                        },
                        grid: {
                            color: "rgba(0,0,0,0.05)"
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        labels: { font: { size: 12, weight: 600 } }
                    }
                }
            }
        });

        // Radar Chart
        new Chart(document.getElementById("radarChart"), {
            type: "radar",
            data: {
                labels: <?= json_encode($questionLabels) ?>,
                datasets: [{
                    label: "Average Ratings",
                    data: <?= json_encode($averages) ?>,
                    borderColor: "rgba(102, 126, 234, 1)",
                    backgroundColor: "rgba(102, 126, 234, 0.2)",
                    borderWidth: 2,
                    pointBackgroundColor: "rgba(102, 126, 234, 1)",
                    pointBorderColor: "#fff",
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 5,
                        ticks: {
                            stepSize: 1,
                            font: { weight: 600 }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        labels: { font: { size: 12, weight: 600 } }
                    }
                }
            }
        });
    </script>
</body>
</html>
