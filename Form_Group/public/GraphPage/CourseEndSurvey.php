<?php
$conn = mysqli_connect("localhost","root","","feedback_db");
if(!$conn){ die("DB Connection Failed"); }

$result = mysqli_query($conn,
"SELECT * FROM course_end_survey ORDER BY submitted_at DESC");

$facultyData = [];

while($r = mysqli_fetch_assoc($result)){
 $f = $r['faculty_name'];

 if(!isset($facultyData[$f])){
  $facultyData[$f] = [
   "dept"=>$r['department'],
   "year"=>$r['year_sem'],
   "objective"=>$r['objective'],
   "outcome"=>$r['outcome'],
   "students"=>[],
   "responses"=>[]
  ];
 }

 $facultyData[$f]['students'][] = $r['student_name'];
 $facultyData[$f]['responses'][] = [
  $r['q1'],$r['q2'],$r['q3'],$r['q4'],$r['q5'],
  $r['q6'],$r['q7'],$r['q8'],$r['q9'],$r['q10']
 ];
}

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
?>

<!DOCTYPE html>
<html>
<head>
<title>Course End Survey Report</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-100 font-serif">

<div class="max-w-6xl mx-auto mt-6 space-y-8">

<?php $idx=0; foreach($facultyData as $faculty=>$f): ?>
<?php
$avg=[];
for($i=0;$i<10;$i++){
 $sum=0;
 foreach($f['responses'] as $r){ $sum+=$r[$i]; }
 $avg[]=round($sum/count($f['responses']),2);
}
?>

<div class="bg-white p-6 shadow">
<h2 class="font-bold text-xl">Faculty: <?= $faculty ?></h2>
<p class="text-sm">Department: <?= $f['dept'] ?> | Year: <?= $f['year'] ?></p>

<p class="font-semibold mt-3">General Objective</p>
<p><?= $f['objective'] ?></p>

<p class="font-semibold mt-3">Course Outcomes</p>
<pre class="whitespace-pre-wrap"><?= $f['outcome'] ?></pre>

<canvas id="chart<?= $idx ?>" class="mt-4"></canvas>

<table class="w-full border mt-4 text-sm">
<thead class="bg-gray-200">
<tr><th class="border p-2">S.No</th><th class="border p-2">Student Name</th></tr>
</thead>
<tbody>
<?php foreach($f['students'] as $i=>$s): ?>
<tr>
<td class="border p-2 text-center"><?= $i+1 ?></td>
<td class="border p-2"><?= $s ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>

<script>
new Chart(document.getElementById("chart<?= $idx ?>"),{
 type:"bar",
 data:{
  labels:<?= json_encode($questions) ?>,
  datasets:[{label:"Average Rating",data:<?= json_encode($avg) ?>}]
 },
 options:{scales:{y:{beginAtZero:true,max:5,ticks:{stepSize:1}}}}
});
</script>

<?php $idx++; endforeach; ?>

</div>
</body>
</html>
