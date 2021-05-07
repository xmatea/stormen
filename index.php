<!Doctype html>
<html>
<head>
  <?php
    $servername= 'localhost';
    $username='root';
    $password='';
    $dbname = 'main';

    $conn = mysqli_connect($servername, $username, $password);

    if (!$conn) {
      die("connection failed :(((");
    }

    mysqli_select_db($conn, $dbname);


  ?>
</head>
<body>
</body>

</html>
