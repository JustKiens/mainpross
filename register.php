<?php
session_start();
require_once('include\connect\dbcon.php');

function generateOTP() {
    return rand(100000, 999999);
}

if(isset($_POST['submit'])) {
    if(isset($_POST['first_name'],$_POST['last_name'],$_POST['email'],$_POST['password']) && !empty($_POST['first_name']) && !empty($_POST['last_name']) && !empty($_POST['email']) && !empty($_POST['password'])) {
        $firstName = trim($_POST['first_name']);
        $lastName = trim($_POST['last_name']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $options = array("cost"=>4);
        $hashPassword = password_hash($password,PASSWORD_BCRYPT,$options);
        $member_s = 0;

        if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $sql = 'select * from members where email = :email';
            $stmt = $con->prepare($sql);
            $p = ['email'=>$email];
            $stmt->execute($p);

            $otp = generateOTP();
            
            if($stmt->rowCount() == 0) {
                $sql = "INSERT INTO members (first_name, last_name, email, password, member_ver, otp) VALUES (:fname, :lname, :email, :pass, :member_s, :otp)";
                try {
                    $handle = $con->prepare($sql);
                    $params = [
                        ':fname'=>$firstName,
                        ':lname'=>$lastName,
                        ':email'=>$email,
                        ':pass'=>$hashPassword,
                        ':member_s'=>$member_s,
                        ':otp'      => $otp
                    ];
                    
                    $handle->execute($params);

                    // Store the OTP in the session
                    $_SESSION['email'] = $email;
                    
                    // Redirect to the verification page
                    sleep(5);
                    header("Location: verification.php");
                    exit();
                }
                catch(PDOException $e) {
                    $errors[] = $e->getMessage();
                }
            }
            else {
                $valFirstName = $firstName;
                $valLastName = $lastName;
                $valEmail = '';
                $valPassword = $password;

                $errors[] = 'Email address already registered';
            }
        }
        else {
            $errors[] = "Email address is not valid";
        }
    }
    else {
        if(!isset($_POST['first_name']) || empty($_POST['first_name'])) {
            $errors[] = 'First name is required';
        }
        else {
            $valFirstName = $_POST['first_name'];
        }
        if(!isset($_POST['last_name']) || empty($_POST['last_name'])) {
            $errors[] = 'Last name is required';
        }
        else {
            $valLastName = $_POST['last_name'];
        }

        if(!isset($_POST['email']) || empty($_POST['email'])) {
            $errors[] = 'Email is required';
        }
        else {
            $valEmail = $_POST['email'];
        }

        if(!isset($_POST['password']) || empty($_POST['password'])) {
            $errors[] = 'Password is required';
        }
        else {
            $valPassword = $_POST['password'];
        }
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
            width: 300; /* Adjust the width as needed */
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
            <div class="col-md-5 mt-3 pt-2 pb-5 align-self-center border bg-light">
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
                            <h1 class="mx-auto">Registration</h1>
                            <form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>">
                                <div class="form-group">
                                <label for="firstname">First Name:</label>
                                    <input type="text" name="first_name" placeholder="Enter First Name" class="form-control custom-width" value="<?php echo ($valFirstName??'')?>">
                                </div>
                                <div class="form-group">
                                <label for="lastname">Last Name:</label>
                                    <input type="text" name="last_name" placeholder="Enter Last Name" class="form-control custom-width" value="<?php echo ($valLastName??'')?>" >
                                </div>
                                <div class="form-group">
                                <label for="email">Email:</label>
                                    <input type="text" name="email" placeholder="Enter Email" class="form-control custom-width" value="<?php echo ($valEmail??'')?>" required>
                                </div>
                                <div class="form-group">
                                <label for="password">Password</label>
                                    <input type="password" name="password" placeholder="Enter Password" class="form-control custom-width" value="<?php echo ($valPassword??'')?>">
                                </div>
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
