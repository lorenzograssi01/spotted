<?php
    require_once "../database.php";
    require_once "../display.php";
    require_once "../common.php";
    
    session_start();
    if(!user_ok() || !isset($_POST["postid"]) || !isset($_POST["comment"]))
      die();

    $user = $_SESSION['name'];
    $postid = $_POST['postid'];
    $comment = $_POST['comment'];
    if(strlen($comment) < 1)
      exit();

    $db = connect();

    $new_id = comment($db, $postid, $user, $comment);
    if($new_id < 0)
    {
      echo "-1";
      return;
    }

    $new_comment = comment_struct($user, $comment, "own_".$new_id, date('Y-m-d h:i:s a', time()));

    $return_arr = array("comments"=>$new_comment);

    echo json_encode($return_arr);
?>