<?php

include "db.php";

$username = "admin";
$plain_password = "admin123";

$hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

$sql = "INSERT INTO admins (username, password) VALUES (?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("SQL Prepare Error: " . $conn->error);
}

$stmt->bind_param("ss", $username, $hashed_password);

if ($stmt->execute()) {
    echo "Admin Created Successfully!";
    echo "<br>Username: admin";
    echo "<br>Password: admin123";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();

?>