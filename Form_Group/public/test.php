<?php
require_once __DIR__ . '/../config/db.php';

echo "Connection successful<br>";

$query = "SHOW TABLES";
$result = mysqli_query($conn, $query);

if ($result) {
    echo "Tables in database:<br>";
    while ($row = mysqli_fetch_row($result)) {
        echo $row[0] . "<br>";
    }
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>