<?php
include '../PHP/Config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $facility_type = $_POST['facility_type'];
    $sector = $_POST['sector'];
    $facility_name = $_POST['facility_name'];
    $registration_no = $_POST['registration_no'];
    $address = $_POST['address'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO facility (`Facility Type`, `Sector`, `Facility Name`, `MOH_PHSRC NUMBER`, `Address`, `Phone Number`, `Email Address`, `Password`)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $facility_type, $sector, $facility_name, $registration_no, $address, $phone_number, $email, $password);

    if ($stmt->execute()) {
        echo "Facility registered successfully!";
        // Optionally redirect
        // header("Location: login.html");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
