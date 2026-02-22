<?php
require_once __DIR__ . '/../../config/db.php';
session_start();

// Fetch statistics
$avgSql = "SELECT 
    COUNT(*) total,
    AVG(q1) q1, AVG(q2) q2, AVG(q3) q3, AVG(q4) q4,
    AVG(q5) q5, AVG(q6) q6, AVG(q7) q7, AVG(q8) q8,
    AVG(q9) q9, AVG(q10) q10, AVG(q11) q11, AVG(q12) q12
FROM feedback_facilities";

$avgResult = mysqli_query($conn, $avgSql);
$data = mysqli_fetch_assoc($avgResult);
$totalResponses = $data['total'] ?? 0;

$averages = [];
$questionLabels = [
    "Classroom\nFacilities",
    "Laboratory\nFacilities",
    "Library\nFacilities",
    "Internet\n& Wi-Fi",
    "Reference\nBooks",
    "Faculty\nSupport",
    "Canteen\nHygiene",
    "Drinking\nWater Quality",
    "Washroom\nCleanliness",
    "Sports\nFacilities",
    "Campus\nSafety",
    "Overall\nInfrastructure"
];

for ($i = 1; $i <= 12; $i++) {
    $averages[] = round($data["q$i"] ?? 0, 2);
}

// Fetch individual responses
$listSql = "SELECT email, name, branch, year, section, reg_no, submitted_at, 
                   q1, q2, q3, q4, q5, q6, q7, q8, q9, q10, q11, q12
            FROM feedback_facilities 
            ORDER BY submitted_at DESC";

$listResult = mysqli_query($conn, $listSql);
$responses = [];

while ($row = mysqli_fetch_assoc($listResult)) {
    $sum = 0;
    for ($i = 1; $i <= 12; $i++) {
        $sum += ($row["q$i"] ?? 0);
    }
    $score = round(($sum / (12 * 5)) * 100, 1);
    $row['score'] = $score;
    $responses[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facilities Feedback Analytics</title>
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
        
        .table-responsive {
            overflow-x: auto;
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
        <h1>📊 Facilities Feedback Analytics</h1>
        <p>Campus infrastructure feedback analysis and insights</p>
    </div>

    <div class="container">
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-box">
                <div class="number"><?= $totalResponses ?></div>
                <div class="label">Total Responses</div>
            </div>
            <div class="stat-box">
                <div class="number"><?= number_format(array_sum($averages) / count($averages), 1) ?>/5</div>
                <div class="label">Average Rating</div>
            </div>
            <div class="stat-box">
                <div class="number"><?= number_format((array_sum($averages) / count($averages)) / 5 * 100, 0) ?>%</div>
                <div class="label">Overall Satisfaction</div>
            </div>
        </div>

        <!-- Charts Section -->
        <?php if ($totalResponses > 0): ?>
        <div class="card">
            <h2 class="section-title">📈 Facility Ratings Analysis</h2>
            
            <div class="charts-grid">
                <!-- Bar Chart -->
                <div class="chart-container">
                    <div class="chart-title">Average Ratings per Facility</div>
                    <canvas id="barChart"></canvas>
                </div>
                
                <!-- Radar Chart -->
                <div class="chart-container">
                    <div class="chart-title">Comprehensive Rating Overview</div>
                    <canvas id="radarChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Feedback Responses Table -->
        <div class="card">
            <h2 class="section-title">📋 Detailed Student Feedback</h2>
            
            <?php if (!empty($responses)): ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Email</th>
                                <th>Branch</th>
                                <th>Year</th>
                                <th>Avg Rating</th>
                                <th>Satisfaction Score</th>
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
                                
                                $avgRating = 0;
                                for ($i = 1; $i <= 12; $i++) {
                                    $avgRating += ($r["q$i"] ?? 0);
                                }
                                $avgRating = number_format($avgRating / 12, 1);
                            ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($r['name']) ?></strong></td>
                                    <td><?= htmlspecialchars($r['email']) ?></td>
                                    <td><?= htmlspecialchars($r['branch'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($r['year'] ?? 'N/A') ?></td>
                                    <td style="text-align: center;"><?= $avgRating ?>/5</td>
                                    <td>
                                        <span class="score-badge <?= $scoreClass ?>">
                                            <?= $r['score'] ?>%
                                        </span>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($r['submitted_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p style="color: #666; text-align: center; padding: 2rem;">📭 No feedback responses yet.</p>
            <?php endif; ?>
        </div>
        <?php else: ?>
            <div class="card">
                <p style="color: #666; text-align: center; padding: 2rem; font-size: 1.1rem;">📭 No feedback data available yet.</p>
            </div>
        <?php endif; ?>

        <a href="../FeedBack_Form/StudentFeedbackOnFacilities.php" class="btn">← Back to Form</a>
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
                        "rgba(168, 85, 247, 0.8)",
                        "rgba(99, 102, 241, 0.8)",
                        "rgba(139, 92, 246, 0.8)",
                        "rgba(102, 126, 234, 0.8)",
                        "rgba(118, 75, 162, 0.8)",
                        "rgba(79, 172, 254, 0.8)",
                        "rgba(168, 85, 247, 0.8)",
                        "rgba(99, 102, 241, 0.8)",
                        "rgba(139, 92, 246, 0.8)"
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
                    },
                    x: {
                        ticks: {
                            font: { size: 11 }
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

    ########################################
    # 2️⃣ ALL Dept + Year Averages
    ########################################

    $groupData=[];

    $groupQuery = "SELECT department,year,$avgColumns
                   FROM alumni_feedback
                   GROUP BY department,year
                   ORDER BY department,year";

    $groupResult = mysqli_query($conn,$groupQuery);

    while($g=mysqli_fetch_assoc($groupResult)){

        $key = $g['department']." - ".$g['year']." Year";

        $avg=[];
        for($i=1;$i<=22;$i++){
            $avg[] = round($g["q$i"] ?? 0,2);
        }

        $groupData[$key] = $avg;
    }

    echo json_encode([
        "questions"=>$questions,
        "averages"=>$averages,
        "percentage"=>$percentage,
        "groups"=>$groupData
    ]);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Feedback Analytics</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<h2>
Welcome <?= $name?><br>
Dept: <?= $dept?><br>
Year: <?=  $year; ?>
</h2>

<h3>Your Dept + Year Performance</h3>
<canvas id="myChart"></canvas>
<h3 id="percent"></h3>

<hr>

<h2>Department & Year Wise Analysis</h2>
<div id="allCharts"></div>

<script>

fetch("../FeedBack_Form/StudentFeedbackOnFacilities.php?action=fetch")
.then(res=>res.json())
.then(data=>{

/* ===== USER GRAPH ===== */

document.getElementById("percent").innerHTML =
"Overall Score: " + data.percentage + "%";

new Chart(document.getElementById("myChart"),{
  type:"bar",
  data:{
    labels:data.questions,
    datasets:[{
      label:"Your Dept-Year Avg",
      data:data.averages,
      backgroundColor:"rgba(59,130,246,0.6)"
    }]
  },
  options:{ scales:{ y:{beginAtZero:true,max:5} } }
});


/* ===== ALL DEPT-YEAR GRAPHS ===== */

let container=document.getElementById("allCharts");

Object.keys(data.groups).forEach((group,index)=>{

    let title=document.createElement("h3");
    title.innerText=group;

    let canvas=document.createElement("canvas");
    canvas.id="chart"+index;

    container.appendChild(title);
    container.appendChild(canvas);

    new Chart(canvas,{
        type:"bar",
        data:{
            labels:data.questions,
            datasets:[{
                label:group+" Avg",
                data:data.groups[group],
                backgroundColor:"rgba(34,197,94,0.6)"
            }]
        },
        options:{ scales:{ y:{beginAtZero:true,max:5} } }
    });

});

});
</script>

</body>
</html>
