<?php
session_start();
include '../PHP/Config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $doctor_id = $_POST['doctor_id'];
    $specialization = $_POST['specialization'];
    $qualifications = $_POST['qualifications'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $profileImagePath = null;

    if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['profileImage']['tmp_name'];
        $fileName = $_FILES['profileImage']['name'];
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = 'doctor_'.$doctor_id.'_'.time().'.'.$ext;
        $uploadDir = '../Images/Doctors/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $destination = $uploadDir.$newFileName;

        if (move_uploaded_file($fileTmp, $destination)) {
            $profileImagePath = 'Images/Doctors/'.$newFileName;
        }
    }

    if ($profileImagePath) {
        $sql = "UPDATE doctors SET `Specialization`=?, `Qualifications`=?, `Email Address`=?, `Phone Number`=?, `Profile Image`=? WHERE `Doctor ID`=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $specialization, $qualifications, $email, $phone, $profileImagePath, $doctor_id);
    } else {
        $sql = "UPDATE doctors SET `Specialization`=?, `Qualifications`=?, `Email Address`=?, `Phone Number`=? WHERE `Doctor ID`=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $specialization, $qualifications, $email, $phone, $doctor_id);
    }

    $stmt->execute();
    $stmt->close();
    $conn->close();

    header("Location: ../HTML/DoctorDashboard.php");
    exit;
}
?>
