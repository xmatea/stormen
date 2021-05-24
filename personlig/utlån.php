<?php
  session_start();
  if ($_SESSION['innlogget'] != true) {
    header('location: login.php');
  }

  require_once "../config.php";
    $sql = "SELECT
    bok.id,
    bok.ISBN,
    bok.tittel,
    bok.kategori,
    bok.forlag,
    bok.status,
    dewey.idDewey,
    dewey.tittel as kategorinavn
    FROM bok
    JOIN dewey ON bok.kategori=dewey.idDewey";

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
      $sql = $sql." WHERE ".join($spørring, " and ")." and status='Tilgjengelig'";

    }
?>

<!DOCTYPE html>
<html>
<head>
  <link href="../stilark/style.css" type="text/css" rel="stylesheet">
  <link href="../stilark/skjema.css" type="text/css" rel="stylesheet">
  <link href="../stilark/tabell.css" type="text/css" rel="stylesheet">
  <link href="../stilark/utlån.css" type="text/css" rel="stylesheet">
</head>
<body>
  <h1 class="logo" href="idex.php">Stormen Bibliotek</h1>
  <a href="../logout.php">Logg ut</a>

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

  <h1 class="sideoverskrift"><a href="personlig/personlig/utlån.php">Lån bok</a></h1>
  <h2>Søk etter tittel eller ISBN</h2>

  <div id="boksøk_wrap">

  <form autocomplete="off" method="POST" id="boksøk">
    <input autocomplete="off" name="hidden" type="text" style="display:none;">
    <label>Tittel:</label>
    <input type="text" name="tittel" placeholder="Tittel" id="søkefelt">

    <label>Kategori:</label>
    <input type="text" name="kategori" placeholder="Kategori" id="søkefelt">

    <label>ISBN:</label>
    <input type="text" name="ISBN" placeholder="ISBN" id="søkefelt">
    <input type="submit">
  </form>
</div>

<?php
$res = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $res = $conn->query($sql);
  echo "<div id='boktabell_utlån'>";
  echo "<form method='GET' id='utlånsvalg'>";
  echo "<table>";
  echo "<th>ISBN</th> <th>Tittel</th> <th>Forlag</th> <th>Kategori</th> <th>Status</th> <th>Velg</th>";

  while($row = $res->fetch_assoc()) {
    echo '<tr> <td>'.$row['ISBN'].'</td> <td>'.$row['tittel'].'</td>';
    echo '<td>'.$row['forlag'].'</td> <td>'.$row['kategorinavn'].'</td> <td>'.$row['status'].'</td>';
    echo '<td><input type="radio" name="bokid" value='.$row['id'].'></td>';
    echo "</tr>";
  }
  echo "<input type='submit'>";
  echo "</table></form></div>";
}

if (isset($_GET['bokid'])) {
  # Utfører en ny spørring, silk at man kan låne bøker direkte med link: personlig/utlån.php?bokid=1775
  $sql = "
  INSERT INTO utlån VALUES (".$_GET['bokid'].", CURRENT_DATE + INTERVAL 1 MONTH, '".$_SESSION['personnummer']."');
  UPDATE bok SET status='Utlånt' WHERE id=".$_GET['bokid'].";";

  $res = mysqli_multi_query($conn, $sql);
  if ($res) {
    echo "Suksess!";
  } else {
    echo "Noe gikk galt.";
  }



}

?>
</body>
</html>
