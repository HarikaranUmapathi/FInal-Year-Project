<?php

// Resolve config path reliably from this file's directory
require_once __DIR__ . '/../../config/db.php';
session_start();

$success = false;
$message = '';

// Ensure feedback table exists
$createTableSql = "CREATE TABLE IF NOT EXISTS feedback_facilities (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255),
  name VARCHAR(255),
  branch VARCHAR(100),
  year VARCHAR(50),
  number VARCHAR(20),
  q1 TINYINT, q2 TINYINT, q3 TINYINT, q4 TINYINT, q5 TINYINT, q6 TINYINT, q7 TINYINT, q8 TINYINT, q9 TINYINT, q10 TINYINT, q11 TINYINT, q12 TINYINT,
  submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
mysqli_query($conn, $createTableSql);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Email = mysqli_real_escape_string($conn, trim($_POST['Email'] ?? ''));
    $Name = mysqli_real_escape_string($conn, trim($_POST['Name'] ?? ''));
    $Branch = mysqli_real_escape_string($conn, trim($_POST['Branch'] ?? ''));
    $Year = mysqli_real_escape_string($conn, trim($_POST['Year'] ?? ''));
    $Number = mysqli_real_escape_string($conn, trim($_POST['Number'] ?? ''));

    // Collect answers
    $answers = [];
    for ($i = 1; $i <= 12; $i++) {
        $answers[$i] = isset($_POST["q$i"]) ? (int)$_POST["q$i"] : null;
    }

    // Simple validation: ensure all questions answered
    $allAnswered = true;
    foreach ($answers as $val) {
        if ($val === null) { $allAnswered = false; break; }
    }

    if (!$allAnswered) {
        $message = 'Please answer all questions.';
    } else {
        $types = 'sssss' . str_repeat('i', 12);
        $sql = "INSERT INTO feedback_facilities (email,name,branch,year,number,q1,q2,q3,q4,q5,q6,q7,q8,q9,q10,q11,q12) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, $types,
                $Email, $Name, $Branch, $Year, $Number,
                $answers[1], $answers[2], $answers[3], $answers[4], $answers[5], $answers[6], $answers[7], $answers[8], $answers[9], $answers[10], $answers[11], $answers[12]
            );
            if (mysqli_stmt_execute($stmt)) {
                $success = true;
                $message = 'Feedback submitted successfully!';
            } else {
                $message = 'Error saving feedback: ' . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        } else {
            $message = 'Database error: ' . mysqli_error($conn);
        }
    }
}

// Prepare questions array for chart labels
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

// Fetch aggregated averages
$avgSql = "SELECT COUNT(*) as cnt,
  ROUND(AVG(q1),2) as q1, ROUND(AVG(q2),2) as q2, ROUND(AVG(q3),2) as q3, ROUND(AVG(q4),2) as q4,
  ROUND(AVG(q5),2) as q5, ROUND(AVG(q6),2) as q6, ROUND(AVG(q7),2) as q7, ROUND(AVG(q8),2) as q8,
  ROUND(AVG(q9),2) as q9, ROUND(AVG(q10),2) as q10, ROUND(AVG(q11),2) as q11, ROUND(AVG(q12),2) as q12
  FROM feedback_facilities";
$res = mysqli_query($conn, $avgSql);
$row = mysqli_fetch_assoc($res);
$totalResponses = (int)($row['cnt'] ?? 0);
$averages = [];
for ($i = 1; $i <= 12; $i++) {
    $averages[] = isset($row["q$i"]) && $row["q$i"] !== null ? (float)$row["q$i"] : 0;
}

 ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Feedback on Facilities</title>

  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Chart.js for visualizations -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-white font-serif">

<!-- ===== COMMON WIDTH CONTAINER ===== -->
<div class="max-w-[900px] mx-auto p-4">

  <!-- ========== HEADER ========== -->
  <table class="w-full border border-black border-collapse">
    <tr>
      <!-- LOGO -->
      <td class="border border-black w-[120px] text-center p-2">
        <img src="../Images/CollegeLogo.png" alt="College Logo" class="w-20 mx-auto">
      </td>

      <!-- TITLE -->
      <td class="border border-black text-center font-bold text-xl tracking-wide">
        STUDENT FEEDBACK ON<br>FACILITIES
      </td>

      <!-- DOC DETAILS -->
      <td class="border border-black w-[260px] p-0">
        <table class="w-full border-collapse text-sm">
          <tr>
            <td class="border border-black px-2 py-1 font-semibold">Doc No:</td>
            <td class="border border-black px-2 py-1">CAHCET/ACD/R/18/ver-1.0</td>
          </tr>
          <tr>
            <td class="border border-black px-2 py-1 font-semibold">Version No:</td>
            <td class="border border-black px-2 py-1">1.0</td>
          </tr>
          <tr>
            <td class="border border-black px-2 py-1 font-semibold">Rev Date:</td>
            <td class="border border-black px-2 py-1">03.01.2025</td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

  <!-- ========== FORM ========== -->
  <form id="feedbackForm" method="post" class="border border-black mt-6 p-6">

    <p class="text-center font-bold">
      C. ABDUL HAKEEM COLLEGE OF ENGINEERING & TECHNOLOGY<br>
      Melvisharam – 632 509
    </p>

    <p class="text-center text-sm mt-2">
      STUDENT FEEDBACK ON FACILITIES (CAHCET/ACD/R/18/ver-1.0)<br>
      DEPARTMENT OF MECHANICAL ENGINEERING<br>
      BATCH: 2024–2025
    </p>

    <p class="text-red-600 text-sm mt-4">* Indicates required question</p>

    <!-- ===== STUDENT DETAILS ===== -->
    <div class="mt-6 space-y-6">

      <div>
        <label class="font-semibold block mb-1">Email *</label>
        <input type="email" name="Email" required
          class="w-full border-b border-black focus:outline-none">
      </div>

      <div>
        <label class="font-semibold block mb-1">Name of the Student *</label>
        <input type="text" name="Name" required
          class="w-full border-b border-black focus:outline-none">
      </div>

      <div>
        <label class="font-semibold block mb-1">Name of Branch *</label>
        <input type="text" name="Branch" required
          class="w-full border-b border-black focus:outline-none">
      </div>

      <div>
        <label class="font-semibold block mb-1">Year of Passing *</label>
        <input type="text" name="Year"
          class="w-full border-b border-black focus:outline-none">
      </div>

      <div>
        <label class="font-semibold block mb-1">Mobile Number *</label>
        <input type="tel" name="Number" required
          class="w-full border-b border-black focus:outline-none">
      </div>

    </div>

    <!-- ===== RATING SCALE INFO ===== -->
    <div class="mt-8">
      <p class="font-semibold">PLEASE EVALUATE BASED ON THE FOLLOWING</p>
      <p class="text-sm">5-Excellent | 4-Good | 3-Average | 2-Poor | 1-No Comment</p>
    </div>

    <!-- ===== QUESTIONS (1–12) ===== -->
    <div class="mt-8 space-y-8">

      <script>
        const questions = [
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

        questions.forEach((q, i) => {
          document.write(`
            <div>
              <p id=${i+1} class="font-semibold mb-2">${i + 1}. ${q} *</p>
              <div class="flex gap-6 text-sm">
                <label><input type="radio" name="q${i+1}" value="5"> 5</label>
                <label><input type="radio" name="q${i+1}" value="4"> 4</label>
                <label><input type="radio" name="q${i+1}" value="3"> 3</label>
                <label><input type="radio" name="q${i+1}" value="2"> 2</label>
                <label><input type="radio" name="q${i+1}" value="1"> 1</label>
              </div>
            </div>
          `);
        });
      </script>

    </div>

    <!-- ===== SUBMIT ===== -->
    <div class="mt-10 text-center">
      <button type="submit"
        class="px-10 py-2 border border-black font-semibold hover:bg-black hover:text-white transition">
        SUBMIT
      </button>
    </div>

  </form>
</div>

<!-- ========== AGGREGATED RESULTS ====== -->
<div class="max-w-[900px] mx-auto p-4 mt-6">
  <?php if (isset($message) && $message): ?>
    <div class="p-3 mb-4 border <?php echo $success ? 'border-green-600 text-green-700' : 'border-red-600 text-red-700'; ?>">
      <?php echo htmlspecialchars($message); ?>
    </div>
  <?php endif; ?>

  <?php if (isset($totalResponses) && $totalResponses > 0): ?>
    <h2 class="font-semibold mb-2">Aggregated Averages (<?php echo $totalResponses; ?> responses)</h2>
    <canvas id="avgChart" height="120"></canvas>
  <?php else: ?>
    <p class="text-sm text-gray-600">No feedback responses yet.</p>
  <?php endif; ?>
</div>

<!-- ========== JAVASCRIPT ========== -->
<script>
document.getElementById("feedbackForm").addEventListener("submit", function(e) {
  for (let i = 1; i <= 12; i++) {
    if (!document.querySelector(`input[name="q${i}"]:checked`)) {
      e.preventDefault();
      alert("Please answer Question " + i);
      return;
    }
  }
  // allow normal submission
});
</script>

<?php if (isset($averages) && $totalResponses > 0): ?>
<script>
  const labels = <?php echo json_encode($questions); ?>;
  const data = <?php echo json_encode($averages); ?>;
  const ctx = document.getElementById('avgChart').getContext('2d');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [{
        label: 'Average score',
        data: data,
        backgroundColor: 'rgba(59,130,246,0.6)',
        borderColor: 'rgba(59,130,246,1)',
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
