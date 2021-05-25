<!doctype html>
<html>
<head>
  <title>Stormen bibliotek</title>
  <link href="stilark/style.css" type="text/css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;700&display=swap" rel="stylesheet">
<body>
  <div class="innhold">
    <div id="header">
    <div id="nav_meny">
      <div class=meny_div>
       <img id="bildelogo" class="meny_element" src="grafisk/stormen.png">
      </div>

      <div class=meny_div>
        <li class="meny_element"><a href ="bøker.php">Se alle bøker</a></li>
      </div>
      <div class="meny_div">
        <li class="meny_element"><a href ="personlig/utlån.php">Logg inn</a></li>
      </div>
      <div class="meny_div">
        <li class="meny_element"><a href ="admin/ansatt_login.php">For ansatte</a></li>
      </div>
    </div>
  </div>

  <h1>Søk i Stormen Biblioteks digitale bibliotek!</h1>

    <?php
    require_once("config.php");

        //kobling hentes fra config.php
        $kobling = $conn;

       /* <!-- if ($kobling->connect_error) {
          die( "Du er en mislykkelse" . $kobling->connect_error);
        } else {
            echo "WOW det funket!";
        } */

      $kobling->set_charset("utf8");

      //trekker ut 3 tilfeldige verdier fra tabell

      $sql = "SELECT id, tittel  FROM bok
      ORDER BY RAND()
      LIMIT 3";

      $resultat = $kobling->query($sql);

      //formaterer resultatet
      while($rad = $resultat->fetch_assoc()){
        $id = $rad["id"];
        $tittel = $rad["tittel"];

        echo "$id $tittel <br>";
      }

    ?>

    </div class="boktips">
      <div>

      </div>

      <div>

      </div>

      <div>

      </div>

    <div>


  </div>
</body>
</html>
