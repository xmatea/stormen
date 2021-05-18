<?php
  session_start();
  if ($_SESSION['innlogget'] != true) {
    header('location: login.php');
  }
?>
<!DOCTYPE html>
<html>
<head>
  <link href="../stilark/style.css" type="text/css" rel="stylesheet">
  <link href="../stilark/login.css" type="text/css" rel="stylesheet">
</head>
<body>
  <h1 class="logo">Stormen Bibliotek</h1>
  <a href="../logout.php">Logg ut</a>

  <div id="nav_meny">
    <div class=meny_div>
      <li class="meny_element"><a href ="bøker.php">Søk i bøker</a></li>
    </div>
    <div class="meny_div">
      <li class="meny_element"><a href ="personlig/utlån.php">Utlån</a></li>
    </div>
    <div class="meny_div">
      <li class="meny_element"><a href ="personlig/innlevering.php">Innlevering</a></li>
    </div>
    <div class="meny_div">
      <li class="meny_element"><a href ="personlig/hjem.php">Mine bøker</a></li>
    </div>
    <div class="meny_div">
      <li class="meny_element"><a href ="ansatt_login.php">For ansatte</a></li>
    </div>
  </div>

  <h1 class="sideoverskrift">Mine bøker</h1>
  <?php
  require_once "../config.php";
    $sql = "SELECT * FROM utlån JOIN bok ON utlån.bokid=bok.id WHERE utlånerid =".$_SESSION['personnummer'];
    $filter = array_filter($_POST);
    $res = $conn->query($sql);

    echo "<div id='boktabell'>";
    echo "<table>";
    echo "<th>Tittel</th>";
    echo "<th>Forfallsdato</th>";

    while($row = $res->fetch_assoc()) {
      echo "<tr>";
      echo '<td>'.$row['tittel'].'</td>';
      echo '<td>'.$row['utlånsdato'].'</td>';
      echo "</tr>";
    }
    echo "</table></div>";
  ?>

</body>
</html>
