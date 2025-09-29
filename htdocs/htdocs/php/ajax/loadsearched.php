<?php
    require_once "../database.php";
    require_once "../display.php";
    require_once "../common.php";

    session_start();
    if(!user_ok() || !isset($_GET["str"]) || !isset($_GET["words"]))
      die();
    $str = $_GET['str'];
    $words = $_GET['words'];

    $db = connect();

    if(communities($db, $_SESSION["name"], "search", $str) == 0 && strlen($words) != 0 && explode("_", $words)[0] != $str)
      communities($db, $_SESSION["name"], "adv_search", $words);
?>