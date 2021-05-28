<!doctype html>
<html>
<head>
  <title>Stormen bibliotek</title>
  <link href="stilark/style.css" type="text/css" rel="stylesheet">
  <link href="stilark/tabell.css" type="text/css" rel="stylesheet">
  <link href="stilark/skjema.css" type="text/css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;700&display=swap" rel="stylesheet">
<body>
  <div id="topp_meny">
     <a href="index.php"><img id="bildelogo" src="grafisk/stormen.png"></a>
        <?php
        session_start();
        # navigasjonsmeny som varierer med tilgangsnivå
        if (isset($_SESSION['admin']) && $_SESSION['admin'] == true) {
          echo '
          <div id="navigasjon">
            <li><a href ="admin/bøker_admin.php">Administrer bøker</li>
            <li><a href ="admin/lån_admin.php">Administrer lån</li>
          </div>
          <div id="innlogging">
            <li><a href="logout.php">Logg ut</a></li>
          </div>';
        } elseif (isset($_SESSION['innlogget']) && $_SESSION['innlogget'] == true) {
          echo'
          <div id="navigasjon">
            <li><a href ="bøker.php">Finn bok</a></li>
            <li><a href ="personlig/utlån.php">Utlån</li>
            <li><a href ="personlig/innlevering.php">Innlevering</li>
            <li><a href ="personlig/hjem.php">Min side</li>
          </div>
          <div id="innlogging">
            <li><a href="logout.php">Logg ut</a></li>
            <li><a href="admin/admin_login.php">For ansatte</a></li>
          </div>';
        } else {
          echo'<div id="navigasjon">
            <li><a href ="bøker.php">Finn bok</a></li>
            <li><a href ="personlig/utlån.php">Utlån</li>
            <li><a href ="personlig/innlevering.php">Innlevering</li>
          </div>
          <div id="innlogging">
            <li><a href="personlig/login.php">Logg inn</a></li>
            <li><a href="admin/admin_login.php">For ansatte</a></li>
          </div>';
        }
          ?>
      </div>


  <!-- boksøkingskjema -->
  <div id="søkeskjema_wrap">
  <form autocomplete="off" method="POST" id="søkeskjema">
    <h3 id="filtrer">Filtrér</h3>
    <input autocomplete="off" name="hidden" type="text" style="display:none !important;">
    <input type="text" name="tittel" placeholder="Søk etter tittel" id="søkefelt">
    <input type="text" name="ISBN" placeholder="Søk etter ISBN" id="søkefelt">
    <input list="kategoriliste" name="kategori" placeholder="Søk etter kategori">
    <!-- genererer liste for kategorisøk -->
    <datalist id="kategoriliste">
      <?php
      require_once "config.php";
      $sql = "SELECT tittel from Dewey";
      $res = mysqli_query($conn, $sql);
      while($row = $res->fetch_assoc()) {
        echo "<option value='".$row['tittel']."'>";
      }
        ?>
    </datalist>
    <input type="submit" value="Søk" name="søk">
  </form>
</div>


  <?php
  # henter spørringer og databasekobling
  require_once "config.php";
  require_once "spørringer.php";
    $sql = $bøker_forfatterliste;
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

    $sql = $sql." GROUP BY bok.id ORDER BY bok.id LIMIT 500";

    $res = $conn->query($sql);
    if ($res) {
    echo "<div id='bokvisning_liten'>";
    echo "<table>";
    echo "<th>ID</th>";
    echo "<th>Tittel</th>";
    echo "<th>Forfatter</th>";
    echo "<th>Kategori</th>";
    echo "<th>Forlag</th>";
    echo "<th>ISBN</th>";
    echo "<th>Status</th>";

    # viser bøker i tabell
    while($row = $res->fetch_assoc()) {
      echo "<tr>";
      echo '<td>'.$row['id'].'</td>';
      echo '<td>'.$row['tittel'].'</td>';
      echo '<td>'.$row['forfatternavn'].'</td>';
      echo '<td>'.$row['kategorinavn'].'</td>';
      echo '<td>'.$row['forlag'].'</td>';
      echo '<td>'.$row['ISBN'].'</td>';
      echo '<td>'.$row['status'].'</td>';
      echo "</tr>";
    }
    echo "</table></div>";
  } else {
    echo "<p style='text-align: center'> Fant ingen resultater. Prøv et mer generelt søkeord.</p>";
  }
  ?>
</body>

</html>
