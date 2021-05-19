<!doctype html>
<html>
<head>
  <title>Stormen bibliotek</title>
  <link href="stilark/style.css" type="text/css" rel="stylesheet">
<body>
  <div class="innhold">
    <h1 id="logo"><a href=index.php>Stormen bibliotek</a></h1>
    <div id="nav_meny">
      <div class=meny_div>
        <li class="meny_element"><a href ="bøker.php">Finn bøker</a></li>
      </div>
      <div class="meny_div">
        <li class="meny_element"><a href ="personlig/utlån.php">Utlån</a></li>
      </div>
      <div class="meny_div">
        <li class="meny_element"><a href ="personlig/innlevering.php">Innlevering</a></li>
      </div>
      <div class="meny_div">
        <li class="meny_element"><a href ="admin/ansatt_login.php">For ansatte</a></li>
      </div>
    </div>

    <?php
        //kobling
        $tjener = "localhost";
        $brukernavn = "root";
        $passord = "";
        $database = "bibliotek";

        $kobling = new mysqli($tjener, $brukernavn, $passord, $database);

       /* <!-- if ($kobling->connect_error) {
          die( "Du er en mislykkelse" . $kobling->connect_error);
        } else {
            echo "WOW det funket!";
        } */

      $kobling->set_charset("utf8");

      //trekker ut 3 tilfeldige verdier fra tabell

      $sql = "SELECT id, tittel  FROM bibliotek.bok
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
