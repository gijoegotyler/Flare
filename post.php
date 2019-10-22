<?php
  $postid = $row["id"];
  $username = "";
  $story = "";
  $image = "";

  if ($r = mysqli_query($link,"SELECT username, story, image FROM posts WHERE id=".$postid)) {
    $data=mysqli_fetch_array($r,MYSQLI_ASSOC);
    $username = $data['username'];
    $story = $data['story'];
    $image = $data['image'];

    mysqli_free_result($r);
  }


?>

<div class="post-div">
  <div class="post-text">
    <div class="userwithpic">
      <?php
        $sql = "SELECT profilepic, username FROM users";
        if ($result = mysqli_query($link, $sql)) {
          if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_array($result)){
              if (strcmp($row['username'], $username) == 0) {
                echo "<img src='profilepics/".$row['profilepic']."' width=25px class='rounded' id='inline'>";
                echo "<a href='profile.php?User=".$username."'>".$username."</a>";
              }
            }
            mysqli_free_result($result);
          }
        }
      ?>
    </div>
    <p>
      <?php
        echo $story;
      ?>
    </p>
  </div>
  <div class="post-image-div">
    <?php
      echo "<img src='postpics/".$image."' class='post-image'>";
    ?>
  </div>
</div>
