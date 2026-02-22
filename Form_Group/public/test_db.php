<?php
require_once '../config/db.php';
if ($conn) {
    echo "Database connection: SUCCESS\n";
    $result = mysqli_query($conn, "SELECT 1");
    if ($result) {
        echo "Database query: SUCCESS\n";
    } else {
        echo "Database query: FAILED - " . mysqli_error($conn) . "\n";
    }
} else {
    echo "Database connection: FAILED\n";
}
?>