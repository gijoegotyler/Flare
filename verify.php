<?php

require_once "config.php";

if(isset($_GET['KEY'])){
  $sql = "UPDATE users SET verified=1 WHERE vkey=?";
  if($stmt = mysqli_prepare($link, $sql)){

      mysqli_stmt_bind_param($stmt, "s", $param_key);

      $param_key = $_GET['KEY'];

      if(mysqli_stmt_execute($stmt)){
        echo "Records were updated successfully.";
        header("location: login.php");
      }else {
        echo "execution went wrong";
      }
  } else {
      echo "preparation failed";
  }
  mysqli_close($link);
}else {
  echo "no key to verify";
}

?>
