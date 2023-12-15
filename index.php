<?php
session_start();
include('include/connect/dbcon.php');

// why false? because me not using it

$insert = false;
$update = false;
$empty = false;
$delete = false;
$already_card = false;
$loggedUserName = "";

// for verification na nakalogin na ba si user
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('location: login.php');
    $loggedUserName = isset($_POST['first_name']);
    exit();
}

if(isset($_GET['delete'])){
  // Get the sno parameter from the GET request
  $sno = $_GET['delete'];
  $delete = true;  // You can use $delete later if needed, but it's not currently used in the provided code block

  try {
      // Establish a PDO connection to the database
      $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      // Prepare and execute the DELETE SQL query
      $sql = "DELETE FROM `cards` WHERE `sno` = :sno";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(':sno', $sno, PDO::PARAM_INT);
      $stmt->execute();

  } catch (PDOException $e) {
      // Handle any exceptions (errors) that might occur during the database operation
      echo "Error: " . $e->getMessage();
  } finally {
      // Close the database connection in the finally block to ensure it's always closed
      $conn = null;
  }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    if (isset($_POST['snoEdit'])){
        $sno = $_POST["snoEdit"];
        $name = $_POST["nameEdit"];
        $id_no = $_POST["id_noEdit"];
        $loggedUserName = isset($_POST['first_name']);

        try {
            $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // for record update ------
            $sql = "UPDATE `cards` SET `name` = :name, `id_no` = :id_no WHERE `cards`.`sno` = :sno";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':sno', $sno, PDO::PARAM_INT);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':id_no', $id_no, PDO::PARAM_STR);
            $stmt->execute();

            $update = true;

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        } finally {
            $conn = null;
        }
    } else {
        $name = $_POST["name"];
        $id_no = $_POST["id_no"];
        $grade = $_POST['grade'];
        $dob = $_POST['dob'];
        $address = $_POST['address'];
        $email = $_POST['email'];
        $exp_date = $_POST['exp_date'];
        $phone = $_POST['phone'];
        $signature = $_POST['signature'];

        if ($name == '' || $id_no == ''){
            $empty = true;
        } else {
            try {
                $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);

                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                // for member id in the session
                $member_id = $_SESSION['user_id'];

                // Check if card no. is already registered
                $stmt_check_card = $conn->prepare("SELECT * FROM cards WHERE id_no= :id_no");
                $stmt_check_card->bindParam(':id_no', $id_no, PDO::PARAM_STR);
                $stmt_check_card->execute();

                if ($stmt_check_card->rowCount() > 0) {
                    $already_card = true;
                } else {
                    // Image upload 
                    $uploaddir = 'assets/uploads/';
                    $uploadfile = $uploaddir . basename($_FILES['image']['name']);

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile)) {
                        
                    } else {
                       
                    }

                    // for inserting new record
                    $stmt_insert = $conn->prepare("INSERT INTO `cards`(`name`, `id_no`, `email`, `phone`, `address`, `dob`, `exp_date`, `image`, `signature`, `member_id`) 
                    VALUES (:name, :id_no, :email, :phone, :address, :dob, :exp_date, :image, :signature, :member_id)");
                        $stmt_insert->bindParam(':name', $name, PDO::PARAM_STR);
                        $stmt_insert->bindParam(':id_no', $id_no, PDO::PARAM_STR);
                        $stmt_insert->bindParam(':email', $email, PDO::PARAM_STR);
                        $stmt_insert->bindParam(':phone', $phone, PDO::PARAM_STR);
                        $stmt_insert->bindParam(':address', $address, PDO::PARAM_STR);
                        $stmt_insert->bindParam(':dob', $dob, PDO::PARAM_STR);
                        $stmt_insert->bindParam(':exp_date', $exp_date, PDO::PARAM_STR);
                        $stmt_insert->bindParam(':image', $uploadfile, PDO::PARAM_STR);
                        $stmt_insert->bindParam(':signature', $signature, PDO::PARAM_STR);
                        $stmt_insert->bindParam(':member_id', $member_id, PDO::PARAM_INT);
                        $stmt_insert->execute();

                    $insert = true;
                }

            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            } finally {
                $conn = null;
            }
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
  <link rel="stylesheet" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <title>ID Card Generator | <?php echo date("Y") ?></title>
</head>

<body>
  <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Edit This Card</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <form method="POST" novalidate>
          <div class="modal-body">
            <input type="hidden" name="snoEdit" id="snoEdit">
            <div class="form-group">
              <label for="name">Student Name</label>
              <input type="text" class="form-control" id="nameEdit" name="nameEdit" required>
            </div>

            <div class="form-group">
              <label for="desc">ID Card Number:</label>
              <input class="form-control" id="id_noEdit" name="id_noEdit" rows="3"></input>
            </div> 
          </div>
          <div class="modal-footer d-block mr-auto">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <nav class="navbar navbar-expand-lg navbar-dark" style="background: black;">
    <a class="navbar-brand" href="#"><img src="assets/images/logo.png" width="50px"></a>
    <li class="nav-item">
                <span class="navbar-text">Welcome to DHVSU ID-GENERATOR!</span>
            </li>
    <div class="navbar-nav ml-auto">
        <ul class="navbar-nav">
            <li class="nav-item active">
                <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout</a>
            </li>
        </ul>
    </div>
</nav>

  <?php
  if($insert){
    echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
    <strong>Success!</strong> Your Card has been inserted successfully
    <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
      <span aria-hidden='true'>×</span>
    </button>
  </div>";
  }
  ?>

  <?php
  if($delete){
    echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
    <strong>Success!</strong> Your Card has been deleted successfully
    <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
      <span aria-hidden='true'>×</span>
    </button>
  </div>";
  }
  ?>

  <?php
  if($update){
    echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
    <strong>Success!</strong> Your Card has been updated successfully
    <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
      <span aria-hidden='true'>×</span>
    </button>
  </div>";
  }
  ?>

   <?php
  if($empty){
    echo "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
    <strong>Error!</strong> The Fields Cannot Be Empty! Please Give Some Values.
    <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
      <span aria-hidden='true'>×</span>
    </button>
  </div>";
  }
  ?>

     <?php
  if($already_card){
    echo "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
    <strong>Error!</strong> The ID No. you entered is already in use
    <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
      <span aria-hidden='true'>×</span>
    </button>
  </div>";
  }
  ?>
  <div style="padding-top: 10px;">
            </div>
  <div class="container my-4">
  <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
  <i class="fa fa-plus"></i> Add New Card</button>
  <a href="id-card.php" class="btn btn-primary">
  <i class="fa fa-address-card"></i> Generate ID Card</a></p>
<div class="collapse" id="collapseExample">
  <div class="card card-body">
    <form id="myForm" method="post" enctype="multipart/form-data">
    <div class="form-row">
      <div class="form-group col-md-6">
        <label for="inputCity" class="form-label">Student Name</label>
        <input type="text" name="name" class="form-control" id="inputCity" required>
      </div>
      <div class="form-group col-md-4">
        <label for="inputState">Class / Grade</label>
        <select name="grade" class="form-control">
          <option selected>Choose...</option>
          <option value="1st">1st</option>
          <option value="2nd">2nd</option>
          <option value="3rd">3rd</option>
          <option value="4th">4th</option>
        </select>
      </div>
      <div class="form-group col-md-2">
        <label for="inputZip">Date Of Birth</label>
        <input type="date" name="dob" class="form-control">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group col-md-6">
        <label for="inputCity">Address</label>
        <input type="text" name="address" class="form-control">
      </div>
      <div class="form-group col-md-4">
        <label for="inputState">Email Address</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="form-group col-md-2">
        <label for="inputZip">Expire Date</label>
        <input type="date" name="exp_date" class="form-control">
      </div>
    </div>
      <div class="form-row">
      <div class="form-group col-md-3">
        <label for="id_no">ID Card No.</label>
          <input class="form-control" id="id_no" name="id_no" type="text" oninput="validateNumericInput(this)" />
        </div>
        <div class="form-group col-md-3">
          <label for="phone">Phone No.</label>
          <input class="form-control" id="phone" name="phone" type="text" oninput="validateNumericInput(this)" />
          </div>
        <div class="form-group col-md-3">
          <label for="id_no">Signature</label>
          <input class="form-control" id="signature" name="signature" type="text" oninput="validateNonNumericInput(this)" ></input>
        </div>
        <div class="form-group col-md-4">
            <label for="photo">Photo</label>
            <input type="file" name="image" id="imageInput" accept="image/*" onchange="previewImage()" />
            <img id="imagePreview" style="max-width: 100%; max-height: 200px; margin-top: 10px;" />
            <p id="imageError" style="color: red; display: none;">Please choose an image.</p>
        </div>
    </div>
    <p id="generalError" style="color: red; display: none;">All fields are required to be filled.</p>
    <button type="button" class="btn btn-primary" onclick="validateForm()"><i class="fa fa-plus"></i> Add Card</button>
</form>
  </div>
</div>
  <div class="container my-4">
    <table class="table" id="myTable">
      <thead>
        <tr>
          <th scope="col">S.No</th>
          <th scope="col">Name</th>
          <th scope="col">ID Card No.</th>
          <th scope="col">Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php
      include 'include/connect/dbcon.php';

      try {
          $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
          $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

          // Check if a user is logged in
          if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true && isset($_SESSION['user']['id'])) {
              $user_id = $_SESSION['user']['id'];

              // for displaying records based on member_id
              $sql = "SELECT * FROM `cards` WHERE member_id = :user_id ORDER BY 1 DESC";
              $stmt = $conn->prepare($sql);
              $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
              $stmt->execute();

              $sno = 0;

              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  $sno = $sno + 1;
                  echo "<tr>
                          <th scope='row'>" . $sno . "</th>
                          <td>" . $row['name'] . "</td>
                          <td>" . $row['id_no'] . "</td>
                          <td> 
                            <button class='edit btn btn-sm btn-primary' id='edit_" . $row['sno'] . "' data-sno='" . $row['sno'] . "'>Edit</button>
                            <button class='delete btn btn-sm btn-primary' id='delete_" . $row['sno'] . "' data-sno='" . $row['sno'] . "'>Delete</button>
                          </td>
                      </tr>";
              }
    } else {
        echo "User not authenticated";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
} finally {
    $conn = null;
}
?>
      </tbody>
    </table>
  </div>
  <hr>
  
  <div style="padding-top: 10px;">
            </div>
  
  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  <script src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
  <script src="Script/validateForm.js"></script>
  <script src="Script/previewImage.js"></script>
  <script src="Script/validateNumericInput.js"></script>
  <script src="Script/validateNonNumericInput.js"></script>

  <script>
    $(document).ready(function () {
      $('#myTable').DataTable();

    });
  </script>

  <script>
    edits = document.getElementsByClassName('edit');
        Array.from(edits).forEach((element) => {
        element.addEventListener("click", (e) => {
        console.log("edit ");
        tr = e.target.parentNode.parentNode;
        name = tr.getElementsByTagName("td")[0].innerText;
        id_no = tr.getElementsByTagName("td")[1].innerText;
        sno = e.target.getAttribute('data-sno'); // Corrected line
        console.log(name, id_no, sno);
        nameEdit.value = name;
        id_noEdit.value = id_no;
        snoEdit.value = sno;
        console.log(sno);
        $('#editModal').modal('toggle');
    })
  });


    deletes = document.getElementsByClassName('delete');
      Array.from(deletes).forEach((element) => {
      element.addEventListener("click", (e) => {
      console.log("delete ");
      sno = e.target.getAttribute('data-sno');

    if (confirm("Are you sure you want to delete this?")) {
      console.log("yes");
      window.location.href = `index.php?delete=${sno}`;
    } else {
      console.log("no");
    }
  });
});
</script>
</body>
</html>