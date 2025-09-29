<?php
    require_once "../database.php";
    require_once "../display.php";
    require_once "../common.php";
    
    session_start();
    if(!user_ok() || !isset($_GET["postid"]) || !isset($_GET["lastcomment"]))
        die();

    $postid = $_GET['postid'];
    $lastcomment = $_GET['lastcomment'];
    if($lastcomment == "false")
        $lastcomment = PHP_INT_MAX;


    $db = connect();

    $posts = get_comments($db, $postid, $lastcomment, 0, 4);
    $newncomments = new_n_comments($db, $postid, $lastcomment);
    $comments = array();

    $i = 0;
    while($row = $posts->fetch_assoc())
    {
        $comments[$i] = comment_html($row);
        $i++;
    }

    $return_arr = array("comments"=>$comments, "newncomments"=>$newncomments);

    echo json_encode($return_arr);

?>