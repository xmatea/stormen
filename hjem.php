<?php
  session_start();
  if ($_SESSION['innlogget'] != true) {
    header('location: login.php');
  }
?>
<!DOCTYPE html>
<html>
<head>
  <link href="style.css" type="text/css" rel="stylesheet">
  <link href="login.css" type="text/css" rel="stylesheet">
</head>
<body>
  <h1 class="logo" href="idex.php">Stormen Bibliotek</h1>
  <a href="logout.php">Logg ut</a>

  <div id="nav_meny">
    <div class=meny_div>
      <li class="meny_element"><a href ="bøker.php">Finn bøker</a></li>
    </div>
    <div class="meny_div">
      <li class="meny_element"><a href ="utlån.php">Utlån</a></li>
    </div>
    <div class="meny_div">
      <li class="meny_element"><a href ="innlevering.php">Innlevering</a></li>
    </div>
    <div class="meny_div">
      <li class="meny_element"><a href ="ansatt_login.php">For ansatte</a></li>
    </div>
  </div>

  <h1>Mine bøker</h1>

</body>
</html>
