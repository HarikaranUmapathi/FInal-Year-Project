<?php
require_once __DIR__ . '/../config/db.php';

function checkTable($conn, $tableName) {
    echo "--- Schema for table: $tableName ---\n";
    $result = $conn->query("DESCRIBE $tableName");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo $row['Field'] . " | " . $row['Type'] . "\n";
        }
    } else {
        echo "Table '$tableName' not found or error: " . $conn->error . "\n";
    }
    echo "\n";
}

// Get all tables
$result = $conn->query("SHOW TABLES");
$tables = [];
while ($row = $result->fetch_array()) {
    $tables[] = $row[0];
}

foreach ($tables as $table) {
    checkTable($conn, $table);
}
?>
