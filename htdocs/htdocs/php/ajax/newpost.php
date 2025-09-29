<?php
    require_once "../database.php";
    require_once "../display.php";
    require_once "../common.php";
    
    session_start();
    if(!user_ok() || !isset($_POST["community"]) || !isset($_POST["content"])|| !isset($_POST["anon"]))
      die();

    $user = $_SESSION['name'];
    $anon = false;
    if($_POST["anon"] == "true")
        $anon = true;

    if(strlen($_POST["content"]) < 2)
    {
      echo "err0";
      exit();
    }

    $db = connect();

    $newid = post($db, $user, $_POST["content"], $_POST["community"], $anon);
    if($newid <= 0)
        die("Something went wrong ".$newid);
    $post = array(
        "id" => $newid,
        "anonym" => $anon,
        "content" => $_POST["content"],
        "creation_time" => date('Y-m-d H:i:s', time()),
        "username" => $user,
        "nlikes" => 0,
        "napplies" => 0,
        "ncomments" => 0,
        "community" => $_POST["community"],
        "open" => 1
    );
    echo post_html($db, $post);
?>