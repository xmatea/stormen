<?php
# starter en session
session_start();

# sjekker om bruker er allerede logget inn
if (isset($_SESSION['innlogget']) && $_SESSION['innlogget'] == true) {
  # sender brukeren til personlig/hjem.php og avslutt
  header("location: hjem.php");
  exit;
}

require_once('../config.php');

# setter variabler og feilvariabler
$passord = $passord_err = "";
$personnummer = $personnummer_err = "";

# prøver å logge inn ETTER at skjemaet er fylt ut
if ($_SERVER["REQUEST_METHOD"] == 'POST') {
  if (empty(trim($_POST['personnummer']))) {
    $personnummer_err = "Vennligst skriv inn brukernavn.";
  } else {
    $personnummer = trim($_POST['personnummer']);

  }

  if (empty($_POST['passord'])) {
    $passord_err = "Vennligst skriv inn passord.";
  } else {
    $passord = trim($_POST['passord']) ;
  }

  if (empty($password_err) && empty($personnummer_err)) {
    $sql = "SELECT personnummer, fornavn, etternavn, passord from utlåner WHERE personnummer = ".$personnummer;
    $res = mysqli_query($conn, $sql);
    $r = $res->fetch_assoc();

    if (!$r) {
      $personnummer_err = "Denne brukeren er ikke registrert.";
    } else {
      if(password_verify($passord, $r['passord'])) {
        $_SESSION['innlogget'] = true;
        $_SESSION['admin'] = false;
        $_SESSION['personnummer'] = $r['personnummer'];
        $_SESSION['fornavn'] = $r['fornavn'];
        $_SESSION['etternavn'] = $r['etternavn'];

        header('location: hjem.php');
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
        if (isset($_SESSION['admin']) && $_SESSION['admin'] == true) {
          echo '
          <div id="navigasjon">
            <li><a href ="../admin/bøker_admin.php">Administrer bøker</li>
            <li><a href ="../admin/lån_admin.php">Administrer lån</li>
          </div>
          <div id="innlogging">
            <li><a href="../logout.php">Logg ut</a></li>
          </div>';
        } elseif (isset($_SESSION['innlogget']) && $_SESSION['innlogget'] == true) {
          echo'
          <div id="navigasjon">
            <li><a href ="../bøker.php">Finn bok</a></li>
            <li><a href ="utlån.php">Utlån</li>
            <li><a href ="innlevering.php">Innlevering</li>
            <li><a href ="hjem.php">Min side</li>
          </div>
          <div id="innlogging">
            <li><a href="../logout.php">Logg ut</a></li>
            <li><a href="../admin/admin_login.php">For ansatte</a></li>
          </div>';
        } else {
          echo'<div id="navigasjon">
            <li><a href ="../bøker.php">Finn bok</a></li>
            <li><a href ="utlån.php">Utlån</li>
            <li><a href ="innlevering.php">Innlevering</li>
          </div>
          <div id="innlogging">
            <li><a href="login.php">Logg inn</a></li>
            <li><a href="../admin/admin_login.php">For ansatte</a></li>
          </div>';
        }
          ?>
      </div>

    <?php
      if(!empty($login_err)) {
        echo($login_err);
      }
    ?>

    <div class = "skjema">

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" autocomplete="off">
      <input autocomplete="off" name="hidden" type="text" style="display:none;">
      <h1 class="skjema_overskrift">Logg inn</h1>
      <label>Personnummer</label>
      <input type="text" name="personnummer" placeholder="Personnummer">
      <?php echo("<span class='skjema_feilmelding'>".$personnummer_err."</span>")?>
      <label>Passord</label>
      <input type="password" name="passord" placeholder="Passord">
      <?php echo("<span class='skjema_feilmelding'>".$passord_err."</span>")?></p>
      <input type="submit">

      <p>Ingen bruker? Registrer <a href="register.php">her</a></p>
    </div>
    </div>
    </body>
  </html>
