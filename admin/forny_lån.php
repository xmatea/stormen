<?php
session_start();

# sjekker om bruker er allerede logget inn
if (!isset($_SESSION['admin']) || $_SESSION['admin'] == false) {
  # sender brukeren til admin_login.php og avslutt
  header("location: admin_login.php");
  exit;
}

# henter kobling og spørringer eksternt
require_once("../config.php");
require_once("../spørringer.php");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Stormen bibliotek</title>
  <link href="../stilark/style.css" type="text/css" rel="stylesheet">
  <link href="../stilark/tabell.css" type="text/css" rel="stylesheet">
  <link href="../stilark/skjema.css" type="text/css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;700&display=swap" rel="stylesheet">
<body>
  <div id="topp_meny">
     <a href="../index.php"><img id="bildelogo" src="../grafisk/stormen.png"></a>
        <?php
        # navigasjonsmeny som varierer med tilgangsnivå
        if (isset($_SESSION['admin']) && $_SESSION['admin'] == true) {
          echo '
          <div id="navigasjon">
            <li><a href ="bøker_admin.php">Administrer bøker</li>
            <li><a href ="lån_admin.php">Administrer lån</li>
          </div>
          <div id="innlogging">
            <li><a href="../logout.php">Logg ut</a></li>
          </div>';
        } elseif (isset($_SESSION['innlogget']) && $_SESSION['innlogget'] == true) {
          echo'
          <div id="navigasjon">
            <li><a href ="../bøker.php">Finn bok</a></li>
            <li><a href ="../personlig/utlån.php">Utlån</li>
            <li><a href ="../personlig/innlevering.php">Innlevering</li>
            <li><a href ="../personlig/hjem.php">Min side</li>
          </div>
          <div id="innlogging">
            <li><a href="../logout.php">Logg ut</a></li>
            <li><a href=".admin_login.php">For ansatte</a></li>
          </div>';
        } else {
          echo'<div id="navigasjon">
            <li><a href ="../bøker.php">Finn bok</a></li>
            <li><a href ="../personlig/utlån.php">Utlån</li>
            <li><a href ="../personlig/innlevering.php">Innlevering</li>
          </div>
          <div id="innlogging">
            <li><a href="../personlig/login.php">Logg inn</a></li>
            <li><a href="admin_login.php">For ansatte</a></li>
          </div>';
        }
          ?>
      </div>
  <?php
  # sjekk at input er ok
  if (isset($_GET['id']) and isset($_GET['personnummer'])) {
      $sql = $utlånerliste." WHERE bokid=".$_GET['id']." and utlånerid='".$_GET['personnummer']."'";
      $res = mysqli_query($conn, $sql);
      $row = $res->fetch_assoc();
      echo "<h2 class='redigeringstekst'>Sett ny faktureringsdato for bok '".$row['tittel']."'?</h2>";
      echo "<form method='post'>";
      echo "<input type='date' name='dato' value='".$row['utlånsdato']."'>";
      echo "<input type='submit' name='bekreft' value='bekreft' class='redigeringsknapp'>";
      echo "</form>";
  }

 # sjekk at admin har bekreftet fornying
  if(isset($_POST['bekreft'])) {
    $sql = "UPDATE utlån SET utlånsdato='".$_POST['dato']."' WHERE bokid=".$_GET['id']." and utlånerid='".$_GET['personnummer']."'";
    $res = mysqli_query($conn, $sql);
    echo($sql);
  }
  ?>
</body>
</html>
