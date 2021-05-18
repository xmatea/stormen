<?php
# starter en session
session_start();

# sjekker om bruker er allerede logget inn
if (!isset($_SESSION['admin']) || $_SESSION['admin'] == false) {
  # sender brukeren til admin_login.php og avslutt
  header("location: admin_login.php");
  exit;
}

?>

<!DOCTYPE html>
<html>
<head>
  <link href="stilark/style.css" type="text/css" rel="stylesheet">
  <link href="stilark/login.css" type="text/css" rel="stylesheet">
</head>
<body>
  <h1 class="logo" href="idex.php">Stormen Bibliotek</h1>
  <a href="logout.php">Logg ut</a>

  <div id="nav_meny">
    <div class="meny_div">
      <li class="meny_element"><a href ="bøker_admin.php">Administrer bøker</a></li>
    </div>
    <div class="meny_div">
      <li class="meny_element"><a href ="lån_admin.php">Administrer lån</a></li>
    </div>
    <div class="meny_div">
      <li class="meny_element"><a href ="kalender_admin.php">Kalender</a></li>
    </div>
  </div>

</body>
</html>
