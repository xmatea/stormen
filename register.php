<?php
require_once "config.php";
// lag variabler for passord, brukernavn og feil
$brukernavn= $passord = $bekreft_passord = "";
$brukernavn_err = $passord_err = $bekreft_passord_err = "";

// Kjører koden under bare når skjema er utfylt
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Sjekk brukernavnsfelt
    if(empty(trim($_POST["brukernavn"]))){
        $brukernavn_err = "Vennligst skriv inn et brukernavn.";
    } else {
        $brukernavn = $_POST["brukernavn"];
        // Sjekk om bruker allrerede eksisterer
        $sql = "SELECT idbruker FROM bruker WHERE brukernavn = '".$brukernavn."'";
        $res = mysqli_query($conn, $sql);
        $r = $res->fetch_assoc();
        var_dump($r);
        if ($r) {
          $brukernavn_err = "Denne brukeren eksisterer allerede.";
        } else {
          $brukernavn = trim($_POST["brukernavn"]);

        }
    }

    // Sjekk password og passordlengde
    if(empty(trim($_POST["passord"]))) {
        $passord_err = "Vennligst skriv inn et passord.";
    } elseif(strlen(trim($_POST["passord"])) < 6) {
        $passord_err = "Passord må bestå av minst 6 tegn.";
    } else {
        $passord = trim($_POST["passord"]);
    }

    // Sjekk bekreftelsespassword og -passordlengde
    if(empty(trim($_POST["bekreft_password"]))){
        $bekreft_passord_err = "Vennligst bekreft passord.";
    } else{
        $bekreft_passord = trim($_POST["bekreft_password"]);
        if(empty($passord_err) && ($passord != $bekreft_passord)){
            $bekreft_passord_err = "Passordene samsvarer ikke.";
        }
    }

    // Sjekk om noen av feilvarablene er satt
    if(empty($brukernavn_err) && empty($passord_err) && empty($bekreft_passord_err)){

        $passord = password_hash($passord, PASSWORD_DEFAULT);
        $sql = "INSERT INTO bruker (brukernavn, passord) VALUES ('".$brukernavn."', '".$passord."')";
        echo($sql);
        $res = mysqli_query($conn, $sql);
        if($res) {
          header("location: login.php");
        } else {
          echo "Noe gikk feil.";
        }
      }

    // Lukk tilkoblingen
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registrer</title>
    <link href="style.css" type="text/css" rel="stylesheet">
    <link href="login.css" type="text/css" rel="stylesheet">
</head>
<body>
  <h1 id="logo" href="idex.php">Stormen Bibliotek</h1>
  <a href="login.php">Logg inn</a>

  <div id="nav_meny">
    <li class="meny_element"><a href ="bøker.php">Finn bøker</a></li>
    <li class="meny_element"><a href ="utlån.php">Utlån</a></li>
    <li class="meny_element"><a href ="innlevering.php">Innlevering</a></li>
    <li class="meny_element"><a href ="login.php">Logg inn</a></li>
  </div>
        <div>login
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <label>Brukernavn</label>
                <input type="text" name="brukernavn">
                <label>Passord</label>
                <input type="password" name="passord">
                <label>Bekreft passord</label>
                <input type="password" name="bekreft_password">
                <input type="submit" value="kjør">
                <input type="reset" value="nullstill">
        </form>
        <p>Har du allerede en bruker? Logg inn <a href="login.php">her</a></p>
    </div>
</body>
</html>
