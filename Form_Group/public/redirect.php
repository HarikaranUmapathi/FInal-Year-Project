<?php
session_start(); // ✅ MUST be first

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $year = $_POST["Year"];
    $department = $_POST["Department"];

    $_SESSION['year'] = $year;
    $_SESSION['dept'] = $department;
    

    if ($_SESSION['Role'] == "Student") {

        switch ($department) {

            case "CSE":
                switch ($year) {
                    case "1":
                        header("Location: FeedBack_Form/StudentFeedBackFormFaculty.php");
                        exit();
                    case "2":
                        header("Location: College_Department/CSE/2CSE.html");
                        exit();
                    case "3":
                        header("Location: College_Department/CSE/3CSE.html");
                        exit();
                    case "4":
                        header("Location: College_Department/CSE/4CSE.html");
                        exit();
                }
                break;

            case "ECE":
                switch ($year) {
                    case "1":
                        header("Location: College_Department/FirstYear/2FirstYear.html");
                        exit();
                    case "2":
                        header("Location: College_Department/ECE/2ECE.html");
                        exit();
                    case "3":
                        header("Location: College_Department/ECE/3ECE.html");
                        exit();
                    case "4":
                        header("Location: College_Department/ECE/4ECE.html");
                        exit();
                }
                break;
             case "AIDS":
                switch ($year) {
                    case "1":
                        header("Location: FeedBack_Form/StudentFeedBackFormFaculty.php");
                        exit();
                    case "2":
                        header("Location: College_Department/AIDS/2AIDS.html");
                        exit();
                    case "3":
                        header("Location: College_Department/AIDS/3AIDS.html");
                        exit();
                    case "4":
                        header("Location: College_Department/AIDS/4AIDS.html");
                        exit();
                }
                break;

            case "AIML":
                switch ($year) {
                    case "1":
                        header("Location: College_Department/FirstYear/2FirstYear.html");
                        exit();
                    case "2":
                        header("Location: College_Department/AIML/2AIML.html");
                        exit();
                    case "3":
                        header("Location: College_Department/AIML/3AIML.html");
                        exit();
                    case "4":
                        header("Location: College_Department/AIML/4AIML.html");
                        exit();
                }
                break;
             case "Civil":
                switch ($year) {
                    case "1":
                        header("Location: FeedBack_Form/StudentFeedBackFormFaculty.php");
                        exit();
                    case "2":
                        header("Location: College_Department/Civil/2Civil.html");
                        exit();
                    case "3":
                        header("Location: College_Department/Civil/3ivil.html");
                        exit();
                    case "4":
                        header("Location: College_Department/Civil/4Civil.html");
                        exit();
                }
                break;

            case "EEE":
                switch ($year) {
                    case "1":
                        header("Location: College_Department/FirstYear/2FirstYear.html");
                        exit();
                    case "2":
                        header("Location: College_Department/EEE/2EEE.html");
                        exit();
                    case "3":
                        header("Location: College_Department/EEE/3EEE.html");
                        exit();
                    case "4":
                        header("Location: College_Department/EEE/4EEE.html");
                        exit();
                }
                break;
             case "IT":
                switch ($year) {
                    case "1":
                        header("Location: FeedBack_Form/StudentFeedBackFormFaculty.php");
                        exit();
                    case "2":
                        header("Location: College_Department/IT/2IT.html");
                        exit();
                    case "3":
                        header("Location: College_Department/IT/3IT.html");
                        exit();
                    case "4":
                        header("Location: College_Department/IT/4IT.html");
                        exit();
                }
                break;

            case "MBA":
                switch ($year) {
                    case "1":
                        header("Location: College_Department/FirstYear/2FirstYear.html");
                        exit();
                    case "2":
                        header("Location: College_Department/MBA/2MBA.html");
                        exit();
                    case "3":
                        header("Location: College_Department/MBA/3MBA.html");
                        exit();
                    case "4":
                        header("Location: College_Department/MBA/4MBA.html");
                        exit();
                }
                break;
             case "MCA":
                switch ($year) {
                    case "1":
                        header("Location: FeedBack_Form/StudentFeedBackFormFaculty.php");
                        exit();
                    case "2":
                        header("Location: College_Department/MCA/2MCA.html");
                        exit();
                    case "3":
                        header("Location: College_Department/MCA/3MCA.html");
                        exit();
                    case "4":
                        header("Location: College_Department/MCA/4MCA.html");
                        exit();
                }
                break;

            case "mechanical":
                switch ($year) {
                    case "1":
                        header("Location: College_Department/FirstYear/2FirstYear.html");
                        exit();
                    case "2":
                        header("Location: College_Department/Mechanical/2Mechanical.html");
                        exit();
                    case "3":
                        header("Location: College_Department/Mechanical/3Mechanical.html");
                        exit();
                    case "4":
                        header("Location: College_Department/Mechanical/4Mechanical.html");
                        exit();
                }
                break;

            default:
                echo "Invalid Department";
        }

    }
    elseif ($_SESSION['Role'] == "Faculty") {

        switch ($department) {
            case "CSE":
                switch ($year) {
                    case "1":
                        header("Location: Faculty_Department/CSE/1CSEFaculty.html");
                        exit();
                    case "2":
                        header("Location: College_Department/CSE/2FCSE.html");
                        exit();
                    case "3":
                        header("Location: College_Department/CSE/3FCSE.html");
                        exit();
                    case "4":
                        header("Location: College_Department/CSE/4FCSE.html");
                        exit();
                }
                break;

            case "AIDS":
                switch ($year) {
                    case "1":
                        header("Location: Faculty_Department/CSE/1CSEFaculty.html");
                        exit();
                    case "2":
                        header("Location: College_Department/AIDS/2FAIDS.html");
                        exit();
                    case "3":
                        header("Location: College_Department/AIDS/3FAIDS.html");
                        exit();
                    case "4":
                        header("Location: College_Department/AIDS/4FAIDS.html");
                        exit();
                }
                break;
            default:
                echo "Invalid Department for Faculty";
        }

    }
    else {
        echo "Invalid Role";
    }
}
?>
