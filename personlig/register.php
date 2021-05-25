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
