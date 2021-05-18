<?php
  session_start();
  if ($_SESSION['innlogget'] == false) {
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
  <h1 class="logo" href="idex.php">Stormen Bibliotek</h1>
  <a href="../logout.php">Logg ut</a>

  <div id="nav_meny">
    <div class=meny_div>
      <li class="meny_element"><a href ="../bøker.php">Finn bøker</a></li>
    </div>
    <div class="meny_div">
      <li class="meny_element"><a href ="utlån.php">Utlån</a></li>
    </div>
    <div class="meny_div">
      <li class="meny_element"><a href ="innlevering.php">Innlevering</a></li>
    </div>
    <div class="meny_div">
      <li class="meny_element"><a href ="hjem.php">Mine bøker</a></li>
    </div>
    <div class="meny_div">
      <li class="meny_element"><a href ="../admin/ansatt_login.php">For ansatte</a></li>
    </div>
  </div>

  <h1><a href="personlig/oversikt.php">Låneoversikt</a></h1>
  <h1><a href="personlig/personlig/utlån.php">Lån bok</h1>
  <h1><a href="personlig/innlevering.php">Levér inn bok</a></h1>

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
