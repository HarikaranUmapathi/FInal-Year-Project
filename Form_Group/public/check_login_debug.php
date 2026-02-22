<?php
require_once '../config/db.php';

echo "--- Database Debugger ---\n";

if ($conn) {
    echo "Database connection successful.\n";
} else {
    echo "Database connection failed.\n";
    exit();
}

$names_to_check = ['Hari U', 'Faculty A'];

foreach ($names_to_check as $name) {
    echo "\nChecking for user: " . $name . "\n";
    
    // Check Students
    echo " Checking 'Students' table...\n";
    $stmt = $conn->prepare("SELECT * FROM students WHERE Name = ?");
    if (!$stmt) {
        echo "Prepare failed: (" . $conn->errno . ") " . $conn->error . "\n";
    } else {
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row) {
            echo "Found in Students!\n";
            echo "Columns found: " . implode(", ", array_keys($row)) . "\n";
            echo "Role: " . $row['role'] . "\n";
            echo "Stored Hash: " . $row['password'] . "\n";
            check_common_passwords($row['password']);
        } else {
            echo "Not found in Students.\n";
        }
        $stmt->close();
    }

    // Check Faculty
    echo " Checking 'Faculty' table...\n";
    $stmt = $conn->prepare("SELECT * FROM Faculty WHERE Name = ?");
    if (!$stmt) {
        echo "Prepare failed: (" . $conn->errno . ") " . $conn->error . "\n";
        // Maybe table doesn't exist?
        $check_table = $conn->query("SHOW TABLES LIKE 'Faculty'");
        if ($check_table->num_rows == 0) {
            echo "Table 'Faculty' does not exist!\n";
        }
    } else {
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row) {
            echo "Found in Faculty!\n";
            echo "Role: " . $row['role'] . "\n";
            echo "Stored Hash: " . $row['password'] . "\n";
            check_common_passwords($row['password']);
        } else {
            echo "Not found in Faculty.\n";
        }
        $stmt->close();
    }
    echo "--------------------------\n";
}

function check_common_passwords($hash) {
    $candidates = ['password', '123456', 'admin', 'root', 'student', 'faculty', 'Hari U', 'test'];
    echo "Checking common passwords against hash...\n";
    foreach ($candidates as $cand) {
        if (password_verify($cand, $hash)) {
            echo "*** MATCH FOUND! The password is: " . $cand . " ***\n";
            return;
        }
    }
    echo "No match found among common candidates.\n";
}
?>
