<?php
session_start();
require_once('include\connect\dbcon.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require_once('include/connect/dbcon.php');

if (!isset($_SESSION['email'])) {
    // Redirect to the login page or any other appropriate action
    header("Location: login.php");
    exit();
}

if (isset($_POST['submit'])) {
    $email = $_SESSION['email'];
    $otpver = $_POST['otpver'];

    // Fetch the stored OTP from the database
    $sql = "SELECT otp FROM members WHERE email = :email";
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);

    try {
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $storedotp = $result['otp'];

        // Check if entered OTP is empty
        if (empty($otpver)) {
            $errors[] = 'Verification code cannot be empty.';
        } elseif (!empty($storedotp) && $otpver == $storedotp) {
            // Update member_ver to 'verified' in the members table
            $sqlUpdate = "UPDATE members SET member_ver = '1', otp = 'verified' WHERE email = :email";
            $stmtUpdate = $con->prepare($sqlUpdate);
            $stmtUpdate->bindParam(':email', $email, PDO::PARAM_STR);

            try {
                $stmtUpdate->execute();
                echo "Member verified successfully!";
            } catch (PDOException $e) {
                $errors[] = 'Error updating member_ver: ' . $e->getMessage();
            }

            // Clear the OTP in the session
            unset($_SESSION['email']);

            sleep(5);
            header("Location: login.php");
            exit();
        } else {
            $errors[] = 'Incorrect verification code. Please try again.';
        }
    } catch (PDOException $e) {
        $errors[] = 'Error fetching OTP from the database: ' . $e->getMessage();
    }
}

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
        $body = "Your OTP is: $storedotp";
        $headers = "From: dhvsuidgen@gmail.com";

        $mail->Body = $body;

        try {
            $mail->send();
            $success = 'The OTP was sent successfully.';
        } catch (Exception $e) {
            echo 'Email could not be sent. Error: ' . $mail->ErrorInfo;
        }
    } else {
        echo 'OTP not found in the database.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>Register Page</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <style>
        .custom-width {
            width: 600; /* Adjust the width as needed */
        }
        body {
            background-image: url('assets/images/background.jpg');
            height: 100vh;
        }
    </style>
</head>
<body class="bg-image">
    <div class="container h-100">
        <div class="row h-100 mt-5 justify-content-center align-items-center">
            <div class="col-md-10 mt-3 pt-2 pb-5 align-self-center border bg-light">
                <?php 
                    if(isset($errors) && count($errors) > 0) {
                        foreach($errors as $error_msg) {
                            echo '<div class="alert alert-danger">'.$error_msg.'</div>';
                        }
                    }
                    if(isset($success)) {
                        echo '<div class="alert alert-success">'.$success.'</div>';
                    }
                ?>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-6 text-center">
                            <img src="assets/images/logo.png" alt="Your Logo" class="img-fluid mb-3" width="100" height="150">
                            <h1 class="mx-auto">Verification</h1>
                            <form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>">
                                <div class="form-group">
                                <label for="otpname">We successfully emailed "<?php $email = $_SESSION['email']; echo $email;?>" for verification purposes. Please enter the OTP</label>
                                <label for="firstname">Enter the OTP:</label>
                                <input type="text" name="otpver" placeholder="Enter the OTP" class="form-control custom-width" value="">
                                </div>
                                <button type="submit" name="send" onclick="" class="btn btn-primary">Send</button>
                                <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                                <label></label>
                                <p class="mx-auto" style="width: 100px;"> Back to <a href="login.php">Login</a></p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
