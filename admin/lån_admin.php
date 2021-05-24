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
  <link href="../stilark/style.css" type="text/css" rel="stylesheet">
  <link href="../stilark/login.css" type="text/css" rel="stylesheet">
</head>
<body>
  <h1 class="logo" href="idex.php">Stormen Bibliotek</h1>
  <a href="logout.php">Logg ut</a>

  <div id="nav_meny">
    <div class="meny_div">
      <li class="meny_element"><a href ="bøker_admin.php">Administrer bøker</a></li>
    </div>
    <div class="meny_div">
      <li class="meny_element"><a href ="lån_admin.php">Administrer lån</a></li>
    </div>
    <div class="meny_div">
      <li class="meny_element"><a href ="kalender_admin.php">Kalender</a></li>
    </div>
  </div>

  <h3>Filtrér</h3>
  <form autocomplete="off" method="POST" id="søkeskjema">
    <input autocomplete="off" name="hidden" type="text" style="display:none;">
    <label>Tittel: </label><input type="text" name="tittel" value="" id="søkefelt">
    <label>Kategori: </label><input type="text" name="kategori" value="" id="søkefelt">
    <label>ISBN:  </label><input type="text" name="ISBN" value="" id="søkefelt">
    <input type="submit" value="GO" id="søkeknapp">
  </form>

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
  echo "<div id='boktabell'>";
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
