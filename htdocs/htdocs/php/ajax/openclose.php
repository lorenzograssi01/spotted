<?php
    require_once "../database.php";
    require_once "../common.php";

    session_start();
    if(!user_ok() || !isset($_POST["postid"]) || !isset($_POST["newval"]))
        die();

    $postid = $_POST['postid'];
    $newval = $_POST['newval'];

    $db = connect();
    
    echo close_open_post($db, $postid, $_SESSION["name"], $newval);
?>