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
        # navigasjonsmeny som varierer med tilgangsnivå; gjest, bruker eller administrator
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

  <h1 class="hjem_overskrift"><?php echo "Hei, ".$_SESSION['fornavn']." ".$_SESSION['etternavn']?>!</h1>
  <?php
  require_once "../config.php";
  require_once "../spørringer.php";
    $sql = $utlånerliste." WHERE utlånerid ='".$_SESSION['personnummer']."' GROUP BY bok.id ORDER BY bok.id LIMIT 100";
    $filter = array_filter($_POST);
    $res = $conn->query($sql);
    echo "<div id='bokvisning_stor_wrap'>";
    echo "<h2 class='tabell_overskrift'>Dine bøker</h2>";
    echo "<div id='bokvisning_stor'>";
    echo "<table id='bokvisning_tabell'>";
    while($row = $res->fetch_assoc()) {
      echo "<tr><td>";
      echo "<div class='bok'>";
      echo "<p class='bv_isbn'><em>ID: ".$row['id']."</em></h3>";
      echo "<h2 class='bv_tittel'>".$row['tittel']."</h2>";
      echo "<p class='bv_forfatter'>av ".$row['forfatternavn']."<p>";
      echo '<p class ="bv_kategori">Kategori: '.$row['kategorinavn'].'<p>';
      echo "<p class='bv_isbn'><em>ISBN: ".$row['ISBN']."</em></h3>";
      echo "<p class='bv_dato'><strong>Lån gyldig til: ".$row['utlånsdato']."</strong></h3>";
      echo "</div>";
      echo "</td></tr>";
    } if ($res->num_rows == 0) {
      echo "<p>Du har ingen utlånte bøker.</p>";
    }
    echo "</table></div></div>";
  ?>

</body>
</html>
