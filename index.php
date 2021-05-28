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

        # NAVIGASJONSMENY SOM OPPDATERES MED HENSYN AV TILGJENGELIGHETSNIVÅ; DVS. GJEST, BRUKER ELLLER ADMINISTRATOR
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

  <!-- FILTRERINGSSKJEMA -->
  <div id="søkeskjema_wrap">
    <h1 class="stor_overskrift">Søk i Stormens digitale bibliotek!</h1>
    <form autocomplete="off" method="POST" class="tittelsøk">
      <input autocomplete="off" name="hidden" type="text" style="display:none;">
      <input type="text" name="tittel" placeholder="Søk etter tittel..." id="tekstfelt">
      <input type="submit" name="søk" class="søkeknapp">
    </form>
    <div>

    <?php
    require_once "config.php";
    require_once "spørringer.php";

    # KJØRER KODEN ETTER AT SØK-KNAPP ER PRESSET
      if (isset($_POST['søk'])) {
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
        echo "<div id='bokvisning_medium'><table id='bokvisning_tabell'>";

        # PRINTER RESULTATET
        while($row = $res->fetch_assoc()) {
          echo "<tr><td>";
          echo "<div class='bok'>";
          echo "<h2 class='bv_tittel'>".$row['tittel']."</h2>";
          echo "<p class='bv_forfatter'>av ".$row['forfatternavn']."<p>";
          echo '<p class ="bv_kategori">Kategori: '.$row['kategorinavn'].'<p>';
          echo '<p class ="bv_hylleplass">Hylleplass: '.$row['idDewey'].'</p>';
          echo "<p class='bv_isbn'><em>ISBN: ".$row['ISBN']."</em></h3>";
          echo "<p class='bv_isbn'><em>ID: ".$row['id']."</em></h3>";
          echo "</div>";

          if($row['status'] == 'Tilgjengelig') {
            echo '<a class="bv_låneknapp" href="personlig/utlån.php?bok_id='.$row['id'].'">Lån bok</a>';
          } elseif ($row['status'] == 'Bestilt') {
            echo '<a class="bv_låneknapp">Utlånt</a>';
          } else {
            echo '<a class="bv_låneknapp">Bestilt</a>';
          }

          echo "</td></tr>";
        }
        echo "</table></div>";
      } else if (isset($_POST['tittel']) && !isset($res)) {
      echo "<p style='text-align: center'> Fant ingen resultater. Prøv et mer generelt søkeord.</p>";
    }
  }

      //kobling hentes fra config.php
      $kobling = $conn;

      //trekker ut 3 tilfeldige verdier fra tabell
      $sql = $bøker_forfatterliste." GROUP BY bok.id ORDER BY RAND() LIMIT 3";
      $resultat = $kobling->query($sql);

      echo "<div class='boktips_main'>";

          echo "<div class=header> <h2> Boktips: </h2> </div>";

          echo "<div class='boktips_innhold'>";
              //formaterer resultatet
              while($rad = $resultat->fetch_assoc()){
                $id = $rad["id"];
                $tittel = $rad["tittel"];
                $kategori = $rad["kategorinavn"];
                $forfatter = $rad['forfatternavn'];

                echo
                "<div class='boktips_bokser'>
                <h3> $id $tittel </h3> <br> Kategori: $kategori <br> Forfatter: $forfatter
                </div>";
              }
        "</div>";
    "</div>";
    ?>
  </div>
</body>
</html>
