<?php
include '../PHP/Config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $nic_number = $_POST['nic_number'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO patients (`Title`, `First Name`, `Last Name`, `NIC`, `Phone Number`, `Email Address`, `Password`)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $title, $first_name, $last_name, $nic_number, $phone_number, $email, $password);

    if ($stmt->execute()) {
        echo "Signup successful!";
        // Optionally redirect to login page
        // header("Location: login.html");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
