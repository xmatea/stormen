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
