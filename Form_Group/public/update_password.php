<?php
require_once __DIR__ . '/../config/db.php';

$name = "Faculty A";
$new_password = "123456";
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

echo "Attempting to update password for user: " . $name . "\n";
echo "New password: " . $new_password . "\n";
echo "New hash length: " . strlen($hashed_password) . "\n";

$stmt = $conn->prepare("UPDATE faculty1 SET Password = ? WHERE Name = ?");
$stmt->bind_param("ss", $hashed_password, $name);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo "SUCCESS: Password updated successfully.\n";
    } else {
         echo "WARNING: Query executed but no rows affected. User might not exist or password was already the same.\n";
    }
} else {
    echo "ERROR: Failed to update password. " . $stmt->error . "\n";
}
?>
