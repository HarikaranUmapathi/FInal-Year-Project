<?php
require_once __DIR__ . '/../../config/db.php';

$conn->set_charset("utf8mb4");

// Ensure table exists
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS course_end_survey (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(100),
    faculty_name VARCHAR(100),
    department VARCHAR(100),
    year_sem VARCHAR(50),
    objective TEXT,
    outcome TEXT,
    feedback_type VARCHAR(50),
    q1 INT, q2 INT, q3 INT, q4 INT, q5 INT,
    q6 INT, q7 INT, q8 INT, q9 INT, q10 INT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$questions = [
    "Course objectives were clearly defined",
    "Course syllabus was relevant to the program",
    "Course content was well organized",
    "Course material was adequate and useful",
    "Teaching methodology was effective",
    "Faculty explained concepts clearly",
    "Faculty encouraged student participation",
    "Assessment methods were fair",
    "Course improved analytical/problem-solving skills",
    "Overall satisfaction with the course"
];

// Get aggregated stats
$statsQuery = "SELECT 
    COUNT(*) as total,
    AVG(q1) as q1, AVG(q2) as q2, AVG(q3) as q3, AVG(q4) as q4, AVG(q5) as q5,
    AVG(q6) as q6, AVG(q7) as q7, AVG(q8) as q8, AVG(q9) as q9, AVG(q10) as q10
FROM course_end_survey";

$statsResult = mysqli_query($conn, $statsQuery);
$statsData = mysqli_fetch_assoc($statsResult);

$totalResponses = intval($statsData['total'] ?? 0);
$averages = [];
for ($i = 1; $i <= 10; $i++) {
    $averages[$i] = round(floatval($statsData["q$i"] ?? 0), 2);
}

$overallAvg = round(array_sum($averages) / count($averages), 2);
$overallPercentage = round($overallAvg * 20, 1);

// Get detailed responses
$responsesQuery = "SELECT * FROM course_end_survey ORDER BY submitted_at DESC LIMIT 100";
$responsesResult = mysqli_query($conn, $responsesQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course End Survey - Analytics</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
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
            padding-bottom: 3rem;
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
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .header p {
            font-size: 1rem;
            opacity: 0.95;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            border-left: 4px solid #667eea;
        }
        
        .stat-card h3 {
            color: #666;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 0.75rem;
            letter-spacing: 0.5px;
        }
        
        .stat-card .value {
            font-size: 2.5rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 0.5rem;
        }
        
        .stat-card .subtext {
            font-size: 0.85rem;
            color: #999;
        }
        
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .chart-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        
        .chart-card h3 {
            font-size: 1.3rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        .chart-container {
            position: relative;
            height: 400px;
        }
        
        .data-table {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            overflow-x: auto;
        }
        
        .data-table h3 {
            font-size: 1.3rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 1.5rem;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }
        
        thead {
            background: #f9fafb;
            border-bottom: 2px solid #e5e7eb;
        }
        
        th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #333;
            white-space: nowrap;
        }
        
        td {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            color: #555;
        }
        
        tbody tr:hover {
            background: #f9fafb;
        }
        
        tbody tr:nth-child(even) {
            background: #fafbfc;
        }
        
        .score-badge {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.8rem;
            text-align: center;
            min-width: 80px;
        }
        
        .score-excellent {
            background: #d1fae5;
            color: #047857;
        }
        
        .score-good {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .score-average {
            background: #fef3c7;
            color: #92400e;
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
            margin-top: 2rem;
            transition: all 0.2s;
        }
        
        .back-link:hover {
            opacity: 0.8;
        }
        
        @media (max-width: 768px) {
            .header h1 {
                font-size: 1.8rem;
            }
            
            .charts-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            table {
                font-size: 0.8rem;
            }
            
            th, td {
                padding: 0.75rem 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 Course End Survey Analytics</h1>
        <p>Comprehensive analysis of course effectiveness and student satisfaction</p>
    </div>

    <div class="container">
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Responses</h3>
                <div class="value"><?= $totalResponses ?></div>
                <div class="subtext">Course evaluation surveys collected</div>
            </div>

            <div class="stat-card">
                <h3>Average Rating</h3>
                <div class="value"><?= number_format($overallAvg, 2) ?></div>
                <div class="subtext">Out of 5.00 scale</div>
            </div>

            <div class="stat-card">
                <h3>Overall Satisfaction</h3>
                <div class="value"><?= $overallPercentage ?>%</div>
                <div class="subtext">Overall satisfaction score</div>
            </div>

            <div class="stat-card">
                <h3>Highest Rated</h3>
                <div class="value"><?= number_format(max($averages), 2) ?></div>
                <div class="subtext">Based on all 10 questions</div>
            </div>
        </div>

        <!-- Charts -->
        <div class="charts-grid">
            <div class="chart-card">
                <h3>📈 Average Ratings by Criterion</h3>
                <div class="chart-container">
                    <canvas id="barChart"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <h3>🎯 Rating Distribution Overview</h3>
                <div class="chart-container">
                    <canvas id="radarChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Detailed Response Table -->
        <div class="data-table">
            <h3>📋 Detailed Survey Responses</h3>
            <table>
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Faculty</th>
                        <th>Department</th>
                        <th>Semester</th>
                        <th>Avg Rating</th>
                        <th>Satisfaction</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = mysqli_fetch_assoc($responsesResult)) {
                        $avg = round((
                            $row['q1'] + $row['q2'] + $row['q3'] + $row['q4'] + $row['q5'] +
                            $row['q6'] + $row['q7'] + $row['q8'] + $row['q9'] + $row['q10']
                        ) / 10, 2);
                        
                        $percentage = round($avg * 20, 1);
                        
                        if ($avg >= 4) {
                            $badgeClass = 'score-excellent';
                            $label = 'Excellent';
                        } elseif ($avg >= 3) {
                            $badgeClass = 'score-good';
                            $label = 'Good';
                        } elseif ($avg >= 2) {
                            $badgeClass = 'score-average';
                            $label = 'Average';
                        } else {
                            $badgeClass = 'score-poor';
                            $label = 'Poor';
                        }
                        
                        $date = new DateTime($row['submitted_at']);
                        $formattedDate = $date->format('M d, Y');
                    ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($row['student_name']) ?></strong></td>
                            <td><?= htmlspecialchars($row['faculty_name']) ?></td>
                            <td><?= htmlspecialchars($row['department']) ?></td>
                            <td><?= htmlspecialchars($row['year_sem']) ?></td>
                            <td><strong><?= $avg ?>/5.00</strong></td>
                            <td><span class="score-badge <?= $badgeClass ?>"><?= $label ?> (<?= $percentage ?>%)</span></td>
                            <td><?= $formattedDate ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php if ($totalResponses == 0): ?>
                <p style="text-align: center; color: #999; padding: 2rem;">No responses yet</p>
            <?php endif; ?>
        </div>

        <a href="../../FormPage/Forms.html" class="back-link">← Back to Forms List</a>
    </div>

    <script>
        // Bar Chart
        const barCtx = document.getElementById('barChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: [
                    'Objectives', 'Syllabus', 'Organization', 'Material', 'Methodology',
                    'Concepts', 'Participation', 'Assessment', 'Skills', 'Satisfaction'
                ],
                datasets: [{
                    label: 'Average Rating',
                    data: [<?= implode(',', $averages) ?>],
                    backgroundColor: [
                        '#667eea', '#764ba2', '#8866c9', '#667eea', '#764ba2',
                        '#8866c9', '#667eea', '#764ba2', '#8866c9', '#667eea'
                    ],
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'x',
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 5,
                        ticks: { color: '#666' },
                        grid: { color: '#e5e7eb' }
                    },
                    x: {
                        ticks: { color: '#666' },
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });

        // Radar Chart
        const radarCtx = document.getElementById('radarChart').getContext('2d');
        new Chart(radarCtx, {
            type: 'radar',
            data: {
                labels: [
                    'Objectives', 'Syllabus', 'Organization', 'Material', 'Methodology',
                    'Concepts', 'Participation', 'Assessment', 'Skills', 'Satisfaction'
                ],
                datasets: [{
                    label: 'Rating Score',
                    data: [<?= implode(',', $averages) ?>],
                    backgroundColor: 'rgba(102, 126, 234, 0.2)',
                    borderColor: '#667eea',
                    borderWidth: 2,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 5,
                        ticks: { color: '#666', font: { size: 10 } },
                        grid: { color: '#e5e7eb' }
                    }
                },
                plugins: {
                    legend: { display: true, position: 'top' }
                }
            }
        });
    </script>
</body>
</html>
