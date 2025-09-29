<?php
    require_once "../database.php";
    require_once "../common.php";

    session_start();
    if(!user_ok() || !isset($_POST["postid"]) || !isset($_POST["type"]))
        die();

    $user = $_SESSION['name'];
    $postid = $_POST['postid'];
    $type = $_POST['type'];
    
    $db = connect();
    $total_likes;
    if($type == "liked")
        $total_likes = unlike($db, $postid, $user);
    elseif($type == "unliked")
        $total_likes = like($db, $postid, $user);
    elseif($type == "applied")
        $total_likes = unapply($db, $postid, $user);
    else
        $total_likes = apply($db, $postid, $user);

    echo $total_likes;
?>