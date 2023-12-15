<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require_once('include/connect/dbcon.php');

if (isset($_POST['send'])) {
    // Get the email from the session
    $storedemail = $_SESSION['email'];

    // Fetch the OTP from the database
    $sql = "SELECT otp FROM members WHERE email = :email";
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':email', $storedemail, PDO::PARAM_STR);

    try {
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $storedotp = $result['otp'];
    } catch (PDOException $e) {
        echo "Error fetching OTP from the database: " . $e->getMessage();
        exit();
    }

    // Check if OTP is found in the database
    if (!empty($storedotp)) {
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'dhvsuidgen@gmail.com';
        $mail->Password = 'xatk watz coaj tkrh';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('dhvsuidgen@gmail.com', 'DHVSU ID Generator');
        $mail->addAddress($storedemail);

        $mail->isHTML(true);
        $subject = "OTP Verification";
        $message = "Your OTP is: $storedotp";
        $headers = "From: dhvsuidgen@gmail.com";

        try {
            $mail->send();
            echo 'Email sent successfully.';
        } catch (Exception $e) {
            echo 'Email could not be sent. Error: ' . $mail->ErrorInfo;
        }
    } else {
        echo 'OTP not found in the database.';
    }
}
?>

