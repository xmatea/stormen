<!doctype html>
<html>
<head>
  <title>Stormen bibliotek</title>
  <link href="stilark/style.css" type="text/css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;700&display=swap" rel="stylesheet">
<body>
  <div class="innhold">
    <div id="nav_meny">
      <div class=element_div>
       <img id="bildelogo" class="meny_element" src="grafisk/stormen.png">
      </div>

      <div class=element_div>
        <li class="meny_element"><a href ="bøker.php">Se alle bøker</a></li>
      </div>

      <div class="element_div">
        <li class="meny_element"><a href ="personlig/login.php">Logg inn</a></li>
      </div>

      <div class="element_div">
        <li class="meny_element"><a href ="admin/admin_login.php">For ansatte</a></li>
      </div>
    </div>

  <h1>Søk i Stormen Biblioteks digitale bibliotek!</h1>

    <?php
    require_once("config.php");
    require_once("spørringer.php");

        //kobling hentes fra config.php
        $kobling = $conn;

       /* <!-- if ($kobling->connect_error) {
          die( "Du er en mislykkelse" . $kobling->connect_error);
        } else {
            echo "WOW det funket!";
        } */


      $kobling->set_charset("utf8");

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
