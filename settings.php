<?php
require_once "config.php";
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Password Reset Form Handling
// Define variables and initialize with empty values
$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = "";

// Processing form data when form is submitted
if(!empty($_POST['passchange'])){

    // Validate new password
    if(empty(trim($_POST["new_password"]))){
        $new_password_err = "Please enter the new password.";
    } elseif(strlen(trim($_POST["new_password"])) < 6){
        $new_password_err = "Password must have atleast 6 characters.";
    } else{
        $new_password = trim($_POST["new_password"]);
    }

    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm the password.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }

    // Check input errors before updating the database
    if(empty($new_password_err) && empty($confirm_password_err)){
        // Prepare an update statement
        $sql = "UPDATE users SET password = ? WHERE id = ?";

        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "si", $param_password, $param_id);

            // Set parameters
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            $param_id = $_SESSION["id"];

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Password updated successfully. Destroy the session, and redirect to login page
                session_destroy();
                header("location: login.php");
                exit();
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }

    // Close connection
    mysqli_close($link);
}

// Profile Pic Form Handling
$file_err = "";
if(!empty($_POST['profilepic-submit'])) {
  $sql = "SELECT profilepic, username FROM users";
  if ($result = mysqli_query($link, $sql)) {
    if(mysqli_num_rows($result) > 0){
      while($row = mysqli_fetch_array($result)){
        if (strcmp($row['username'], $_SESSION['username']) == 0) {
          if (strcmp("default.jpg", $row['profilepic']) !== 0) {
            unlink("profilepics/".$row['profilepic']);
          }
        }
      }
      mysqli_free_result($result);
    }
  }

  if (isset($_FILES["newprofpic"]["name"])) {
    $target_dir = "profilepics/";
    $target_file = $target_dir . basename($_FILES["newprofpic"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["newprofpic"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        $file_err = "File is not an image.";
        $uploadOk = 0;
    }

    if ($_FILES["newprofpic"]["size"] > 500000) {
      $file_err = "Sorry, your file is too large.";
      $uploadOk = 0;
    }

    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        $file_err = "Sorry, only JPG, JPEG, & PNG files are allowed.";
        $uploadOk = 0;
    }

    $file_name = md5($_SESSION['username']).".".$imageFileType;

    if ($uploadOk !== 0) {
      if (move_uploaded_file($_FILES["newprofpic"]["tmp_name"], $target_dir.$file_name)) {
        $sql = "UPDATE users SET profilepic=? WHERE username=?";
        if($stmt = mysqli_prepare($link, $sql)){
          mysqli_stmt_bind_param($stmt, "ss", $param_picname, $param_username);

          $param_picname = $file_name;
          $param_username = $_SESSION['username'];

          if(mysqli_stmt_execute($stmt)){
            header("Refresh:0");
          }else {
            echo "failed preparation";
          }

          mysqli_stmt_close($stmt);
        }else {
          echo "failed preparation";
        }
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="navbar.css">
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; }
        .form-group{ display: inline-block !important; }
        #section { width: 100%; padding: 3%; border-bottom: 1px solid black; }
        #title { display: inline-block; margin-right: 0.5%;}
    </style>
</head>
<body>
    <?php include 'navbar.php';?>
    <br>
    <div id="section">
      <form id="form_1" action="" method="post" enctype="multipart/form-data">
          <h3 id="title">Change Profile Picture:</h3>
          <div class="form-group <?php echo (!empty($new_password_err)) ? 'has-error' : ''; ?>">
              <input type="file" name="newprofpic" id="newprofpic" />
              <span class="help-block"><?php echo $file_err; ?></span>
          </div>
          <div class="form-group">
              <input type="submit" class="btn btn-primary" name="profilepic-submit" value="Submit">
          </div>
      </form>
    </div>
    <div id="section">
      <form id="form_2" action="" method="post">
          <h3 id="title">Change Your Password:</h3>
          <div class="form-group <?php echo (!empty($new_password_err)) ? 'has-error' : ''; ?>">
              <label>New Password</label>
              <input type="password" name="new_password" class="form-control" value="<?php echo $new_password; ?>">
              <span class="help-block"><?php echo $new_password_err; ?></span>
          </div>
          <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
              <label>Confirm Password</label>
              <input type="password" name="confirm_password" class="form-control">
              <span class="help-block"><?php echo $confirm_password_err; ?></span>
          </div>
          <div class="form-group">
              <input type="submit" class="btn btn-primary" name="passchange" value="Submit">
          </div>
      </form>
    </div>
</body>
</html>
