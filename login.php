<?php
session_start();

// for pdo connection
require_once('include/connect/dbcon.php');

$errors = array(); // Initialize an empty array to store errors

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($email) || empty($password)) {
        $errors[] = "Email and Password are required";
    } else {
        try {
            // Use prepared statements to prevent SQL injection
            $sql = "SELECT id, email, password, member_ver FROM members WHERE email = :email";
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $getRow = $stmt->fetch(PDO::FETCH_ASSOC);

                // Verify the password
                if (password_verify($password, $getRow['password'])) {
                    unset($getRow['password']);

                    // Store member's ID and email in the session
                    $_SESSION['user_id'] = $getRow['id'];
                    $_SESSION['email'] = $getRow['email'];
                    
                    // Check if the user is verified (member_ver is 1)
                    if ($getRow['member_ver'] == 1) {
                        $_SESSION['authenticated'] = true;
                        $_SESSION['user'] = $getRow;
                        header('location: index.php');
                        exit();
                    } else {
                        // Redirect to verification page
                        header('location: verification.php');
                        exit();
                    }
                } else {
                    $errors[] = "The password you entered was not valid.";
                }
            } else {
                $errors[] = "Account with this Email does not exist";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        } finally {
            $con = null; // Change $conn to $con based on your variable names
        }
    }
}
?>




<!doctype html>
<html>
<head>
<title>Login Page</title>
<meta charset="utf-8">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
</head>
<body class="bg-image"
    style="background-image: url('assets/images/background.jpg'); height: 100vh";>
    <div class="container h-100">
    <div class="row h-100 mt-5 justify-content-center align-items-center">
        <div class="col-md-10 mt-5 pt-2 pb-5 align-self-center border bg-light">
            <?php 
                if(isset($errors) && count($errors) > 0)
                {
                    foreach($errors as $error_msg)
                    {
                        echo '<div class="alert alert-danger">'.$error_msg.'</div>';
                    }
                }
            ?>
                    <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <img src="assets/images/logo.png" alt="Your Logo" class="img-fluid mb-3" width="100" height="150">
            <h1 class="mx-auto">Welcome to ID-Generator</h1>
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>">
                <div class="form-group">
                    <input type="email" name="email" placeholder="Enter Email" class="form-control" aria-describedby="emailHelp">
                    <small>We will not gonna share your email with everyone.</small>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Enter Password" class="form-control">
                    <small>Never share your password!</small>
                </div>

                <div class="text-center">
                    <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                    <a href="register.php" class="btn btn-primary">Register</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>