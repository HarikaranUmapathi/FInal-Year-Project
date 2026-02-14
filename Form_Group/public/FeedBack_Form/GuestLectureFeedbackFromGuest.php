<?php
require_once __DIR__ . '/../../config/db.php';
session_start();

$success = false;
$message = "";
/* Create DB & Table automatically */
$conn->query("CREATE DATABASE IF NOT EXISTS feedback_db");
$conn->select_db($db);

$conn->query("
CREATE TABLE IF NOT EXISTS guest_feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    guest_name VARCHAR(100),
    designation VARCHAR(100),
    organization VARCHAR(200),
    subject VARCHAR(200),
    objective TEXT,
    q1 INT,
    q2 INT,
    q3 INT,
    q4 INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
");

/* ======================
   INSERT DATA
====================== */

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $stmt = $conn->prepare("INSERT INTO guest_feedback
    (guest_name,designation,organization,subject,objective,q1,q2,q3,q4)
    VALUES (?,?,?,?,?,?,?,?,?)");

    $stmt->bind_param("sssssssss",
        $_POST['guestName'],
        $_POST['designation'],
        $_POST['org'],
        $_POST['subject'],
        $_POST['objective'],
        $_POST['q1'],
        $_POST['q2'],
        $_POST['q3'],
        $_POST['q4']
    );

    $stmt->execute();
    $msg = "Feedback Saved Successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Guest Feedback Form</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<div class="max-w-4xl mx-auto bg-white p-6 mt-6 shadow">

<h2 class="text-center font-bold text-xl">
Guest Lecture Feedback Form
</h2>

<?php if($msg){ ?>
<p class="text-green-600 text-center font-semibold mt-2">
<?= $msg ?>
</p>
<?php } ?>

<form method="POST" class="space-y-4 mt-6">

<input name="guestName" placeholder="Guest Name" class="border p-2 w-full" required>
<input name="designation" placeholder="Designation" class="border p-2 w-full">
<input name="org" placeholder="Organization" class="border p-2 w-full">
<input name="subject" placeholder="Subject" class="border p-2 w-full">
<textarea name="objective" placeholder="Objective" class="border p-2 w-full"></textarea>

<p class="font-bold mt-4">Ratings (5-Excellent | 1-Poor)</p>

<?php
$questions = [
"Topic relevance",
"Presentation clarity",
"Student interaction",
"Overall effectiveness"
];

for($i=1;$i<=4;$i++){
echo "<div>";
echo "<label class='font-medium'>{$questions[$i-1]}</label><br>";
for($j=1;$j<=5;$j++){
echo "<label class='mr-3'>
<input type='radio' name='q$i' value='$j' required> $j
</label>";
}
echo "</div>";
}
?>

<button class="bg-blue-600 text-white px-6 py-2 rounded mt-4">
Submit Feedback
</button>

</form>

<a href="feedback_graph.php" class="text-blue-600 mt-6 block font-semibold">
View Feedback Graph →
</a>

</div>
</body>
</html>
