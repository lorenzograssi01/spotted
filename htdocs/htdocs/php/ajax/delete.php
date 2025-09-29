<?php
    require_once "../database.php";
    require_once "../common.php";

    session_start();
    if(!user_ok() || !isset($_POST["type"]) || !isset($_POST["id"]))
        die();

    $user = $_SESSION['name'];
    $type = $_POST['type'];
    $id = $_POST['id'];
    
    $db = connect();
    $ret;
    if($type == "post")
        $ret = delete_post($db, $id, $user);
    else
        $ret = delete_comment($db, $id, $user);
    
    echo $ret;
?>