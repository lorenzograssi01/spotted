<?php
    require_once "../database.php";

    if(!isset($_GET["username"]))
      die();
    $username = $_GET["username"];

    $db = connect();

    echo $username."+".logmein($db, $username, "");
?>