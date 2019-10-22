<nav class="navbar navbar-expand-sm bg-dark navbar-dark">
  <ul class="navbar-nav">
    <li>
      <?php
        echo "<a class='navbar-brand' href='profile.php?User=".$_SESSION['username']."'>"
      ?>
        <?php
          $sql = "SELECT profilepic, username FROM users";
          if ($result = mysqli_query($link, $sql)) {
            if(mysqli_num_rows($result) > 0){
              while($row = mysqli_fetch_array($result)){
                if (strcmp($row['username'], $_SESSION['username']) == 0) {
                  echo "<img src='profilepics/".$row['profilepic']."' width=25px class='rounded' id='inline'>";
                  echo $_SESSION['username'];
                }
              }
              mysqli_free_result($result);
            }
          }
        ?>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="welcome.php">Home</a>
    </li>
  </ul>
  <ul class="nav navbar-nav ml-auto">
    <li class="nav-item" id="right">
      <a class="nav-link" href="createpost.php">New Post</a>
    </li>
    <li class="nav-item" id="right">
      <a class="nav-link" href="settings.php">Settings</a>
    </li>
    <li class="nav-item" id="right">
      <a class="nav-link" href="logout.php">Logout</a>
    </li>
  </ul>
</nav>
