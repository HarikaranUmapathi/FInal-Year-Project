<?php
 if($_SERVER["REQUEST_METHOD"]=="POST"){
    $year=$_POST["Year"];
    $department=$_POST["Department"];
 
 session_start();
 $_SESSION['year']=$year;
   

     if ($department == "CSE") {
         if($year=="1"){
            header("Location: College_Department/FirstYear/2FirstYear.html");
            exit();
        }
        elseif($year=="2"){
            header("Location: College_Department/CSE/2CSE.html");
            exit();
        }
        elseif($year=="3"){
            header("Location: College_Department/CSE/3CSE.html");
            exit();
        }
        elseif($year=="4"){
            header("Location: College_Department/CSE/4CSE.html");
            exit();
        }
    
    } 

    elseif ($department == "ECE") {
        if($year=="1"){
            header("Location: College_Department/FirstYear/2FirstYear.html");
            exit();
        }
        elseif($year=="2"){
            header("Location: College_Department/ECE/2ECE.html");
            exit();
        }
        elseif($year=="3"){
            header("Location: College_Department/ECE/3ECE.html");
            exit();
        }
        elseif($year=="4"){
            header("Location: College_Department/ECE/4ECE.html");
            exit();
        }
       
    } 

    elseif ($department == "MECH") {
        if($year=="1"){
            header("Location: College_Department/FirstYear/2FirstYear.html");
            exit();
        }
        elseif($year=="2"){
            header("Location: College_Department/Mechanical/2MEchanical.html");
            exit();
        }
        elseif($year=="3"){
            header("Location: College_Department/Mechanical/3MEchanical.html");
            exit();
        }
        elseif($year=="4"){
            header("Location: College_Department/Mechanical/4MEchanical.html");
            exit();
        }
    } 

    elseif ($department == "CIVIL") {
        if($year=="1"){
            header("Location: College_Department/FirstYear/2FirstYear.html");
            exit();
        }
        elseif($year=="2"){
            header("Location: College_Department/Civil/2Civil.html");
            exit();
        }
        elseif($year=="3"){
            header("Location: College_Department/Civil/3Civil.html");
            exit();
        }
        elseif($year=="4"){
            header("Location: College_Department/Civil/4Civil.html");
            exit();
        }
    } 

     elseif ($department == "IT") {
        if($year=="1"){
            header("Location: College_Department/FirstYear/2FirstYear.html");
            exit();
        }
        elseif($year=="2"){
            header("Location: College_Department/IT/2IT.html");
            exit();
        }
        elseif($year=="3"){
            header("Location: College_Department/IT/3IT.html");
            exit();
        }
        elseif($year=="4"){
            header("Location: College_Department/IT/4IT.html");
            exit();
        }
    }

    elseif ($department == "MBA") {
        if($year=="1"){
            header("Location: College_Department/MBA/1MBA.html");
            exit();
        }
        elseif($year=="2"){
            header("Location: College_Department/MBA/2MBA.html");
            exit();
        }
    } 

    elseif ($department == "MCA") {
        if($year=="1"){
            header("Location: College_Department/MCA/1MCA.html");
            exit();
        }
        elseif($year=="2"){
            header("Location: College_Department/MCA/2MCA.html");
            exit();
        }
       
    } 

     elseif ($department == "EEE") {
        if($year=="1"){
            header("Location: College_Department/FirstYear/2FirstYear.html");
            exit();
        }
        elseif($year=="2"){
            header("Location: College_Department/EEE/2EEE.html");
            exit();
        }
        elseif($year=="3"){
            header("Location: College_Department/EEE/3EEE.html");
            exit();
        }
        elseif($year=="4"){
            header("Location: College_Department/EEE/4EEE.html");
            exit();
        }
    } 

    elseif ($department == "FIRSTYEAR") {
        if($year=="1"){
            header("Location: College_Department/FirstYear/2FirstYear.html");
            exit();
        }
        elseif($year=="2"){
            header("Location: College_Department/FirstYear/2FirstYear.html");
            exit();
        }
        elseif($year=="3"){
            header("Location: College_Department/FirstYear/3FirstYear.html");
            exit();
        }
        elseif($year=="4"){
            header("Location: College_Department/FirstYear/4FirstYear.html");
            exit();
        }
    } 

    elseif ($department == "AIDS") {
        if($year=="1"){
            header("Location: College_Department/FirstYear/2FirstYear.html");
            exit();
        }
        elseif($year=="2"){
            header("Location: College_Department/AIDS/2AIDS.html");
            exit();
        }
        elseif($year=="3"){
            header("Location: College_Department/AIDS/3AIDS.html");
            exit();
        }
        elseif($year=="4"){
            header("Location: College_Department/AIDS/4AIDS.html");
            exit();
        }
    } 

    elseif ($department == "AIML") {
        if($year=="1"){
            header("Location: College_Department/FirstYear/2FirstYear.html");
            exit();
        }
        elseif($year=="2"){
            header("Location: College_Department/AIML/2AIML.html");
            exit();
        }
        elseif($year=="3"){
            header("Location: College_Department/AIML/3AIML.html");
            exit();
        }
        elseif($year=="4"){
            header("Location: College_Department/AIML/4AIML.html");
            exit();
        }
    } 

    else {
        echo "Invalid Department";
    }
}
?>