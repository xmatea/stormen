<?php
# starter en session
session_start();

# sjekker om bruker er allerede logget inn
if (!isset($_SESSION['admin']) || $_SESSION['admin'] == false) {
  # sender brukeren til admin_login.php og avslutt
  header("location: admin_login.php");
  exit;
}

require_once('../config.php');

$ISBN = $tittel = $forlag = $fornavn_1 = $fornavn_2 = $etternavn_1 = $etternavn_2 = $status = $kategori = "";
$err = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (strlen(trim($_POST['ISBN'])) != 17) {
    $err = "Ugyldig ISBN";
  } else {
    $ISBN = $_POST['ISBN'];
  }

  if (empty($_POST['tittel'])) {
    $err = "Ugyldig tittel";
  } else {
    $tittel = $_POST['tittel'];
  }

  if (empty($_POST['forlag'])) {
    $err = "Ugyldig forlag";
  } else {
    $forlag = $_POST['forlag'];
  }

  if (empty($_POST['status'])) {
    $err = "Ugyldig status";
  } else {
    $status = $_POST['status'];
  }

  if (empty($_POST['kategori'])) {
    $err = "Ugyldig kategori";
  } else {
    $kategori = $_POST['kategori'];
  }

  if (empty($_POST['fornavn_1']) or empty($_POST['etternavn_1'])) {
    $err = "Ugyldig forfatter";
  } else {
    $fornavn_1 = $_POST['fornavn_1'];
    $etternavn_1 = $_POST['etternavn_1'];
  }

  if (!empty($_POST['fornavn_2']) and !empty($_POST['etternavn_2'])) {
    $err = "Ugyldig forfatter";
  } else {
    $fornavn_2 = $_POST['fornavn_2'];
    $etternavn_2 = $_POST['etternavn_2'];
  }
  echo($err);
  echo($fornavn_1);
  $sql = "INSERT INTO bok
  (isbn, tittel, forlag, kategori, status)
  values ('".$ISBN."', '".$tittel."', '".$forlag."', '".$kategori."', '".$status."')";

  $sql_forfatter = "";

  if (!empty($fornavn_2)) {
    $sql_forfatter = "INSERT INTO forfatter
    (fornavn, etternavn)
    values ('".$fornavn_1."', '".$etternavn_1."'), ('".$fornavn_2."', '".$etternavn_2."')";

  } else {
    $sql_forfatter = "INSERT INTO forfatter
    (fornavn, etternavn)
    values ('".$fornavn_1."', '".$etternavn_1."')";
  }

  echo($sql);
  echo($sql_forfatter);
  $res = mysqli_query($conn, $sql);
  while($res->fetch_assoc()) {
    var_dump($res);
  }


  $res_forfatter = $conn->query($sql_forfatter);
  while($forfatter = $res_forfatter->fetch_assoc()) {
    echo($forfatter['id'].", ".$bok['id']);
  }
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
  <a href="logout.php">Logg ut</a>

  <div id="nav_meny">
    <div class="meny_div">
      <li class="meny_element"><a href ="bøker_admin.php">Administrer bøker</a></li>
    </div>
    <div class="meny_div">
      <li class="meny_element"><a href ="lån_admin.php">Administrer lån</a></li>
    </div>
    <div class="meny_div">
      <li class="meny_element"><a href ="kalender_admin.php">Kalender</a></li>
    </div>
  </div>

  <form class="registrer_bok_skjema" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <input type="text" name="ISBN" placeholder="ISBN (separert med '-')">
    <input type="text" name="tittel" placeholder="Tittel">
    <input type="text" name="forlag" placeholder="Forlag">
    <input type="text" name="kategori" placeholder="Kategori (Dewey-indeks)">
    <input type="text" name="fornavn_1" placeholder="Fornavn 1">
    <input type="text" name="etternavn_1" placeholder="Etternavn 1">
    <input type="text" name="fornavn_2" placeholder="Fornavn 2">
    <input type="text" name="etternavn_2" placeholder="Etternavn 2">
    <input list="statusliste" name="status" id="status" placeholder="Status">
    <datalist id="statusliste">
      <option value="Tilgjengelig">
      <option value="Bestilt">
      <option value="Utlånt">
    </datalist>
    <input type="submit">
  </form>

</body>
</html>
