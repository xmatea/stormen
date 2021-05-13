<?php
# starter en session
session_start();

# sjekker om bruker er allerede logget inn
if (isset($_SESSION['innlogget']) && $_SESSION['innlogget'] == true) {
  # sender brukeren til hjem.php og avslutt
  header("hjem.php");
  exit;
}

require_once('config.php');

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
    $sql = "SELECT idbruker, brukernavn, passord from bruker WHERE brukernavn = '".$brukernavn."'";

    $res = mysqli_query($conn, $sql);
    if ($res == false) {
      $brukernavn_err = "Denne brukeren eksisterer ikke.";
      exit;
    }

    $r = $res->fetch_assoc();
    if(password_verify($passord, $r['passord'])) {

      $_SESSION['innlogget'] = true;
      $_SESSION['id'] = $r['id'];
      $_SESSION['brukernavn'] = $r['brukernavn'];
      header('location: hjem.php');
    } else {
      $passord_err = "Feil passord";
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
  <link href="style.css" type="text/css" rel="stylesheet">

</head>
<body>
  <div class="innpakning">
    <h1 id="logo" href="idex.php">Stormen Bibliotek</h1>
    <div id="nav_meny">
      <li class="meny_element"><a href ="bøker.php">Finn bøker</a></li>
      <li class="meny_element"><a href ="utlån.php">Utlån</a></li>
      <li class="meny_element"><a href ="innlevering.php">Innlevering</a></li>
      <li class="meny_element"><a href ="login.php">Logg inn</a></li>
    </div>

    <h1>Logg inn</h1>
    <?php
      if(!empty($login_err)) {
        echo($login_err);
      }
    ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
      <label>Brukernavn</label><input type="text" name="brukernavn">
      <label>Passord</label><input type="password" name="passord">
      <input type="submit">
      <p>Ingen bruker? Registrer <a href="register.php">her</a></p>
    </body>
  </html>
