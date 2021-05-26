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

  if ($err) {
    echo "En feil oppsto! Alle felt må fylles ut.";
  } else {
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

    $res_bok = mysqli_multi_query($conn, $sql);
  }
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

<div id="administratorskjema">
  <form id="bokinnleggingsskjema" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <h1>Registrér bok</h1>
    <div>
      <input type="text" name="ISBN" placeholder="ISBN (separert med '-')">
      <input type="text" name="tittel" placeholder="Tittel">
    </div>
    <div>
      <input type="text" name="forlag" placeholder="Forlag">
      <input type="text" name="kategori" placeholder="Kategori (Dewey-indeks)">
    </div>
    <div>
      <input type="text" name="fornavn_1" placeholder="Fornavn 1">
      <input type="text" name="etternavn_1" placeholder="Etternavn 1">
    </div>
    <div>
      <input type="text" name="fornavn_2" placeholder="Fornavn 2">
      <input type="text" name="etternavn_2" placeholder="Etternavn 2">
    </div>
    <div>
    <input list="statusliste" name="status" id="status" placeholder="Status">
    <datalist id="statusliste">
      <option value="Tilgjengelig">
      <option value="Bestilt">
      <option value="Utlånt">
    </datalist>
  </div>
  <div>
    <input type='submit' name='bekreft' value='Legg til' class="søkeknapp">
  </div>
  </form>

  <form autocomplete="off" method="POST" id="søkeskjema">
    <h1>Rediger bok</h1>
    <input autocomplete="off" name="hidden" type="text" style='display:none !important;'>
    <div>
    <input type="text" name="ID" value="" id="søkefelt" placeholder="Søk etter ID">
    <input type="text" name="tittel" value="" id="søkefelt" placeholder="Søk etter tittel">
  </div>
  <div>
    <input type="text" name="kategori" value="" id="søkefelt" placeholder="Søk etter kategori">
    <input type="text" name="ISBN" value="" id="søkefelt" placeholder="Søk etter ISBN">
  </div>
  <!--  <label>Forfatter:  </label><input type="text" name="forfatter" value="" id="søkefelt"> -->
    <input type="submit" name="filtrering" value="Søk" class="søkeknapp">
  </form>
</div>

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
        } elseif ($field == 'ID') {
         array_push($spørring, "bok.id = ".$value);
        }
      }
      if (count($spørring) > 0) {
        $sql2 = $sql2." WHERE ".join($spørring, " and ");
      }
    }

    $sql2 = $sql2." GROUP BY bok.id ORDER BY bok.id LIMIT 1000";

    $res = $conn->query($sql2);
      echo "<div id='bokvisning_liten'>";
      echo "<table>";
      echo "<th>Id</th>";
      echo "<th>ISBN</th>";
      echo "<th>Tittel</th>";
      echo "<th>Forlag</th>";
      echo "<th>Kategori</th>";
      echo "<th>Forfatter</th>";
      echo "<th>Status</th>";
      echo "<th>Administrer</th>";
      echo "<th></th>";

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
