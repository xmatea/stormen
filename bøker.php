<!doctype html>
<html>
<head>
  <link href="style.css" type="text/css" rel="stylesheet">
</head>
<body>

  <h1 class="logo"><a href=index.php>Stormen bibliotek</a></h1>
  <div id="nav_meny">
    <div class=meny_div>
      <li class="meny_element"><a href ="bøker.php">Finn bøker</a></li>
    </div>
    <div class="meny_div">
      <li class="meny_element"><a href ="utlån.php">Utlån</a></li>
    </div>
    <div class="meny_div">
      <li class="meny_element"><a href ="innlevering.php">Innlevering</a></li>
    </div>
    <div class="meny_div">
      <li class="meny_element"><a href ="ansatt_login.php">For ansatte</a></li>
    </div>
  </div>
  <h3>Filtrér</h3>
  <form autocomplete="off" method="POST" id="søkeskjema">
    <input autocomplete="off" name="hidden" type="text" style="display:none;">
    <label>Tittel: </label><input type="text" name="tittel" value="" id="søkefelt">
    <label>Kategori: </label><input type="text" name="kategori" value="" id="søkefelt">
    <label>ISBN:  </label><input type="text" name="ISBN" value="" id="søkefelt">
    <input type="submit" value="GO" id="søkeknapp">
  </form>
  <?php
  require_once "config.php";
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
      $sql = $sql." WHERE ".join($spørring, " and ");
    }

    $res = $conn->query($sql);
    echo "<div id='boktabell'>";
    echo "<table>";
    echo "<th>Id</th>";
    echo "<th>ISBN</th>";
    echo "<th>Tittel</th>";
    echo "<th>Forlag</th>";
    echo "<th>Kategori</th>";
    echo "<th>Status</th>";

    while($row = $res->fetch_assoc()) {
      echo "<tr>";
      echo '<td>'.$row['id'].'</td>';
      echo '<td>'.$row['ISBN'].'</td>';
      echo '<td>'.$row['tittel'].'</td>';
      echo '<td>'.$row['forlag'].'</td>';
      echo '<td>'.$row['kategorinavn'].'</td>';
      echo '<td>'.$row['status'].'</td>';
      echo "</tr>";
    }
    echo "</table></div>";
  ?>
</body>

</html>
