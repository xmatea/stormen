<?php
session_start();

# sjekker om bruker er allerede logget inn
if (!isset($_SESSION['admin']) || $_SESSION['admin'] == false) {
  # sender brukeren til admin_login.php og avslutt
  header("location: admin_login.php");
  exit;
}

require_once("../config.php");
require_once("../spørringer.php");
?>

<!DOCTYPE html>
<html>
<head>
  <link href="../stilark/style.css" type="text/css" rel="stylesheet">
  <link href="../stilark/login.css" type="text/css" rel="stylesheet">
</head>
<body>
  <h1 class="logo" href="idex.php">Stormen Bibliotek</h1>
  <a href="logout.php">Logg ut</a><br>
  <a href="bøker_admin.php">tilbake...</a>
  <?php
  if (isset($_GET['id']) and isset($_GET['personnummer'])) {
      $sql = $utlånerliste." WHERE bokid=".$_GET['id']." and utlånerid='".$_GET['personnummer']."'";
      $res = mysqli_query($conn, $sql);
      $row = $res->fetch_assoc();
      echo "<h2>Sett ny faktureringsdato for bok '".$row['tittel']."'?</h2>";
      echo "<form method='post'>";
      echo "<input type='date' name='dato' value='".$row['utlånsdato']."'>";
      echo "<input type='submit' name='bekreft' value='bekreft'>";
      echo "</form>";
  }

  if(isset($_POST['bekreft'])) {
    $sql = "UPDATE utlån SET utlånsdato='".$_POST['dato']."' WHERE bokid=".$_GET['id']." and utlånerid='".$_GET['personnummer']."'";
    $res = mysqli_query($conn, $sql);
    echo($sql);
  }
  ?>
</body>
</html>
