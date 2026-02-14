<?php
require_once __DIR__ . '/../../config/db.php';
session_start();

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
for ($i = 1; $i <= 4; $i++) {
    $averages[] = round($data["q$i"] ?? 0, 2);
}

/* ================= FETCH INDIVIDUAL RESPONSES ================= */
$listSql = "SELECT guest_name, designation, organization, submitted_at 
            FROM guest_feedback 
            ORDER BY submitted_at DESC";

$listResult = mysqli_query($conn, $listSql);
if (!$listResult) {
    die("List Query Error: " . mysqli_error($conn));
}

$responses = [];
while ($row = mysqli_fetch_assoc($listResult)) {
    $responses[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Feedback Graph</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<div class="max-w-5xl mx-auto bg-white p-6 mt-6 shadow">

<h2 class="text-center font-bold text-xl mb-4">
Guest Lecture Feedback Analysis
</h2>

<p class="font-semibold mb-4">
Total Responses: <?= $data['total'] ?>
</p>

<!-- AVERAGE CHART -->
<canvas id="chart"></canvas>

<!-- BACK LINK -->
<a href="feedback_form.php" class="text-blue-600 block mt-6 font-semibold">
← Back to Form
</a>

<!-- INDIVIDUAL RESPONSES TABLE -->
<?php if(!empty($responses)) { ?>
<div class="overflow-x-auto mt-6">
<table class="min-w-full border">
<thead class="bg-gray-200">
<tr>
<th class="border px-4 py-2">Guest Name</th>
<th class="border px-4 py-2">Designation</th>
<th class="border px-4 py-2">Organization</th>
<th class="border px-4 py-2">Submitted At</th>
</tr>
</thead>
<tbody>
<?php foreach($responses as $r) { ?>
<tr>
<td class="border px-4 py-2"><?= htmlspecialchars($r['guest_name']) ?></td>
<td class="border px-4 py-2"><?= htmlspecialchars($r['designation']) ?></td>
<td class="border px-4 py-2"><?= htmlspecialchars($r['organization']) ?></td>
<td class="border px-4 py-2"><?= htmlspecialchars($r['submitted_at']) ?></td>
</tr>
<?php } ?>
</tbody>
</table>
</div>
<?php } ?>

</div>

<script>
const averages = <?= json_encode($averages) ?>;

new Chart(document.getElementById("chart"), {
    type: "bar",
    data: {
        labels: [
            "Topic Relevance",
            "Presentation Clarity",
            "Interaction",
            "Overall Effectiveness"
        ],
        datasets: [{
            label: "Average Rating",
            data: averages,
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

</body>
</html>
