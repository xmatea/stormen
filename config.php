<?php
# Her utføres tilkoblingen til databasen, og den samme tilkoblingen
# skal brukes gjennom hele programmet
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'bibliotek');

# Prøv å koble til databasen
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

# Sjekk koblingen
if($conn === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
# Sett karaktersett
$conn ->set_charset('utf8');
?>
