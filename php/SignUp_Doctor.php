<?php
include '../PHP/Config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $nic_number = $_POST['nic_number'];
    $facility_name = $_POST['facility_name'];
    $license_number = $_POST['license_number'];
    $specialization = $_POST['specialization'];
    $experience = $_POST['experience'];
    $qualifications = $_POST['qualifications'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO doctors (`Title`, `First Name`, `Last Name`, `NIC`, `Facility Name`, `License Number`, `Specialization`, `Experience`, `Qualifications`, `Phone Number`, `Email Address`, `Password`)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssisissss", $title, $first_name, $last_name, $nic_number, $facility_name, $license_number, $specialization, $experience, $qualifications, $phone_number, $email, $password);

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