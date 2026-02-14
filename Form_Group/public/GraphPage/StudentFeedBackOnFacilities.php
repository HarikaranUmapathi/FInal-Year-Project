<?php
require_once __DIR__ . '/../../config/db.php';
session_start();

$dept = $_SESSION['dept'];
$year = $_SESSION['year'];
$name=$_SESSION['name'];

# Questions list
$questions = [];
for ($i=1;$i<=22;$i++){
    $questions[] = "Q".$i;
}

# Build AVG columns dynamically
$avgColumns="";
for ($i=1;$i<=22;$i++){
    $avgColumns .= "AVG(q$i) q$i,";
}
$avgColumns = rtrim($avgColumns, ",");

#################################################
# AJAX FETCH DATA
#################################################
if(isset($_GET['action'])){

    ########################################
    # 1️⃣ Logged-in Student Dept+Year Average
    ########################################

    $sql = "SELECT $avgColumns FROM alumni_feedback
            WHERE department='$dept' AND year='$year'";

    $result = mysqli_query($conn,$sql);
    $row = mysqli_fetch_assoc($result);

    $averages=[];
    $total=0;

    for($i=1;$i<=22;$i++){
        $val = round($row["q$i"] ?? 0,2);
        $averages[]=$val;
        $total += $val;
    }

    $percentage = round(($total/(22*5))*100,2);

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
