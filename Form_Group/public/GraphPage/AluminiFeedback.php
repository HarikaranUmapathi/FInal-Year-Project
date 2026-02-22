<?php
require_once __DIR__ . '/../../config/db.php';

$conn->set_charset("utf8mb4");

// Ensure table exists
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS alumni_feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255),
    name VARCHAR(255),
    branch VARCHAR(100),
    pass_year VARCHAR(20),
    mobile VARCHAR(20),
    feedback_type VARCHAR(50),
    q1 INT, q2 INT, q3 INT, q4 INT, q5 INT,
    q6 INT, q7 INT, q8 INT, q9 INT, q10 INT,
    q11 INT, q12 INT, q13 INT, q14 INT, q15 INT,
    q16 INT, q17 INT, q18 INT, q19 INT, q20 INT,
    q21 INT, q22 INT,
    suggestion TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$questions = [
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

// Get aggregated stats
$statsQuery = "SELECT 
    COUNT(*) as total,
    AVG(q1) as q1, AVG(q2) as q2, AVG(q3) as q3, AVG(q4) as q4, AVG(q5) as q5,
    AVG(q6) as q6, AVG(q7) as q7, AVG(q8) as q8, AVG(q9) as q9, AVG(q10) as q10,
    AVG(q11) as q11, AVG(q12) as q12, AVG(q13) as q13, AVG(q14) as q14, AVG(q15) as q15,
    AVG(q16) as q16, AVG(q17) as q17, AVG(q18) as q18, AVG(q19) as q19, AVG(q20) as q20,
    AVG(q21) as q21, AVG(q22) as q22
FROM alumni_feedback";

$statsResult = mysqli_query($conn, $statsQuery);
$statsData = mysqli_fetch_assoc($statsResult);

$totalResponses = intval($statsData['total'] ?? 0);
$averages = [];
for ($i = 1; $i <= 22; $i++) {
    $averages[$i] = round(floatval($statsData["q$i"] ?? 0), 2);
}

$overallAvg = round(array_sum($averages) / count($averages), 2);
$overallPercentage = round($overallAvg * 20, 1);

// Get detailed responses
$responsesQuery = "SELECT * FROM alumni_feedback ORDER BY submitted_at DESC LIMIT 100";
$responsesResult = mysqli_query($conn, $responsesQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Feedback - Analytics</title>
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
            height: 500px;
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
            
            .chart-container {
                height: 350px;
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
        <h1>📊 Alumni Feedback Analytics</h1>
        <p>Comprehensive analysis of alumni satisfaction and institutional impact</p>
    </div>

    <div class="container">
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Responses</h3>
                <div class="value"><?= $totalResponses ?></div>
                <div class="subtext">Alumni surveys collected</div>
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
                <div class="subtext">Based on all 22 questions</div>
            </div>
        </div>

        <!-- Charts -->
        <div class="charts-grid">
            <div class="chart-card">
                <h3>📈 Average Ratings Across All Criteria</h3>
                <div class="chart-container">
                    <canvas id="barChart"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <h3>🎯 Multi-Dimensional Rating Distribution</h3>
                <div class="chart-container">
                    <canvas id="radarChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Detailed Response Table -->
        <div class="data-table">
            <h3>📋 Detailed Alumni Responses</h3>
            <table>
                <thead>
                    <tr>
                        <th>Alumni Name</th>
                        <th>Email</th>
                        <th>Branch</th>
                        <th>Pass Year</th>
                        <th>Avg Rating</th>
                        <th>Satisfaction</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = mysqli_fetch_assoc($responsesResult)) {
                        $sum = 0;
                        for ($i = 1; $i <= 22; $i++) {
                            $sum += $row["q$i"];
                        }
                        $avg = round($sum / 22, 2);
                        
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
                            <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['branch']) ?></td>
                            <td><?= htmlspecialchars($row['pass_year']) ?></td>
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
                    'Career', 'HigherEd', 'Startup', 'Knowledge', 'Problems', 'Solutions',
                    'Experiments', 'Tools', 'Society', 'Environment', 'Ethics', 'Teamwork',
                    'Communication', 'Leadership', 'Learning', 'CAE', 'Materials', 'Vision',
                    'Mission', 'Objectives', 'PSOs', 'Confirmation'
                ],
                datasets: [{
                    label: 'Average Rating',
                    data: [<?= implode(',', $averages) ?>],
                    backgroundColor: '#667eea',
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
                        ticks: { color: '#666' },
                        grid: { color: '#e5e7eb' }
                    },
                    x: {
                        ticks: { color: '#666', maxRotation: 45, minRotation: 0 },
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
                    'Career', 'HigherEd', 'Startup', 'Knowledge', 'Problems', 'Solutions',
                    'Experiments', 'Tools', 'Society', 'Environment', 'Ethics', 'Teamwork',
                    'Communication', 'Leadership', 'Learning', 'CAE', 'Materials', 'Vision',
                    'Mission', 'Objectives', 'PSOs', 'Confirmation'
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
                        ticks: { color: '#666', font: { size: 9 } },
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