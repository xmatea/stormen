<?php

# starter en session
session_start();
require_once('../config.php');

# setter variabler og feilvariabler
$passord = $passord_err = "";
$brukernavn = $brukernavn_err = "";

# prøver å logge inn ETTER at skjemaet er fylt ut
if ($_SERVER["REQUEST_METHOD"] == 'POST') {
  if (empty(trim($_POST['brukernavn']))) {
    $brukernavn_err = "Vennligst skriv inn brukernavn.";
  } else {
    $brukernavn = trim($_POST['brukernavn']);

  }

  if (empty($_POST['passord'])) {
    $passord_err = "Vennligst skriv inn passord.";
  } else {
    $passord = trim($_POST['passord']) ;
  }

  if (empty($password_err) && empty($brukernavn_err)) {
    $sql = "SELECT brukernavn, passord from admin WHERE brukernavn = '".$brukernavn."'";
    echo($sql);
    $res = mysqli_query($conn, $sql);
    $r = $res->fetch_assoc();

    if (!$r) {
      $brukernavn_err = "Denne brukeren er ikke registrert.";
    } else {
      if(password_verify($passord, $r['passord'])) {
        # HER LOGGES BRUKEREN UT, HVIS ADMINISTRATOREN ER LOGGET INN MED PERSONLIG BRUKER.
        $_SESSION['innlogget'] = false;
        $_SESSION['admin'] = true;
        $_SESSION['brukernavn'] = $r['brukernavn'];

        header('location: admin.php');
      } else {
        $passord_err = "Feil passord";
      }
    }
  } else {
    $login_err = "An error occured.";
  }
}
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
        # NAVIGASJONSMENY SOM VARIERER MED TILGANGSNIVÅ
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
      if(!empty($login_err)) {
        echo($login_err);
      }
    ?>

    <div id="skjemainnpakning">
      <div id="innloggingsskjema">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" autocomplete="off">
          <h1 class="skjema_overskrift">Logg inn med ansattkonto</h1>
          <input type="text" name="brukernavn" placeholder="Brukernavn">
          <?php echo("<span class='skjema_feilmelding'>".$brukernavn_err."</span>")?>
          <input type="password" name="passord" placeholder="Passord">
          <?php echo("<span class='skjema_feilmelding'>".$passord_err."</span>")?></p>
          <input type="submit" class="form_button">
        </form>
      </div>
    </div>
    </body>
  </html>
