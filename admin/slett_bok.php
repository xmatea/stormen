<?php
session_start();

# sjekker om bruker er allerede logget inn
if (!isset($_SESSION['admin']) || $_SESSION['admin'] == false) {
  # sender brukeren til admin_login.php og avslutt
  header("location: admin_login.php");
  exit;
}

require_once("../config.php");
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
  if (isset($_GET['id']) and isset($_GET['tittel'])) {
      echo "<h2>Ønsker du å slette '".$_GET['tittel']."'?</h2>";
      echo "<form method='post'>";
      echo "<input type='submit' name='bekreft' value='bekreft'>";
      echo "</form>";
  }

  if(isset($_POST['bekreft'])) {
    $sql = "
    DELETE FROM forfatter_has_bok WHERE bok_id=".$_GET['id'].";
    DELETE FROM bok where id=".$_GET['id'].";
    DELETE FROM utlån where bokid=".$_GET['id'];
    $res = mysqli_multi_query($conn, $sql);
    echo($sql);
    if($res) {
      echo "Suksess!";
    } else {
      echo "Noe gikk galt.";
    }
  }
  ?>
</body>
</html>
