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
  <h1 id="logo" href="idex.php">Stormen Bibliotek</h1>
  <a href="logout.php">Logg ut</a>

  <div id="nav_meny">
    <li class="meny_element"><a href ="bøker.php">Finn bøker</a></li>
    <li class="meny_element"><a href ="utlån.php">Utlån</a></li>
    <li class="meny_element"><a href ="innlevering.php">Innlevering</a></li>
  </div>
  <h1>Mine bøker</h1>

</body>
</html>
