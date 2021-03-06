<?php
session_start();

# sjekker om bruker er allerede logget inn
if (!isset($_SESSION['admin']) || $_SESSION['admin'] == false) {
  # sender brukeren til admin_login.php og avslutt
  header("location: admin_login.php");
  exit;
}

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
        # navigasjonsmeny som vaierer med tilgangsnivå
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

  #setter variabler
  $ISBN = $tittel = $forlag = $status = $kategori = $fornavn_1 = $fornavn_2 = $etternavn_1 = $etternavn_2 = "";
  $forfatterid_1 = $forfatterid_2 = "";
  $err = "";
  $id = "";

  #sjekker at en id er spesifisert
  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = $bøker_forfatterliste." WHERE bok.id=".$_GET['id'];
    $res = $conn->query($sql);
    $bok = $res->fetch_assoc();

    # splitter forfatternavn fra spørringen (spørringen joiner alle forfatterfelt til ett, her splitter vi dem igjen)
    $forfattere = explode(', ', $bok['forfatternavn']);
    $fornavn_1 = preg_split('/\s+/', $forfattere[0])[0];
    $etternavn_1 = preg_split('/\s+/', $forfattere[0])[1];
    $forfatterid_1 = explode(', ', $bok['forfatterid'])[0];

    if (count($forfattere) > 1) {
      $fornavn_2 = preg_split('/\s+/', $forfattere[1])[0];
      $etternavn_2 = preg_split('/\s+/', $forfattere[1])[1];
      $forfatterid_2 = explode(', ', $bok['forfatterid'])[1];
    }

    $ISBN = $bok['ISBN'];
    $tittel = $bok['tittel'];
    $forlag = $bok['forlag'];
    $kategori = $bok['kategori'];
    $status = $bok['status'];

    #dette kjører når skjemaet er utfylt
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      if (strlen(trim($_POST['ISBN'])) != 17) {
            $err = "Ugyldig ISBN";
          } else {
            $ISBN = $_POST['ISBN'];
          }

          if (!empty($_POST['tittel'])) {
            $tittel = $_POST['tittel'];
          }

          if (!empty($_POST['forlag'])) {
            $forlag = $_POST['forlag'];
          }

          if (!empty($_POST['status'])) {
            $status = $_POST['status'];
          }

          if (!empty($_POST['kategori'])) {
            $kategori = $_POST['kategori'];
          }

          if (!empty($_POST['fornavn_1']) and !empty($_POST['etternavn_1'])) {
            $fornavn_1 = $_POST['fornavn_1'];
            $etternavn_1 = $_POST['etternavn_1'];
          }

          if (!empty($_POST['fornavn_2']) and !empty($_POST['etternavn_2'])) {
            $fornavn_2 = $_POST['fornavn_2'];
            $etternavn_2 = $_POST['etternavn_2'];
          }

          # i tilfellet med én forfatter
          if (!empty($fornavn_2)) {
            # OPPDATERER FORFATTERINFORMASJON
            # SLETTER FORFATTER_HAS_BOK-KOBLING FOR Å GJØRE TING ENKLERE
            $sql = "
            DELETE FROM forfatter_has_bok
            WHERE forfatter_idforfatter=".$forfatterid_1." and bok_id=".$bok['id'].";

            DELETE FROM forfatter_has_bok
            WHERE forfatter_idforfatter=".$forfatterid_2." and bok_id=".$bok['id'].";

            INSERT IGNORE INTO forfatter (fornavn, etternavn) values ('".$fornavn_1."', '".$etternavn_1."');
            SET @forfatter_id1 = LAST_INSERT_ID();

            INSERT IGNORE INTO forfatter (fornavn, etternavn) values ('".$fornavn_2."', '".$etternavn_2."');
            SET @forfatter_id2 = LAST_INSERT_ID();
            SET @bok_id = ".$bok['id'].";

            INSERT INTO forfatter_has_bok (bok_id, forfatter_idforfatter) SELECT @bok_id, idforfatter from forfatter where fornavn='".$fornavn_1."' and etternavn='".$etternavn_1."';
            INSERT INTO forfatter_has_bok (bok_id, forfatter_idforfatter) SELECT @bok_id, idforfatter from forfatter where fornavn='".$fornavn_2."' and etternavn='".$etternavn_2."';";

          # i tilfellet med to forfattere
          } else {
              # OPPDATERER FORFATTERINFORMASJON
            # SLETTER FORFATTER_HAS_BOK-KOBLING FOR Å GJØRE TING ENKLERE
            $sql =   "
            DELETE FROM forfatter_has_bok
            WHERE forfatter_idforfatter=".$forfatterid_1." and bok_id=1".$bok['id'].";

            INSERT IGNORE INTO forfatter (fornavn, etternavn) values ('".$fornavn_1."', '".$etternavn_1."');
            SET @forfatter_id1 = LAST_INSERT_ID();
            SET @bok_id = ".$bok['id'].";

            INSERT INTO forfatter_has_bok (bok_id, forfatter_idforfatter)
            SELECT @bok_id, idforfatter from forfatter where idforfatter=@forfatter_id1;";
    }
    # OPPDATERER BOKINFORMASJON
    $sql = $sql."
    UPDATE bok SET
    ISBN='".$ISBN."',
    tittel='".$tittel."',
    kategori='".$kategori."',
    forlag='".$forlag."',
    status='".$status."'
    WHERE id=".$bok['id'].";";

    #utfør spørring
    $res = mysqli_multi_query($conn, $sql);
  }

}

      echo "<h2 style='text-align: center; font-weight: 400;'>Redigerer '".$tittel."'</h2>";
      # PRINT REDIGERINGSSKJEMA OG FYLL DET MED ALLEREDE EKSISTERENDE VERDIER FOR BOKEN
      echo '
      <div id="administratorskjema">
      <form id="bokinnleggingsskjema" method="post">
      <div>
        <input type="text" name="ISBN" value="'.$ISBN.'">
        <input type="text" name="tittel" value="'.$tittel.'">
        </div>
        <div>
        <input type="text" name="forlag" value="'.$forlag.'">
        <input list="kategoriliste" name="kategori" placeholder="Kategori" placeholder="'.$kategori.'">
        <datalist id="kategoriliste">';
          # genererer kategoriliste
          require_once "../config.php";
          $sql = "SELECT tittel from Dewey";
          $res = mysqli_query($conn, $sql);
          while($row = $res->fetch_assoc()) {
            echo "<option value='".$row['tittel']."'>";
          }
          echo '
        </datalist>
        </div>
        <div>
        <input type="text" name="fornavn_1" value="'.$fornavn_1.'" placeholder="Fornavn 1">
        <input type="text" name="etternavn_1" value="'.$etternavn_1.'" placeholder="Etternavn 1">
        </div>
        <div>
        <input type="text" name="fornavn_2" value="'.$fornavn_2.'" placeholder="Fornavn 2">
        <input type="text" name="etternavn_2" value="'.$etternavn_2.'" placeholder="Etternavn 2">
        </div>
        <input list="statusliste" name="status" id="status" placeholder="'.$status.'">
        <datalist id="statusliste">
          <option value="Tilgjengelig">
          <option value="Bestilt">
          <option value="Utlånt">
        </datalist>
        <input type="submit">
      </form></div>';

      if ($res) {
        echo "<p style='text-align: center;'>Boken ble oppdatert suksessfult.";
      } else {
        echo "<p style='text-align: center;'>En feil oppsto!</p>";
      }
  ?>
</body>
</html>
