<?php
# starter en session
session_start();

# sjekker om bruker er allerede logget inn
if (isset($_SESSION['innlogget']) && $_SESSION['innlogget'] == true) {
  # sender brukeren til personlig/hjem.php og avslutt
  header("location: personlig/hjem.php");
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

        header('location: personlig/hjem.php');
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
  <meta charset="utf-8">
  <title>Logg inn</title>
  <link href="../stilark/style.css" type="text/css" rel="stylesheet">
  <link href="../stilark/login.css" type="text/css" rel="stylesheet">
</head>
<body>
  <div class="innhold">
    <h1 class="logo" href="index.php"><a href=index.php>Stormen bibliotek</a></h1>

    <div id="nav_meny">
      <div class=meny_div>
        <li class="meny_element"><a href ="../bøker.php">Finn bøker</a></li>
      </div>
      <div class="meny_div">
        <li class="meny_element"><a href ="utlån.php">Utlån</a></li>
      </div>
      <div class="meny_div">
        <li class="meny_element"><a href ="innlevering.php">Innlevering</a></li>
      </div>
      <div class="meny_div">
        <li class="meny_element"><a href ="hjem.php">Mine bøker</a></li>
      </div>
      <div class="meny_div">
        <li class="meny_element"><a href ="../admin/ansatt_login.php">For ansatte</a></li>
      </div>
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
