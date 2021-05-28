<?php
  session_start();
  if ($_SESSION['innlogget'] != true) {
    header('location: login.php');
  }

  require_once "../config.php";
  require_once "../spørringer.php";
    $sql = $bøker_forfatterliste;

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

  <h1 class="stor_overskrift" style="text-align: center">Lån bok</h1>
  <h2 class"medium_overskrift" style="text-align: center">Søk etter tittel eller ISBN</h2>

  <div id="søkeskjema_wrap">
  <form autocomplete="off" method="POST" id="søkeskjema">
    <input autocomplete="off" name="hidden" type="text" style="display:none !important;">
    <input type="text" name="tittel" placeholder="Tittel">
    <input type="text" name="kategori" placeholder="Kategori">
    <input type="text" name="ISBN" placeholder="ISBN">
    <input type="submit" name="søk" value="søk">
  </form>
</div>

<?php
  $sql = $bøker_forfatterliste;
  $filter = array_filter($_POST);
  if (isset($_GET['bok_id'])) {
    $res = mysqli_query($conn, "SELECT * from bok where id=".$_GET['bok_id']);
    $row = $res->fetch_assoc();
    if ($row['status'] == 'Tilgjengelig') {
      $tittel = $row['tittel'];
        echo '
        <div id="handlingsbekreftelse">
        <h1>Bekreft utlån: '.$tittel.'</h1>
        <form method="post">
          <input type="submit" value="Levér inn" name="bekreft">
        </form>
        </div>';
    } else {
      echo "<p>Denne boken er ikke tilgjengelig.</p>";
    }

  }

  if (isset($_POST['bekreft'])) {
    # Utfører en ny spørring, silk at man kan låne bøker direkte med link: personlig/utlån.php?bokid=1775
    $sql = "
    INSERT INTO utlån VALUES (".$_GET['bok_id'].", CURRENT_DATE + INTERVAL 1 MONTH, '".$_SESSION['personnummer']."');
    UPDATE bok SET status='Utlånt' WHERE id=".$_GET['bok_id'].";";

    $res = mysqli_multi_query($conn, $sql);
    if ($res) {
      echo "Suksess!";
      header("location: utlån.php");
    } else {
      echo "Noe gikk galt.";
    }
  }

  if (isset($_POST['søk'])) {
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
      $sql = $sql." WHERE ".join($spørring, " and ")." and bok.status='Tilgjengelig'";
    }

  $sql = $sql." GROUP BY bok.id ORDER BY bok.id LIMIT 500";
  $res = $conn->query($sql);
  $filter = array_filter($_POST);
  $res = $conn->query($sql);
  echo "<div id='bokvisning_stor_wrap'style='margin-top: 50px;'>";
  #echo "<h2 class='tabell_overskrift'>Lån tittel</h2>";
  echo "<div id='bokvisning_medium'>";
  echo "<table id='bokvisning_tabell'>";
  if ($res) {
    if ($res->num_rows == 0) {
      echo "<p>Fant ingen resultater. Prøv et mer generelt søkeord.</p>";
    }
    while($row = $res->fetch_assoc()) {
      echo "<tr><td>";
      echo "<div class='bok'>";
      echo "<p class='bv_isbn'><em>ID: ".$row['id']."</em></h3>";
      echo "<h2 class='bv_tittel'>".$row['tittel']."</h2>";
      echo "<p class='bv_forfatter'>av ".$row['forfatternavn']."<p>";
      echo '<p class ="bv_kategori">Kategori: '.$row['kategorinavn'].'<p>';
      echo "<p class='bv_isbn'><em>ISBN: ".$row['ISBN']."</em></h3>";
      echo "</div>";
      if($row['status'] == 'Tilgjengelig') {
        echo '<a class="bv_låneknapp" href="utlån.php?bok_id='.$row['id'].'">Lån bok</a>';
      } elseif ($row['status'] == 'Utlånt') {
        echo '<a class="bv_låneknapp">Utlånt</a>';
      } else {
        echo '<a class="bv_låneknapp">Bestilt</a>';
      }

      echo "</td></tr>";
    }
  }
  echo "</table></div></div>";
?>
</body>
</html>
