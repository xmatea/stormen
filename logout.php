<?php
# START SESSION
session_start();

# SLETT ALLE SESSION-VARIABLER
$_SESSION = array();
session_destroy();

header("location: personlig/login.php");
?>
