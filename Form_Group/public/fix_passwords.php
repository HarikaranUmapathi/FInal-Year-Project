<?php
require_once __DIR__ . '/../config/db.php';

echo "--- Password Reset Script ---\n";

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error() . "\n");
}

$new_password = "123";
$new_hash = password_hash($new_password, PASSWORD_DEFAULT);

echo "New password will be: '$new_password'\n";
echo "New hash: $new_hash\n\n";

$users_to_update = [
    ['table' => 'students', 'name' => 'Hari U'],
    ['table' => 'Faculty', 'name' => 'Faculty A'],
    ['table' => 'Faculty', 'name' => 'Principal'],
    ['table' => 'Faculty', 'name' => 'HOD']
];

foreach ($users_to_update as $user) {
    $table = $user['table'];
    $name = $user['name'];
    
    echo "Updating user '$name' in table '$table'...\n";
    
    $stmt = $conn->prepare("UPDATE $table SET password = ? WHERE Name = ?");
    if ($stmt) {
        $stmt->bind_param("ss", $new_hash, $name);
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            echo "SUCCESS: Password updated.\n";
        } else {
            echo "WARNING: No rows updated. User might not exist or password was already same.\n";
        }
        $stmt->close();
    } else {
        echo "ERROR: Prepare failed - " . $conn->error . "\n";
    }
    echo "--------------------------\n";
}

echo "Password reset complete.\n";
?>
