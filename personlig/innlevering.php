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

  <h1 class="sideoverskrift">Levér inn bok</h1>
  <?php
  require_once "../config.php";
    $sql = "SELECT * FROM utlån JOIN bok ON utlån.bokid=bok.id WHERE utlånerid =".$_SESSION['personnummer'];
    $filter = array_filter($_POST);
    $res = $conn->query($sql);

    echo "<div id='boktabell'>";
    echo "<form method='GET' id='utlånsvalg'>";
    echo "<table><tr>";
    echo "<th>Tittel</th>";
    echo "<th>Forfallsdato</th>";
    echo "<th>Lever inn</th></tr>";


    while($row = $res->fetch_assoc()) {
      echo "<tr>";
      echo '<td>'.$row['tittel'].'</td>';
      echo '<td>'.$row['utlånsdato'].'</td>';
        echo '<td><input type="radio" name="bokid" value='.$row['id'].'></td>';
      echo "</tr>";
    }
    echo "<input type='submit'>";
    echo "</table></form></div>";

    if (isset($_GET['bokid'])) {
      # Utfører en ny spørring, silk at man kan låne bøker direkte med link: personlig/utlån.php?bokid=1775
      $sql = "SELECT * from bok where id=".$_GET['bokid'];
      $res = $conn->query($sql);
      $row = $res->fetch_assoc();
      $sql1 = "DELETE FROM utlån WHERE bokid=".$row['id'];
      $sql2 = "UPDATE bok SET status='Tilgjengelig' WHERE id=".$row['id'].";";
      $res1 = mysqli_query($conn, $sql1);
      $res2 = mysqli_query($conn, $sql2);
      var_dump($sql1);
      var_dump($res2);
    }
    ?>

</body>
</html>
