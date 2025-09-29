<?php
    require_once "../database.php";
    require_once "../common.php";

    session_start();
    if(!user_ok() || !isset($_POST["appid"]) || !isset($_POST["type"]))
        die();

    $user = $_SESSION['name'];
    $appid = $_POST['appid'];
    $type = $_POST['type'];
    
    $db = connect();
    
    $ret = accept_cancel_applicant($user, $db, $appid, $type);

    echo $ret;
?>