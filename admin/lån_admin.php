<?php
# starter en session
session_start();

# sjekker om bruker er allerede logget inn
if (!isset($_SESSION['admin']) || $_SESSION['admin'] == false) {
  # sender brukeren til admin_login.php og avslutt
  header("location: admin_login.php");
  exit;
}
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
    <input type="text" name="kategori" value="" id="søkefelt" placeholder="Søk etter kategori">
    <input type="text" name="ISBN" value="" id="søkefelt" placeholder="Søk etter ISBN">
    <input type="submit" name="filtrering" value="Søk" class="søkeknapp">
  </form>
</div>

  <?php
  $sql = $utlånerliste;
  $filter = array_filter($_POST);
  if ($filter) {
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

  $res = $conn->query($sql);
  echo "<div id='bokvisning_liten'>";
  echo "<table>";
  echo "<th>ID</th>";
  echo "<th>Tittel</th>";
  echo "<th>Personnummer</th>";
  echo "<th>Navn</th>";
  echo "<th>Utlånsdato</th>";
  echo "<th>Forny</th>";
  echo "<th>Fjern</th>";

  while($row = $res->fetch_assoc()) {
    echo "<tr>";
    echo '<td>'.$row['id'].'</td>';
    echo '<td>'.$row['tittel'].'</td>';
    echo '<td>'.$row['personnummer'].'</td>';
    echo '<td>'.$row['utlåner'].'</td>';
    echo '<td>'.$row['utlånsdato'].'</td>';
    echo '<td><a href="forny_lån.php?id='.$row['id'].'&personnummer='.$row['personnummer'].'">Forny</a></td>';
    echo '<td><a href="slett_lån.php?id='.$row['id'].'&personnummer='.$row['personnummer'].'">Slett</a></td>';
    echo "</tr>";
  }
  echo "</table></div>";
  ?>

</body>
</html>
