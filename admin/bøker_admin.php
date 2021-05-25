<?php
# starter en session
session_start();

# sjekker om bruker er allerede logget inn
if (!isset($_SESSION['admin']) || $_SESSION['admin'] == false) {
  # sender brukeren til admin_login.php og avslutt
  header("location: admin_login.php");
  exit;
}

require_once('../config.php');

$ISBN = $tittel = $forlag = $fornavn_1 = $fornavn_2 = $etternavn_1 = $etternavn_2 = $status = $kategori = "";
$err = "";

if (isset($_POST['bekreft'])) {
  echo("aaah");
  if (strlen(trim($_POST['ISBN'])) != 17) {
    $err = "Ugyldig ISBN";
  } else {
    $ISBN = $_POST['ISBN'];
  }

  if (empty($_POST['tittel'])) {
    $err = "Ugyldig tittel";
  } else {
    $tittel = $_POST['tittel'];
  }

  if (empty($_POST['forlag'])) {
    $err = "Ugyldig forlag";
  } else {
    $forlag = $_POST['forlag'];
  }

  if (empty($_POST['status'])) {
    $err = "Ugyldig status";
  } else {
    $status = $_POST['status'];
  }

  if (empty($_POST['kategori'])) {
    $err = "Ugyldig kategori";
  } else {
    $kategori = $_POST['kategori'];
  }

  if (empty($_POST['fornavn_1']) or empty($_POST['etternavn_1'])) {
    $err = "Ugyldig forfatter";
  } else {
    $fornavn_1 = $_POST['fornavn_1'];
    $etternavn_1 = $_POST['etternavn_1'];
  }

  if (empty($_POST['fornavn_2']) and empty($_POST['etternavn_2'])) {
    $err = "Ugyldig forfatter";
  } else {
    $fornavn_2 = $_POST['fornavn_2'];
    $etternavn_2 = $_POST['etternavn_2'];
  }

  if (!empty($fornavn_2)) {
    $sql = "
            INSERT IGNORE INTO forfatter (fornavn, etternavn) values ('".$fornavn_1."', '".$etternavn_1."');
            SET @forfatter_id1 = LAST_INSERT_ID();

            INSERT IGNORE INTO forfatter (fornavn, etternavn) values ('".$fornavn_2."', '".$etternavn_2."');
            SET @forfatter_id2 = LAST_INSERT_ID();

            INSERT IGNORE INTO bok (isbn, tittel, forlag, kategori, status) values ('".$ISBN."', '".$tittel."', '".$forlag."', '".$kategori."', '".$status."');
            SET @bok_id = LAST_INSERT_ID();

            INSERT INTO forfatter_has_bok (bok_id, forfatter_idforfatter) SELECT @bok_id, idforfatter from forfatter where fornavn='".$fornavn_1."' and etternavn='".$etternavn_1."';
            INSERT INTO forfatter_has_bok (bok_id, forfatter_idforfatter) SELECT @bok_id, idforfatter from forfatter where fornavn='".$fornavn_2."' and etternavn='".$etternavn_2."';";

  } else {
    $sql = "
            INSERT IGNORE INTO forfatter (fornavn, etternavn) values ('".$fornavn_1."', '".$etternavn_1."');
            SET @forfatter_id = LAST_INSERT_ID();

            INSERT IGNORE INTO bok (isbn, tittel, forlag, kategori, status) values ('".$ISBN."', '".$tittel."', '".$forlag."', '".$kategori."', '".$status."');
            SET @bok_id = LAST_INSERT_ID();

            INSERT INTO forfatter_has_bok (bok_id, forfatter_idforfatter) SELECT @bok_id, idforfatter from forfatter where fornavn='".$fornavn_1."' and etternavn='".$etternavn_1."';";
  }

  $res = mysqli_multi_query($conn, $sql);
}
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

  <form class="registrer_bok_skjema" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <input type="text" name="ISBN" placeholder="ISBN (separert med '-')">
    <input type="text" name="tittel" placeholder="Tittel">
    <input type="text" name="forlag" placeholder="Forlag">
    <input type="text" name="kategori" placeholder="Kategori (Dewey-indeks)">
    <input type="text" name="fornavn_1" placeholder="Fornavn 1">
    <input type="text" name="etternavn_1" placeholder="Etternavn 1">
    <input type="text" name="fornavn_2" placeholder="Fornavn 2">
    <input type="text" name="etternavn_2" placeholder="Etternavn 2">
    <input list="statusliste" name="status" id="status" placeholder="Status">
    <datalist id="statusliste">
      <option value="Tilgjengelig">
      <option value="Bestilt">
      <option value="Utlånt">
    </datalist>
    <input type='submit' name='bekreft' value='bekreft'>
  </form>

  <h3>Filtrér</h3>
  <form autocomplete="off" method="POST" id="søkeskjema">
    <input autocomplete="off" name="hidden" type="text" style="display:none;">
    <label>Tittel: </label><input type="text" name="tittel" value="" id="søkefelt">
    <label>Kategori: </label><input type="text" name="kategori" value="" id="søkefelt">
    <label>ISBN:  </label><input type="text" name="ISBN" value="" id="søkefelt">
    <input type="submit" name="filtrering" value="søk" id="søkeknapp">
  </form>

  <?php
  require_once "../config.php";
  require_once "../spørringer.php";
  if (isset($_POST['filtrering'])) {
    $sql2 = $bøker_forfatterliste;
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
      $sql2 = $sql2." WHERE ".join($spørring, " and ");
    }

    $sql2 = $sql2." GROUP BY bok.id ORDER BY bok.id LIMIT 1000";

    $res = $conn->query($sql2);
    echo "<div id='boktabell'>";
    echo "<table>";
    echo "<th>Id</th>";
    echo "<th>ISBN</th>";
    echo "<th>Tittel</th>";
    echo "<th>Forlag</th>";
    echo "<th>Kategori</th>";
    echo "<th>Forfatter</th>";
    echo "<th>Status</th>";
    echo "<th>Administrer</th>";

    while($row = $res->fetch_assoc()) {
      echo "<tr>";
      echo '<td>'.$row['id'].'</td>';
      echo '<td>'.$row['ISBN'].'</td>';
      echo '<td>'.$row['tittel'].'</td>';
      echo '<td>'.$row['forlag'].'</td>';
      echo '<td>'.$row['kategorinavn'].'</td>';
      echo '<td>'.$row['forfatternavn'].'</td>';
      echo '<td>'.$row['status'].'</td>';
      echo '<td><a href="rediger_bok.php?id='.$row['id'].'">Rediger</a></td>';
      echo '<td><a href="slett_bok.php?id='.$row['id'].'&tittel='.$row['tittel'].'">Slett</a></td>';
      echo "</tr>";
    }
    echo "</table></div>";
  }
  ?>

</body>
</html>
