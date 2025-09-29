<?php
    require_once "../database.php";
    require_once "../display.php";
    require_once "../common.php";
    
    session_start();
    if(!user_ok() || !isset($_GET["postid"]) || !isset($_GET["lastapp"]))
        die();

    $postid = $_GET['postid'];
    $lastapp = $_GET['lastapp'];
    if($lastapp == "false")
        $lastapp = 0;

    $db = connect();

    $applicants = get_applicants($_SESSION["name"], $db, $postid, $lastapp, 0, 4);
    $newnapplies = new_n_applies($db, $postid, $lastapp);
    $applicants_array = array();

    $i = 0;
    while($row = $applicants->fetch_assoc())
    {
        $applicants_array[$i] = user_html($row, true, $row["accepted"]);
        $i++;
    }

    $return_arr = array("applicants"=>$applicants_array, "newnapplicants"=>$newnapplies);

    echo json_encode($return_arr);
?>