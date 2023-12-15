<?php
session_start();

// Check if the user is not authenticated
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: login.php');
    exit();
}

include 'include/connect/dbcon.php';
$html = '';

if (isset($_POST['search'])) {
    $id_no = $_POST['id_no'];

    try {
        // Check if a user is logged in
        if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true && isset($_SESSION['user']['id'])) {
            $user_id = $_SESSION['user']['id'];

            // Create a PDO connection
            $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);

            // Set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Modify the SQL query to include the user_id check
            $sql = "SELECT * FROM cards WHERE id_no=:id_no AND member_id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_no', $id_no, PDO::PARAM_STR);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $html = "<div class='card' style='width:350px; padding:0;' >";
                $html .= "";
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $name = $row["name"];
                    $id_no = $row["id_no"];
                    $grade = $row['grade'];
                    $dob = $row['dob'];
                    $address = $row['address'];
                    $email = $row['email'];
                    $exp_date = $row['exp_date'];
                    $phone = $row['phone'];
                    $address = $row['address'];
                    $image = $row['image'];
                    $date = date('M d, Y', strtotime($row['date']));
                    // updated signature
                    $signature = $row['signature'];

                    $html .= "
                        <!-- second id card  -->
                        <div class='container' style='text-align:left; border:2px dotted black;'>
                        <div class='header'></div>

                        <div class='container-2'>
                            <div class='box-1'>
                                <img src='$image'/>
                            </div>
                            <div class='box-2'>
                                <h2>$name</h2>
                                <p style='font-size: 14px; color: black'>Student</p>
                            </div>
                            <div class='box-3'>
                                <img src='assets/images/logo.png' alt=''>
                            </div>
                        </div>

                        <div class='container-3'>
                            <div class='info-1'>
                                <div class='id'>
                                    <h4>ID No</h4>
                                    <p>$id_no</p>
                                </div>
                                <div class='dob'>
                                    <h4>Phone</h4>
                                    <p>$phone</p>
                                </div>
                            </div>
                            <div class='info-2'>
                                <div class='join-date'>
                                    <h4>Date of birth</h4>
                                    <p>$date</p>
                                </div>
                                <div class='expire-date'>
                                    <h4>Expire Date</h4>
                                    <p>$exp_date</p>
                                </div>
                            </div>
                            <div class='info-3'>
                                <div class='email'>
                                    <h4>Address</h4>
                                    <p>$address</p>
                                </div>
                            </div>
                            <div class='info-4'>
                                <div class='sign'>
                                    <p style='font-size:12px;margin-bottom: 8px;'>Student signature</p>
                                    <p style='font-family: Dancing Script'>$signature</p>
                                </div>
                            </div>
                    ";
                }
            } else {
                // Display a message if the ID doesn't belong to the logged-in user
                $html = "<p>The ID number you entered is not yours or you're not the one who generated this ID.</p>";
            }
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        // Handle the error as needed
    } finally {
        // Close the database connection
        $conn = null;
    }
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dom-to-image/2.6.0/dom-to-image.js"></script>
    <title>ID Card Generator | <?php echo date("Y") ?></title>
    <link rel="stylesheet" href="style/Id-card.css">
  </head>
  <body>
  <nav class="navbar navbar-expand-lg navbar-dark" style="background: black;">
    <a class="navbar-brand" href="#"><img src="assets/images/logo.png" width="50px"></a>
    <li class="nav-item">
        <span class="navbar-text">Welcome to DHVSU ID-GENERATOR!</span>
    </li>
    <div class="navbar-nav ml-auto">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Home</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout</a>
            </li>
        </ul>
    </div>
</nav>
<div style="padding-top: 10px;">
            </div>
  <br>

<div class="row" style="margin: 0px 20px 5px 20px">
  <div class="col-sm-6">
    <div class="card jumbotron">
      <div class="card-body">
        <form class="form" method="POST" action="id-card.php">.
        <label for="exampleInputEmail1">Student Id Card No.</label>
        <input class="form-control mr-sm-2" type="search" placeholder="Enter Id Card No." name="id_no">
        <small id="emailHelp" class="form-text text-muted">Every student's should have unique Id no.</small>
        <br>
        <button class="btn btn-outline-primary my-2 my-sm-0" type="submit" name="search">Generate</button>
        </form>
      </div>
    </div>
  </div>
  <div class="col-sm-6">
      <div class="card">
          <div class="card-header" >
              Here is your Id Card
          </div>
          <hr>
          <?php
    // Assuming $html is the PHP variable you are checking // Assign the actual value or leave it as an empty string based on your logic
    if (!empty($html)) {
    ?>
        <button id="demo" class="downloadtable btn btn-primary" onclick="downloadtable()">Download Id Card</button>
        <hr>
        <div class="card-body" id="mycard">
        <?php echo $html; ?>
        </div>
    <?php
    }
    ?>
        <br>
     </div>
  </div>
</div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dom-to-image/2.6.0/dom-to-image.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
    <script src='Script/downloadtable.js'></script>
    <script src='Script/downloadURI.js'></script>
  </body>
</html>