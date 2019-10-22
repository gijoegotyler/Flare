<?php
require_once "config.php";
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$username = "";

if(isset($_GET['User'])){
    $username = $_GET['User'];
}else {
    $username = $_SESSION['username'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="navbar.css">
    <!-- <link rel="stylesheet" href="welcome.css"> -->
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; }
        #profilepic { margin-top: 1%; }
    </style>
</head>
<body>
  <?php include 'navbar.php';?>
  <center>
    <?php
      $sql = "SELECT profilepic, username FROM users";
      if ($result = mysqli_query($link, $sql)) {
        if(mysqli_num_rows($result) > 0){
          while($row = mysqli_fetch_array($result)){
            if (strcmp($row['username'], $username) == 0) {
              echo "<img src='profilepics/".$row['profilepic']."' height=300vh class='rounded' id='profilepic'>";
            }
          }
          mysqli_free_result($result);
        }
      }
    ?>
    <h1>
      <?php
        echo $username;
      ?>
    </h1>
  </center>
  <?php
    $mysql = "SELECT id, username FROM posts";
    if ($r = mysqli_query($link, $mysql)) {
      if(mysqli_num_rows($r) > 0){
        while($row = mysqli_fetch_array($r)){
          if (strcmp($row['username'], $_SESSION['username']) == 0) {
            include "post.php";
          }
        }
      }
      mysqli_free_result($r);
  }
  ?>
</body>
</html>
