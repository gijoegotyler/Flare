<?php
require_once "config.php";
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$file_err = "";
$text_err = "";
if(!empty($_POST['post-submit'])) {
  if (isset($_FILES["postpic"]["name"])) {
    $target_dir = "postpics/";
    $target_file = $target_dir . basename($_FILES["postpic"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["postpic"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        $file_err = "File is not an image.";
        $uploadOk = 0;
    }

    if ($_FILES["postpic"]["size"] > 1500000) {
      $file_err = "Sorry, your file is too large.";
      $uploadOk = 0;
    }

    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        $file_err = "Sorry, only JPG, JPEG, & PNG files are allowed.";
        $uploadOk = 0;
    }

    if(empty(trim($_POST["story"]))){
      $text_err = "please write something";
      $uploadOk = 0;
    }

    $file_name = md5($_SESSION['username'].time()).".".$imageFileType;

    if ($uploadOk !== 0) {
      if (move_uploaded_file($_FILES["postpic"]["tmp_name"], $target_dir.$file_name)) {

        $sql = "INSERT INTO posts (username, story, image) VALUES (?, ?, ?)";
        if($stmt = mysqli_prepare($link, $sql)){
          mysqli_stmt_bind_param($stmt, "sss", $param_username, $param_story, $param_picname);

          $param_picname = $file_name;
          $param_story = $_POST["story"];
          $param_username = $_SESSION['username'];

          if(mysqli_stmt_execute($stmt)){
            header("location: welcome.php");
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
    <title>New Post</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="navbar.css">
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; }
    </style>
</head>
<body>
    <?php include 'navbar.php';?>
    <br>
    <center>
      <h3>Create Post</h3>
      <form id="form" action="" method="post" enctype="multipart/form-data">
          <div class="form-group <?php echo (!empty($text_err)) ? 'has-error' : ''; ?>">
              <label>Your Story:</label>
              <textarea class="form-control" rows="5" id="story" name="story" width=80%;></textarea>
              <span class="help-block"><?php echo $text_err; ?></span>
          </div>
          <div class="form-group <?php echo (!empty($file_err)) ? 'has-error' : ''; ?>">
              <label>Image</label>
              <input type="file" name="postpic" id="postpic" />
              <span class="help-block"><?php echo $file_err; ?></span>
          </div>
          <div class="form-group">
              <input type="submit" class="btn btn-primary" name="post-submit" value="Submit">
          </div>
      </form>
    </center>
</body>
</html>
