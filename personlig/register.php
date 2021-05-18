<?php
require_once "../config.php";
// lag variabler for passord, brukernavn og feil
$personnummer = $fornavn = $etternavn = $passord = $bekreft_passord = "";
$personnummer_err = $fornavn_err = $etternavn_err = $passord_err = $bekreft_passord_err = "";

// Kjører koden under bare når skjema er utfylt
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Sjekk personnummerfeltet
    if(empty(trim($_POST["personnummer"]))){
        $personnummer_err = "Vennligst skriv inn et personnummer.";
    } else {
        $personnummer = $_POST["personnummer"];
        // Sjekk om bruker allrerede eksisterer
        $sql = "SELECT personnummer FROM utlåner WHERE personnummer = '".$personnummer."'";
        echo($sql);
        $res = mysqli_query($conn, $sql);
        $r = $res->fetch_assoc();
        var_dump($r);
        if ($r) {
          $personnummer_err = "Denne brukeren er allerede registrert.";
        } else {
          $personnummer = trim($_POST["personnummer"]);

        }
    }

    // Sjekk navnefelt

    if (empty(trim($_POST["fornavn"])) || empty(trim($_POST["etternavn"]))) {
      $fornavn_err = "Vennlist skriv inn et fornavn.";
      $etternavn_err = "Vennligst skriv inn et etternavn.";

    } else {
      $fornavn = trim($_POST["fornavn"]);
      $etternavn = trim($_POST["etternavn"]);
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
    if(empty(trim($_POST["bekreft_password"]))) {
        $bekreft_passord_err = "Vennligst bekreft passord.";
    } else{
        $bekreft_passord = trim($_POST["bekreft_password"]);
        if(empty($passord_err) && ($passord != $bekreft_passord)){
            $bekreft_passord_err = "Passordene samsvarer ikke.";
        }
    }

    // Sjekk om noen av feilvarablene er satt
    if(empty($personnummer_err) && empty($fornavn_err) && empty($etternavn_err) && empty($passord_err) && empty($bekreft_passord_err)){

        $passord = password_hash($passord, PASSWORD_DEFAULT);
        $sql = "INSERT INTO utlåner (personnummer, fornavn, etternavn, passord) VALUES ('".$personnummer."', '".$fornavn."','".$etternavn."','".$passord."')";
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
    <title>Registrér</title>
    <link href="../stilark/style.css" type="text/css" rel="stylesheet">
    <link href="../stilark/login.css" type="text/css" rel="stylesheet">
</head>
<body>
  <div class="innhold">
    <h1 class="logo"><a href=index.php>Stormen bibliotek</a></h1>

    <div id="nav_meny">
      <div class=meny_div>
        <li class="meny_element"><a href ="../bøker.php">Søk i bøker</a></li>
      </div>
      <div class="meny_div">
        <li class="meny_element"><a href ="personlig/utlån.php">Utlån</a></li>
      </div>
      <div class="meny_div">
        <li class="meny_element"><a href ="personlig/innlevering.php">Innlevering</a></li>
      </div>
      <div class="meny_div">
        <li class="meny_element"><a href ="personlig/hjem.php">Mine bøker</a></li>
      </div>
      <div class="meny_div">
        <li class="meny_element"><a href ="ansatt_login.php">For ansatte</a></li>
      </div>
    </div>


        <div class="skjema">
        <h1 class="skjema_overskrift">Registrér ny bruker</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <label>Personnummer</label>
                <input type="text" name="personnummer">
                <label>Fornavn</label>
                <input type="text" name="fornavn">
                <label>Etternavn</label>
                <input type="text" name="etternavn">
                <label>Passord</label>
                <input type="password"   name="passord">
                <label>Bekreft passord</label>
                <input type="password" name="bekreft_password">
                <input type="submit" value="Registrér">
        </form>
        <p>Har du allerede en bruker? Logg inn <a href="login.php">her</a></p>
    </div>
  </div>
</body>
</html>
