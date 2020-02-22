<?php
include("config.php");
include("lib/db.php");


session_start();

session_unset();
session_destroy();

header("Location: /"); 
exit();
?>
