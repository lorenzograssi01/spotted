<?php
    require_once "../database.php";
    require_once "../common.php";
    require_once "../display.php";

    session_start();
    if(!user_ok() || !isset($_POST["type"]) || !isset($_POST["first_msg"]) || !isset($_POST["last_msg"]) || !isset($_POST["application"]))
        die();

    $type = $_POST['type'];
    $first_msg = $_POST["first_msg"];
    $last_msg = $_POST["last_msg"];
    $application = $_POST["application"];

    if($first_msg == "false")
        $first_msg = PHP_INT_MAX;

    if($last_msg == "false")
        $last_msg = 0;

    $database = connect();

    if($type == "send")
    {
        if(!isset($_POST["msg_text"]))
            die();
        $msg_text = $_POST["msg_text"];

        $textres = text_message($database, $application, $_SESSION["name"], $msg_text);
        if($textres < 0)
        {
            echo $textres;
            die();
        }
    }

    $newmessages = get_messages($database, $application, $_SESSION["name"], $last_msg, $first_msg);

    $newmessarray = array();
    $i = 0;
    while($row = $newmessages->fetch_assoc())
    {
        $newmessarray[$i] = message_html($row);
        $i++;
    }
    sign_as_read($_SESSION["name"], $database, $application);

    echo json_encode($newmessarray);
?>