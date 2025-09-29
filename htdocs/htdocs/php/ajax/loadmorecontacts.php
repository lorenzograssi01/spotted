<?php
    require_once "../database.php";
    require_once "../display.php";
    require_once "../common.php";
    
    session_start();
    if(!user_ok() || !isset($_GET["last_msg"]) || !isset($_GET["first_msg"]) || !isset($_GET["type"]))
        die();

    $last_msg = $_GET['last_msg'];
    $first_msg = $_GET['first_msg'];
    $type = $_GET['type'];
    if($type == "open")
        $open = true;
    else
        $open = false;
    
    if($last_msg == "false")
        $last_msg = PHP_INT_MAX;
    if($first_msg == "false")
        $last_msg = PHP_INT_MAX;

    $db = connect();

    if($open)
        $chats = get_chats($db, $_SESSION["name"], $last_msg, $first_msg);
    else
        $chats = get_matches($db, $_SESSION["name"], $last_msg, $first_msg);

    $chats_array = array();

    $i = 0;
    while($row = $chats->fetch_assoc())
    {
        $chats_array[$i] = chat_html($row, $open);
        $i++;
    }

    echo json_encode($chats_array);
?>