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

  <div id="sideinnhold">
  <h1 class="stor_overskrift">Søk i Stormen Biblioteks digitale bibliotek!</h1>

  <form autocomplete="off" method="POST" class="tittelsøk">
    <input autocomplete="off" name="hidden" type="text" style="display:none;">
        <input type="text" name="tittel" placeholder="Søk etter tittel..." value="" id="tekstfelt">
    <input type="submit" value="Søk" name="søk" id="søkeknapp">
  </form>

    <?php
    require_once "config.php";
    require_once "spørringer.php";
    if (isset($_POST['søk'])) {

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
          $sql = $sql." WHERE ".join($spørring, " and ");
        }

        $sql = $sql." GROUP BY bok.tittel ORDER BY bok.id LIMIT 1000";

        $res = $conn->query($sql);
        echo "<div id='bokvisning_innpakning'><table id='bokvisning'>";

        while($row = $res->fetch_assoc()) {
          echo "<tr>";
          echo"<td>";

          echo "<div class='bok'>";
          echo "<p class='bokdisplay_isbn'>ISBN: ".$row['ISBN']."</h3>";
          echo "<h2 class='bokdisplay_tittel'>".$row['tittel']."</h2>";
          echo "<p class='bokdisplay_forfatter'>av ".$row['forfatternavn']."<p>";
          echo '<p class ="bokdisplay_kategori">Kategori: '.$row['kategorinavn'].'<p>';
          echo '<p>Hylleplass: '.$row['idDewey'].'</p>';


          echo "</div>";
            echo"</td>";

          #echo '<td>'.$row['forlag'].'</td>';
          #echo '<td>'.$row['kategorinavn'].'</td>';
        #  echo '<td>'.$row['forfatternavn'].'</td>';
          #echo '<td>'.$row['status'].'</td>';
          echo "</tr>";
        }
        echo "</table></div>";
      }
      //kobling hentes fra config.php
      $kobling = $conn;

      //trekker ut 3 tilfeldige verdier fra tabell
      $sql = $bøker_forfatterliste." GROUP BY bok.id ORDER BY RAND() LIMIT 3";
      $resultat = $kobling->query($sql);

      //formaterer resultatet
      while($rad = $resultat->fetch_assoc()){
        $id = $rad["id"];
        $tittel = $rad["tittel"];

        echo "$id $tittel ".$rad['forfatternavn']." <br>";
      }

    ?>
</div>
</body>
</html>
