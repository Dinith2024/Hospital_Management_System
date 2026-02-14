<?php
include '../PHP/Config.php'; // DB connection

$response = ['status'=>'error','message'=>''];

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';

    if($name && $email && $subject && $message) {
        $stmt = $conn->prepare("INSERT INTO contacts (name,email,subject,message,created_at) VALUES (?,?,?,?,NOW())");
        $stmt->bind_param("ssss",$name,$email,$subject,$message);
        if($stmt->execute()){
            $response['status'] = 'success';
            $response['message'] = 'Message sent successfully!';
        } else {
            $response['message'] = 'Database error. Please try again.';
        }
    } else {
        $response['message'] = 'Please fill all fields.';
    }
}

echo json_encode($response);
?>
