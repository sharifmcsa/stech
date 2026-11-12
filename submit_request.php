<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["customer_name"] ?? "");
    $mobile = trim($_POST["mobile"] ?? "");
    $address = trim($_POST["address"] ?? "");
    $service = trim($_POST["service_type"] ?? "");
    $problem = trim($_POST["problem_description"] ?? "");

    if ($name == "" || $mobile == "" || $address == "" ||
        $service == "" || $problem == "") {
        die("Please fill in all fields.");
    }

    $sql = "INSERT INTO service_requests
            (customer_name, mobile, address, service_type, problem_description)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $name, $mobile, $address, $service, $problem);

    if ($stmt->execute()) {
        echo "<script>
            alert('Your service request submitted successfully!');
            window.location.href='index.html';
        </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>