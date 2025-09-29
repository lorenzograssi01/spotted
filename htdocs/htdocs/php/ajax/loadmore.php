<?php
    require_once "../database.php";
    require_once "../display.php";
    require_once "../common.php";
    
    session_start();
    if(!user_ok() || !isset($_GET["time"]))
      die();
    $time = $_GET['time'];

    $db = connect();
    $posts;
    $new_posts;
    if(!isset($_GET["type"]) || $_GET["type"] == 'std')
    {
      $posts = get_posts($_SESSION["name"], $db, 15 * $time - 3, 15 + 2 * 3);
      $new_posts = array();
    }
    elseif($_GET['type'] == 'comm')
    {
      $posts = get_community_posts($db, $_GET["community"], $_GET["maxid"], 0, 15);
      $new_posts = array();
    }
    elseif($_GET['type'] == 'user')
    {
      $posts = get_user_posts($_SESSION["name"], $db, $_GET["user"], $_SESSION["name"] == $_GET["user"], $_GET["maxid"], 0, 15);
      $new_posts = array();
    }

    $i = 0;
    while($row = $posts->fetch_assoc())
    {
        $new_posts[$i] = array();
        $new_posts[$i]["content"] = post_html($db, $row);
        $new_posts[$i]["id"] = $row['id'];
        $i++;
    }

    $return_arr = array("posts"=>$new_posts);

    echo json_encode($return_arr);

?>