<?php
    require_once "../database.php";
    require_once "../common.php";

    session_start();
    if(!user_ok() || !isset($_POST["community"]) || !isset($_POST["type"]))
        die();

    $user = $_SESSION['name'];
    $comm = $_POST['community'];
    $type = $_POST['type'];

    $db = connect();
    $res;
    if($type == "unsubscribe")
        $res = unsubscribe($db, $comm, $user);
    else
        $res = subscribe($db, $comm, $user);

    echo $res;
?>