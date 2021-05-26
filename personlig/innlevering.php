<?php
  session_start();
  if ($_SESSION['innlogget'] != true) {
    header('location: login.php');
  }
?>
<!DOCTYPE html>
<html>
<head>
  <title>Stormen bibliotek</title>
  <link href="../stilark/style.css" type="text/css" rel="stylesheet">
  <link href="../stilark/tabell.css" type="text/css" rel="stylesheet">
  <link href="../stilark/skjema.css" type="text/css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;700&display=swap" rel="stylesheet">
<body>
  <div id="topp_meny">
     <a href="../index.php"><img id="bildelogo" src="../grafisk/stormen.png"></a>
        <?php
        if (isset($_SESSION['admin']) && $_SESSION['admin'] == true) {
          echo '
          <div id="navigasjon">
            <li><a href ="../admin/bøker_admin.php">Administrer bøker</li>
            <li><a href ="../admin/lån_admin.php">Administrer lån</li>
          </div>
          <div id="innlogging">
            <li><a href="../logout.php">Logg ut</a></li>
          </div>';
        } elseif (isset($_SESSION['innlogget']) && $_SESSION['innlogget'] == true) {
          echo'
          <div id="navigasjon">
            <li><a href ="../bøker.php">Finn bok</a></li>
            <li><a href ="utlån.php">Utlån</li>
            <li><a href ="innlevering.php">Innlevering</li>
            <li><a href ="hjem.php">Min side</li>
          </div>
          <div id="innlogging">
            <li><a href="../logout.php">Logg ut</a></li>
            <li><a href="../admin/admin_login.php">For ansatte</a></li>
          </div>';
        } else {
          echo'<div id="navigasjon">
            <li><a href ="../bøker.php">Finn bok</a></li>
            <li><a href ="utlån.php">Utlån</li>
            <li><a href ="innlevering.php">Innlevering</li>
          </div>
          <div id="innlogging">
            <li><a href="login.php">Logg inn</a></li>
            <li><a href="../admin/admin_login.php">For ansatte</a></li>
          </div>';
        }
          ?>
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
