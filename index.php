<!Doctype html>
<html>
<head>
  <link href="style.css" type="text/css" rel="stylesheet">
</head>
<body>

  <h1 >STORMEN BIBLIOTEK</h1>
  <form autocomplete="off" method="POST" id="søkeskjema">
    <input autocomplete="off" name="hidden" type="text" style="display:none;">
    <input type="text" name="sql" value="Søk på tittel" id="søkefelt">
    <input type="submit" value="GO" id="søkeknapp">
  </form>
  <?php
    $servername= 'localhost';
    $username='root';
    $password='';
    $dbname = 'bibliotek';

    $conn = mysqli_connect($servername, $username, $password);
    $conn ->set_charset('utf8');

    if (!$conn) {
      die("connection failed :(((");
    }

    mysqli_select_db($conn, $dbname);
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

    if ($_POST['sql']) {
      $sql_i = $sql." WHERE bok.tittel LIKE '%".$_POST['sql']."%'";
      if($conn->query($sql_i)) {
        $sql = $sql_i;
      }
    }

    echo("<h4>Søker etter: ".$_POST['sql']."</h4>");

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
