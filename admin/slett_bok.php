<?php
session_start();

# sjekker om bruker er allerede logget inn
if (!isset($_SESSION['admin']) || $_SESSION['admin'] == false) {
  # sender brukeren til admin_login.php og avslutt
  header("location: admin_login.php");
  exit;
}

#henter kobling eksternt.
require_once("../config.php");
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
        # navigasjonsmeny som varierer med tilgangsnivå.
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
  if (isset($_GET['id']) and isset($_GET['tittel'])) {
      echo "<h2 class='redigeringstekst'>Ønsker du å slette '".$_GET['tittel']."'?</h2>";
      echo "<form method='post'>";
      echo "<input type='submit' name='bekreft' value='bekreft' class='redigeringsknapp'>";
      echo "</form>";
  }

 # utfør sletting når bruker har bekreftet
  if(isset($_POST['bekreft'])) {
    $sql = "
    DELETE FROM forfatter_has_bok WHERE bok_id=".$_GET['id'].";
    DELETE FROM bok where id=".$_GET['id'].";
    DELETE FROM utlån where bokid=".$_GET['id'];
    $res = mysqli_multi_query($conn, $sql);
    echo($sql);
    if($res) {
      echo "Suksess!";
    } else {
      echo "Noe gikk galt.";
    }
  }
  ?>
</body>
</html>
