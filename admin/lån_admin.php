<?php
# starter en session
session_start();

# sjekker om bruker er allerede logget inn
if (!isset($_SESSION['admin']) || $_SESSION['admin'] == false) {
  # sender brukeren til admin_login.php og avslutt
  header("location: admin_login.php");
  exit;
}
# HENTER KOBLING OG SPØRRINGER EKSTERNT
require_once "../config.php";
require_once "../spørringer.php";
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

<div id="søkeskjema_wrap">
  <form autocomplete="off" method="POST" id="søkeskjema" style="display: inline;">
    <input autocomplete="off" name="hidden" type="text" style='display:none !important;'>
    <input type="text" name="tittel" value="" id="søkefelt" placeholder="Søk etter tittel">
    <input list="kategoriliste" name="kategori" placeholder="Søk etter kategori">
    <!-- genererer liste for kategorisøk -->
    <datalist id="kategoriliste">
      <?php
      require_once "../config.php";
      $sql = "SELECT tittel from Dewey";
      $res = mysqli_query($conn, $sql);
      while($row = $res->fetch_assoc()) {
        echo "<option value='".$row['tittel']."'>";
      }
        ?>
    </datalist>
    <input type="text" name="ISBN" value="" id="søkefelt" placeholder="Søk etter ISBN">
    <input type="submit" class="søkeknapp">
  </form>
</div>

  <?php
  $sql = $utlånerliste;
  $filter = array_filter($_POST);
  if (!empty($filter)) {
    $spørring = [];

    # Filtrerer søkeparametere i en array og setter dem sammen til sql-kode
    foreach($filter as $field => $value) {
      if ($field == 'tittel') {
        array_push($spørring, "bok.tittel LIKE '%".$value."%'");
      } elseif ($field == 'ISBN') {
        array_push($spørring, "bok.ISBN LIKE '%".$value."%'");
      } elseif ($field == 'kategori') {
        array_push($spørring, "dewey.tittel LIKE '%".$value."%'");
      }
    }
    $sql = $sql." WHERE ".join($spørring, " and ");
  }

  $sql = $sql."  GROUP BY bok.id ORDER BY bok.id LIMIT 400";
  $res = $conn->query($sql);

if ($res) {
  echo "<div id='bokvisning_liten'>";
  echo "<table>";
  echo "<th>ID</th>";
  echo "<th>Tittel</th>";
  echo "<th>Personnummer</th>";
  echo "<th>Navn</th>";
  echo "<th>Utlånsdato</th>";
  echo "<th>Forny</th>";
  echo "<th>Fjern</th>";
  # viser søkeresultater
  while($row = $res->fetch_assoc()) {
    echo "<tr>";
    echo '<td>'.$row['id'].'</td>';
    echo '<td>'.$row['tittel'].'</td>';
    echo '<td>'.$row['personnummer'].'</td>';
    echo '<td>'.$row['fornavn'].' '.$row['etternavn'].'</td>';
    echo '<td>'.$row['utlånsdato'].'</td>';
    # sender ID til forny_lån.php med GET
    echo '<td><a href="forny_lån.php?id='.$row['id'].'&personnummer='.$row['personnummer'].'">Forny</a></td>';
    # sender ID til slett_lån.php med GET
    echo '<td><a href="slett_lån.php?id='.$row['id'].'&personnummer='.$row['personnummer'].'">Slett</a></td>';
    echo "</tr>";
  }
  echo "</table></div>";
} else {
  echo "<p style='text-align: center'> Fant ingen resultater. Prøv et mer generelt søkeord.</p>";
}
  ?>

</body>
</html>
