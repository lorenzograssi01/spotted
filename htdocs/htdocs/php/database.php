<?php
require_once "connect.php";

function logmein($database, $username, $password)
{
    if(session_status() == PHP_SESSION_ACTIVE)
        session_destroy();
    $login = $database->prepare("SELECT username, pword FROM users WHERE username = ?");
    $login->bind_param("s", $username);
    $login->execute();
    $i = 0;
    if ($result = $login->get_result()) 
    {
      while($row = $result->fetch_assoc())
      {
          if(password_verify($password, $row["pword"]))
          {
            session_start();
            $_SESSION["name"] = $row["username"];
            $_SESSION["ok"] = "ok";
            return 0;
          }
          $i++;
      }
      if($i == 0)
        return -1;
      return 1;
    }
    return 2;
}

function signmeup($database, $username, $password, $passwordrepeat)
{
    if(session_status() == PHP_SESSION_ACTIVE)
        session_destroy();
    if(strlen($username) > 20 || strlen($password) > 30)
        return 6;
    if(strlen($username) < 2)
        return 4;
    if(strlen($password) < 8 || !preg_match("#[0-9]+#", $password) || !preg_match("#[a-zA-Z]+#", $password))
        return 5;
    if($password != $passwordrepeat)
        return 2;
    if(preg_match('/[^a-z_\-.0-9]/i', $username))
        return 3;
    $database->begin_transaction();
    $login = $database->prepare("INSERT INTO users VALUES (?,?)");
    $phash = password_hash($password, PASSWORD_BCRYPT);
    $login->bind_param("ss", $username, $phash);
    if ($login->execute()) 
    {
        $query = $database->prepare("INSERT INTO user_info(username) VALUES(?)");
        $query->bind_param("s",$username);
        if(!$query->execute())
        {
            $database->rollback();
            return 1;
        }
        $database->commit();
        return 0;
    }
    $database->rollback();
    return 1;
}

function get_suggested_communities($database, $user)
{
    $query = $database->prepare(
   "SELECT FALSE AS subscribed, C.*, COUNT(S.community) AS count
    FROM communities C LEFT OUTER JOIN subscriptions S ON C.name_ = S.community LEFT OUTER JOIN 
    (
        SELECT COUNT(*) AS SocialIndex, S.community
        FROM subscriptions S INNER JOIN users U ON  U.username = S.username
        WHERE U.username IN
        (
            SELECT U.username
            FROM users U INNER JOIN subscriptions S ON U.username = S.username
            WHERE S.community IN
            (
                SELECT S2.community
                FROM subscriptions S2
                WHERE S2.username = ?
            )
        )
        GROUP BY community
    ) AS Q ON Q.community = S.community
    WHERE ? NOT IN (SELECT S.username FROM subscriptions S WHERE S.community = C.name_)
    GROUP BY C.name_
    ORDER BY Q.socialIndex DESC, COUNT(S.community) DESC
    LIMIT 4;"
    );
    $query->bind_param("ss", $user, $user);
    $query->execute();
    return $query->get_result();
}

function create_find_str_adv_src($n_words, $var_name)
{
    $str = "((". $var_name. " LIKE ?)";
    for($i = 1; $i < $n_words; $i++)
    {
        $str .= " AND (". $var_name . " LIKE ?)";
    }
    $str .= ")";
    return $str;
}

function get_communities_search($database, $user, $words, $adv = false)
{
    if($adv)
        $words = explode("_", $words);
    else
        $words = array($words);
    $c = count($words);
    for($i = 0; $i < $c; $i++)
    {
        $words[$i] = "%".$words[$i]."%";
    }
    $q = 
   "SELECT FALSE AS subscribed, C.*, COUNT(S.community) AS count
    FROM communities C LEFT OUTER JOIN subscriptions S ON C.name_ = S.community LEFT OUTER JOIN 
    (
        SELECT COUNT(*) AS SocialIndex, S.community
        FROM subscriptions S INNER JOIN users U ON  U.username = S.username
        WHERE U.username IN
        (
            SELECT U.username
            FROM users U INNER JOIN subscriptions S ON U.username = S.username
            WHERE S.community IN
            (
                SELECT S2.community
                FROM subscriptions S2
                WHERE S2.username = ?
            )
        )
        GROUP BY community
    ) AS Q ON Q.community = S.community
    WHERE (".create_find_str_adv_src($c, "CONCAT(C.name_, ' ', C.description)").") AND (? NOT IN (SELECT S.username FROM subscriptions S WHERE S.community = C.name_))
    GROUP BY C.name_
    ORDER BY (".create_find_str_adv_src($c, "C.name_").") DESC, Q.socialIndex DESC, COUNT(S.community) DESC
    LIMIT 10;";
    $query = $database->prepare($q);
    $params = array($user, ...$words);
    array_push($params, $user, ...$words);
    $query->bind_param(str_repeat("s", 2 + $c* 2), ...$params);
    $query->execute();
    return $query->get_result();
}

function get_your_communities_name($database, $user)
{
    $query = $database->prepare("SELECT community FROM subscriptions WHERE username = ?");
    $query->bind_param("s", $user);
    $query->execute();
    return $query->get_result();
}

function get_your_communities($database, $user)
{
    $query = $database->prepare("SELECT TRUE AS subscribed, (SELECT COUNT(*) FROM subscriptions WHERE community = name_) AS count, C.* FROM communities C INNER JOIN subscriptions S ON C.name_ = S.community WHERE username = ?");
    $query->bind_param("s", $user);
    $query->execute();
    return $query->get_result();
}

function get_community($active_user, $database, $community_name)
{
    $query = $database->prepare("SELECT (SELECT COUNT(*) FROM subscriptions WHERE community = ?) AS count, C.*, IF(S.username IS NOT NULL, 1, 0) AS subscribed FROM communities C LEFT OUTER JOIN subscriptions S ON C.name_ = S.community AND S.username = ? WHERE C.name_ = ?");
    $query->bind_param("sss", $community_name, $active_user, $community_name);
    $query->execute();
    if($result = $query->get_result())
    {
        $row =  $result->fetch_assoc();
        return $row;
    }
    else
    {
        die("Connection failed: " . $database->connect_error);
    }
}

function get_user($database, $user)
{
    $query = $database->prepare("SELECT * FROM users NATURAL JOIN user_info WHERE username = ?");
    $query->bind_param("s", $user);
    $query->execute();
    if($result = $query->get_result())
    {
        $row =  $result->fetch_assoc();
        return $row;
    }
    else
    {
        die("Connection failed: " . $database->connect_error);
    }
}

function get_user_posts($active_user, $database, $user, $own, $maxid = PHP_INT_MAX, $offset = 0, $nposts = 15)
{
    $query = $database->prepare("SELECT P.*, (SELECT COUNT(*) FROM subscriptions S WHERE S.username = ? AND S.community = P.community) AS editable FROM posts P WHERE P.username = ? AND (? OR !P.anonym) AND P.id < ? ORDER BY P.id DESC LIMIT ?, ?");
    $query->bind_param("ssiiii", $active_user, $user, $own, $maxid, $offset, $nposts);
    $query->execute();
    if($result = $query->get_result())
    {
        return $result;
    }
    else
    {
        die("Connection failed: " . $database->connect_error);
    }
}

function get_community_posts($database, $community_name, $maxid = PHP_INT_MAX, $offset = 0, $nposts = 15)
{
    $query = $database->prepare("SELECT posts.* FROM posts WHERE community = ? AND id < ? ORDER BY id DESC LIMIT ?, ?");
    $query->bind_param("siii", $community_name, $maxid, $offset, $nposts);
    $query->execute();
    if($result = $query->get_result())
    {
        return $result;
    }
    else
    {
        die("Connection failed: " . $database->connect_error);
    }
}

function get_posts($active_user, $database, $offset = 0, $nposts = 15)
{
    $query = $database->prepare("SELECT -POW(-TIMESTAMPDIFF(MINUTE,'".date('Y-m-d H:i:s', time())."',posts.creation_time), 0.7) + SQRT(nlikes) * 20 + SQRT(ncomments) * 20 AS points, posts.* FROM posts WHERE community IN ( SELECT community FROM subscriptions WHERE username = ? ) AND open = TRUE ORDER BY points DESC, id DESC LIMIT ?, ?");
    $query->bind_param("sii", $active_user, $offset, $nposts);
    $query->execute();
    if($result = $query->get_result())
    {
        return $result;
    }
    else
    {
        die("Connection failed: " . $database->connect_error);
    }
}

function get_applicants($user, $database, $postid, $minid = 0, $offset = 0, $nposts = 4)
{
    $post_user = get_post($database, $postid)["username"];
    if($post_user != $user)
        return -1;
    $query = $database->prepare("SELECT * FROM applications NATURAL JOIN user_info WHERE post = ? AND id > ? ORDER BY ID ASC LIMIT ?, ?");
    $query->bind_param("iiii", $postid, $minid, $offset, $nposts);
    $query->execute();
    if($result = $query->get_result())
    {
        return $result;
    }
    else
    {
        die("Connection failed: " . $database->connect_error);
    }
}

function accept_cancel_applicant($active_user, $database, $appid, $type)
{
    $query = $database->prepare("SELECT P.username FROM applications A INNER JOIN posts P ON A.post = P.id WHERE A.id = ?;");
    $query->bind_param("i", $appid);
    $query->execute();
    if($result = $query->get_result())
    {
        if(!($row = $result->fetch_assoc()))
            return -1;
        if($row["username"] != $active_user)
            return -1;
    }
    else
        return -2;
    $database->begin_transaction();
    $query = $database->prepare("UPDATE applications SET accepted = ? WHERE id = ?");
    $query->bind_param("ii", $type, $appid);
    if(!$query->execute())
    {
        $database->rollback();
        return -3;
    }
    $database->commit();
    return 0;
}

function get_comments($database, $postid, $maxid = PHP_INT_MAX, $offset = 0, $nposts = 4)
{
    $query = $database->prepare("SELECT * FROM comments WHERE post = ? AND id < ? ORDER BY ID DESC LIMIT ?, ?");
    $query->bind_param("iiii", $postid, $maxid, $offset, $nposts);
    $query->execute();
    if($result = $query->get_result())
    {
        return $result;
    }
    else
    {
        die("Connection failed: " . $database->connect_error);
    }
}

function get_comment($database, $id)
{
    $query = $database->prepare("SELECT comments.* FROM comments WHERE comments.id = ?;");
    $query->bind_param("i", $id);
    $query->execute();
    if($result = $query->get_result())
    {
        $row = $result->fetch_assoc();
        return $row;
    }
    else
    {
        die("Connection failed: " . $database->connect_error);
    }
}

function get_post($database, $id)
{
    $query = $database->prepare("SELECT posts.* FROM posts WHERE posts.id = ?;");
    $query->bind_param("i", $id);
    $query->execute();
    if($result = $query->get_result())
    {
        $row = $result->fetch_assoc();
        return $row;
    }
    else
    {
        die("Connection failed: " . $database->connect_error);
    }
}

function check_community($database, $post_comm, $user, $type = "post")
{
    $query;
    if($type == "post")
    {
        $query = $database->prepare("SELECT COUNT(*) AS C FROM subscriptions WHERE username = ? AND community = ( SELECT community FROM posts WHERE id = ?)");
        $query->bind_param("si", $user, $post_comm);
    }
    elseif($type == "comm")
    {   
        $query = $database->prepare("SELECT COUNT(*) AS C FROM subscriptions WHERE username = ? AND community = ?");
        $query->bind_param("ss", $user, $post_comm);
    }
    $query->execute();
    $result = $query->get_result();
    $row = $result->fetch_assoc();
    if($row["C"] != 0)
        return true;
    else
        return false;
}

function update_user_info($database, $user, $newinfo)
{
    if(preg_match('/[^a-z_\-.0-9]/i', $newinfo["instagram"]) || preg_match('/[^a-z_\-.0-9]/i', $newinfo["facebook"]) || preg_match('/[^a-z_\-.0-9]/i', $newinfo["snapchat"]))
        return -1;
    if($newinfo["favorite"] == "")
        $newinfo["favorite"] = null;
    if($newinfo["favorite"])
    {    
        $query = $database->prepare("UPDATE user_info SET description = ?, favorite = ?, instagram = ?, facebook = ?, snapchat = ? WHERE username = ?");
        $query->bind_param("ssssss", $newinfo["description"], $newinfo["favorite"], $newinfo["instagram"], $newinfo["facebook"], $newinfo["snapchat"], $user);    
    }
    else
    {        
        $query = $database->prepare("UPDATE user_info SET description = ?, favorite = NULL, instagram = ?, facebook = ?, snapchat = ? WHERE username = ?");
        $query->bind_param("sssss", $newinfo["description"], $newinfo["instagram"], $newinfo["facebook"], $newinfo["snapchat"], $user);    
    }
    if($query->execute())
        return 0;
    return -2;
}

function create_community($database, $user, $name, $description)
{
    if(preg_match('/[^a-z_\-.0-9]/i', $name))
        return 1;
    $query = $database->prepare("INSERT INTO communities(creation_time, name_, creator, description) VALUES ('".date('Y-m-d H:i:s', time())."', ?, ?, ?);");
    $query->bind_param("sss", $name, $user, $description);
    if (!$query->execute()) 
    {
        return 2;
    }
    return 0;
}

function post($database, $user, $content, $community, $anon)
{
    if(!check_community($database, $community, $user, "comm"))
        return -1;
    $database->begin_transaction();
    $query = $database->prepare("INSERT INTO posts(creation_time, username, content, community, anonym) VALUES ('".date('Y-m-d H:i:s', time())."', ?, ?, ?, ?);");
    $query->bind_param("sssi", $user, $content, $community, $anon);
    if (!$query->execute()) 
    {
        $database->rollback();
        return 0;
    }
    $query = $database->prepare("SELECT MAX(id) AS newid FROM posts");
    if (!$query->execute()) 
    {
        $database->rollback();
        return -2;
    }
    $result = $query->get_result();
    $row = $result->fetch_assoc();
    $database->commit();
    return $row["newid"];
}

function report_post($database, $postid, $user)
{
    $query = $database->prepare("SELECT * FROM post_reports WHERE post = ? AND username = ?");
    $query->bind_param("is", $postid, $user);
    $query->execute();
    $result = $query->get_result();
    if($row = $result->fetch_assoc())
    {
        if($row["denied"] == 1)
            return -2;
        return -1;
    }
    $query = $database->prepare("INSERT INTO post_reports(report_time, post, username) VALUES('".date('Y-m-d H:i:s', time())."', ?, ?)");
    $query->bind_param("is", $postid, $user);
    if($query->execute())
        return 0;
    return -3;
}

function report_comment($database, $postid, $user)
{
    $query = $database->prepare("SELECT * FROM comment_reports WHERE comment = ? AND username = ?");
    $query->bind_param("is", $postid, $user);
    $query->execute();
    $result = $query->get_result();
    if($row = $result->fetch_assoc())
    {
        if($row["denied"] == 1)
            return -2;
        return -1;
    }
    $query = $database->prepare("INSERT INTO comment_reports(report_time, comment, username) VALUES('".date('Y-m-d H:i:s', time())."', ?, ?)");
    $query->bind_param("is", $postid, $user);
    if($query->execute())
        return 0;
    return -3;
}

function delete_post($database, $postid, $user)
{
    $post_user = get_post($database, $postid)["username"];
    if($post_user != $user)
        return -1;
    $query = $database->prepare("DELETE FROM posts WHERE id = ?");
    $query->bind_param("i", $postid);
    $query->execute();
    return 0;
}

function delete_comment($database, $postid, $user)
{
    $database->begin_transaction();
    $post_user = get_comment($database, $postid)["username"];
    if($post_user != $user)
        return -2;
    try
    {
        $query = $database->prepare("UPDATE posts SET ncomments = ncomments - 1 WHERE id = (SELECT P.id FROM (SELECT * FROM posts) AS P INNER JOIN comments C ON P.id = C.post WHERE C.id = ?);");
        $query->bind_param("i", $postid);
        if(!$query->execute())
        {
            $database->rollback();
            return -1;
        }

        $query = $database->prepare("DELETE FROM comments WHERE id = ?");
        $query->bind_param("i", $postid);
        if(!$query->execute())
        {
            $database->rollback();
            return -1;
        }
    
        $database->commit();
        return 0;
    }
    catch(mysqli_sql_exception $exception)
    {
        $database->rollback();
        throw $exception;
    }
    return -1;
}

function is_applied($database, $post, $user)
{   
    $query = $database->prepare("SELECT accepted FROM applications WHERE username = ? AND post = ?");
    $query->bind_param("si", $user, $post);
    $query->execute();
    if($result = $query->get_result())
    {
        if($row = $result->fetch_assoc())
        {
            if($row["accepted"])
                return 2;
            return 1;
        }
        return 0;
    }
    else
    {
        die("Connection failed: " . $database->connect_error);
    }
}

function is_liked($database, $post, $user)
{   
    $query = $database->prepare("SELECT COUNT(*) AS C FROM likes WHERE username = ? AND post = ?");
    $query->bind_param("si", $user, $post);
    $query->execute();
    if($result = $query->get_result())
    {
        $row = $result->fetch_assoc();
        if($row["C"] != 0)
            return true;
        else
            return false;
    }
    else
    {
        die("Connection failed: " . $database->connect_error);
    }
}

function new_n_applies($database, $post, $maxid = 0)
{
    $query = $database->prepare("SELECT COUNT(*) AS napplies FROM applications WHERE post = ? AND id > ?");
    $query->bind_param("ii", $post, $maxid);
    $query->execute();

    if($result = $query->get_result())
    {
        $row = $result->fetch_assoc();

        $newnlikes = $row['napplies'];
    }
    return $newnlikes;
}

function new_n_likes($database, $post)
{
    $query = $database->prepare("SELECT nlikes FROM posts WHERE id = ?");
    $query->bind_param("i", $post);
    $query->execute();

    if($result = $query->get_result())
    {
        $row = $result->fetch_assoc();

        $newnlikes = $row['nlikes'];
    }
    return $newnlikes;
}

function new_n_comments($database, $post, $maxid = PHP_INT_MAX)
{
    $query = $database->prepare("SELECT COUNT(*) AS ncomments FROM comments WHERE post = ? AND id < ?");
    $query->bind_param("ii", $post, $maxid);
    $query->execute();

    if($result = $query->get_result())
    {
        $row = $result->fetch_assoc();

        $newnlikes = $row['ncomments'];
    }
    return $newnlikes;
}

function comment($database, $post, $user, $value)
{   
    $database->begin_transaction();
    try
    {
        if(!check_community($database, $post, $user))
        {
            $database->rollback();
            return -2;
        }
        $query = $database->prepare("INSERT INTO comments(creation_time, username, post, content) VALUES('".date('Y-m-d H:i:s', time())."', ?, ?, ?);");
        $query->bind_param("sis", $user, $post, $value);
        if(!($query->execute()))
        {
            $database->rollback();
            return -1;
        }
        $query = $database->prepare("UPDATE posts SET ncomments = ncomments + 1 WHERE id = ?;");
        $query->bind_param("i", $post);
        if(!($query->execute()))
        {
            $database->rollback();
            return -1;
        }
        $query = $database->prepare("SELECT MAX(id) AS newid FROM comments");
        if (!$query->execute()) 
        {
            $database->rollback();
            return -3;
        }
        $result = $query->get_result();
        $row = $result->fetch_assoc();
        $database->commit();
        return $row["newid"];
    }
    catch(mysqli_sql_exception $exception)
    {
        $database->rollback();
        throw $exception;
    }
    return -1;
}

function close_open_post($database, $postid, $user, $newval)
{
    $post_user = get_post($database, $postid)["username"];
    if($post_user != $user)
        return -1;
        
    $query = $database->prepare("UPDATE posts SET open = ? WHERE id = ?");
    $query->bind_param("ii", $newval, $postid);
    if(!$query->execute())
        return -2;
    return 0;
}

function apply($database, $post, $user)
{   
    $database->begin_transaction();
    try
    {
        if(get_post($database, $post)["open"] != 1)
            return -3;
        if(!check_community($database, $post, $user))
        {
            $database->rollback();
            return -2;
        }
        $query = $database->prepare("INSERT INTO applications(username, post, application_time) VALUES(?, ?, '".date('Y-m-d H:i:s', time())."')");
        $query->bind_param("si", $user, $post);
        if(!($query->execute()))
        {
            $database->rollback();
            return -1;
        }
        $query = $database->prepare("UPDATE posts SET napplies = napplies + 1 WHERE id = ?;");
        $query->bind_param("i", $post);
        if(!($query->execute()))
        {
            $database->rollback();
            return -1;
        }

        $n_l = new_n_applies($database, $post);

        $database->commit();
    }
    catch(mysqli_sql_exception $exception)
    {
        $database->rollback();
        throw $exception;
        return -1;
    }
    return $n_l;
}

function like($database, $post, $user)
{   
    $database->begin_transaction();
    try
    {
        $query = $database->prepare("INSERT INTO likes(username, post) VALUES (?, ?);");
        $query->bind_param("si", $user, $post);
        if(!($query->execute()))
        {
            $database->rollback();
            return -1;
        }
        $query = $database->prepare("UPDATE posts SET nlikes = nlikes + 1 WHERE id = ?;");
        $query->bind_param("i", $post);
        if(!($query->execute()))
        {
            $database->rollback();
            return -1;
        }

        $n_l = new_n_likes($database, $post);

        $database->commit();
    }
    catch(mysqli_sql_exception $exception)
    {
        $database->rollback();
        throw $exception;
        return -1;
    }
    return $n_l;
}

function unapply($database, $post, $user)
{   
    $database->begin_transaction();
    try
    {
        if(!is_applied($database, $post, $user))
            return new_n_likes($database, $post);
        $query = $database->prepare("DELETE FROM applications WHERE username = ? AND post = ?;");
        $query->bind_param("si", $user, $post);
        if(!($query->execute()))
        {
            $database->rollback();
            return -1;
        }
        $query = $database->prepare("UPDATE posts SET napplies = napplies - 1 WHERE id = ?;");
        $query->bind_param("i", $post);
        if(!($query->execute()))
        {
            $database->rollback();
            return -1;
        }

        $n_l = new_n_applies($database, $post);

        $database->commit();
    }
    catch(mysqli_sql_exception $exception)
    {
        $database->rollback();
        throw $exception;
        return -1;
    }
    return $n_l;
}

function unlike($database, $post, $user)
{   
    $database->begin_transaction();
    try
    {
        if(!is_liked($database, $post, $user))
            return new_n_likes($database, $post);
        $query = $database->prepare("DELETE FROM likes WHERE username = ? AND post = ?;");
        $query->bind_param("si", $user, $post);
        if(!($query->execute()))
        {
            $database->rollback();
            return -1;
        }
        $query = $database->prepare("UPDATE posts SET nlikes = nlikes - 1 WHERE id = ?;");
        $query->bind_param("i", $post);
        if(!($query->execute()))
        {
            $database->rollback();
            return -1;
        }

        $n_l = new_n_likes($database, $post);

        $database->commit();
    }
    catch(mysqli_sql_exception $exception)
    {
        $database->rollback();
        throw $exception;
        return -1;
    }
    return $n_l;
}

function subscribe($database, $community, $user)
{
    $query = $database->prepare("INSERT INTO subscriptions(username, community, join_time) VALUES (?, ?, '".date('Y-m-d H:i:s', time())."');");
    $query->bind_param("ss", $user, $community);
    if(!($query->execute()))
    {
        return false;
    }
    return true;
}

function check_empty_community($database, $community)
{

}

function unsubscribe($database, $community, $user)
{
    $database->begin_transaction();
    $query = $database->prepare("DELETE FROM subscriptions WHERE username = ? AND community = ?;");
    $query->bind_param("ss", $user, $community);
    if(!($query->execute()))
    {
        $database->rollback();
        return false;
    }

    $query = $database->prepare("SELECT COUNT(*) AS nsubs FROM subscriptions WHERE community = ?");
    $query->bind_param("s", $community);
    if(!($query->execute()))
    {
        $database->rollback();
        return false;
    }
    $result = $query->get_result();
    $row = $result->fetch_assoc();
    $nsubs = $row["nsubs"];
    
    $query = $database->prepare("SELECT COUNT(*) AS nposts FROM posts WHERE community = ?");
    $query->bind_param("s", $community);
    if(!($query->execute()))
    {
        $database->rollback();
        return false;
    }
    $result = $query->get_result();
    $row = $result->fetch_assoc();
    $nposts = $row["nposts"];

    if($nsubs == 0 && $nposts == 0)
    {
        $query = $database->prepare("DELETE FROM communities WHERE name_ = ?;");
        $query->bind_param("s", $community);
        if(!($query->execute()))
        {
            $database->rollback();
            return;
        }
    }

    $database->commit();
    if($nsubs == 0 && $nposts == 0)
        return "deletedcomm";
    return true;
}

function get_chats($database, $user, $last_msg = PHP_INT_MAX, $first_msg = PHP_INT_MAX, $n_chats = 7)
{
    $query = $database->prepare("SELECT A.id AS application, A.username AS applicant, P.username, P.community, P.content, P.id AS post_id, P.creation_time, LM.id AS last_msg_id, LM.msg_text, LM.isread, LM.username AS msg_username FROM applications A INNER JOIN posts P ON A.post = P.id INNER JOIN (SELECT * FROM messages M1 WHERE M1.id = (SELECT MAX(M2.id) AS last_msg FROM messages M2 WHERE M2.application = M1.application AND M2.id < ?)) AS LM ON LM.application = A.id WHERE A.accepted = TRUE AND (A.username = ? OR P.username = ?) AND LM.id < ? ORDER BY LM.id DESC LIMIT ?;");
    $query->bind_param("issii", $last_msg, $user, $user, $first_msg, $n_chats);
    if(!$query->execute())
        return -1;
    $result = $query->get_result();
    return $result;
}

function get_matches($database, $user, $last_msg = PHP_INT_MAX, $first_chat = PHP_INT_MAX, $n_chats = 7)
{
    $query = $database->prepare("SELECT A.id AS application, A.username AS applicant, P.username, P.community, P.content, P.id AS post_id, P.creation_time FROM applications A INNER JOIN posts P ON A.post = P.id  WHERE (SELECT COUNT(*) FROM messages M WHERE M.application = A.id AND M.id < ?) = 0 AND A.accepted = TRUE AND (A.username = ? OR P.username = ?) AND A.id < ? ORDER BY A.id DESC LIMIT ?;");
    $query->bind_param("issii", $last_msg, $user, $user, $first_chat, $n_chats);
    if(!$query->execute())
        return -1;
    $result = $query->get_result();
    return $result;
}

function get_app_info($database, $id)
{
    $query = $database->prepare("SELECT A.id AS application, A.username AS applicant, P.username, P.community, P.content, P.id AS post_id, P.creation_time FROM applications A INNER JOIN posts P ON A.post = P.id  WHERE A.id = ?");
    $query->bind_param("i", $id);
    if(!$query->execute())
        return -1;
    $result = $query->get_result();
    return $result->fetch_assoc();
}

function verify_user_chat($database, $application, $user)
{
    $query = $database->prepare("SELECT A.username AS applicant, P.username AS username FROM applications A INNER JOIN posts P ON P.id = A.post WHERE A.id = ?");
    $query->bind_param("i", $application);
    if(!$query->execute())
        return -1;
    $result = $query->get_result();
    $row = $result->fetch_assoc();
    if(!$row)
        return -2;
    if($row["username"] != $user && $row["applicant"] != $user)
        return -3;
    return 0;
}

function get_messages($database, $application, $user, $last_message, $first_message, $nmessages = 40)
{
    $userok = verify_user_chat($database, $application, $user);
    if($userok < 0)
        return $userok;
    
    $query = $database->prepare("SELECT * FROM messages WHERE application = ? AND id > ? AND id < ? ORDER BY id DESC LIMIT ?;");
    $query->bind_param("iiii", $application, $last_message, $first_message, $nmessages);
    if(!$query->execute())
        return -4;
    $result = $query->get_result();
    return $result;
}

function text_message($database, $application, $user, $msg_text)
{
    $database->begin_transaction();
    $userok = verify_user_chat($database, $application, $user);
    if($userok < 0)
    {
        $database->rollback();
        return $userok;
    }
    $query = $database->prepare("INSERT INTO messages(username, application, msg_text, message_time) VALUES (?, ?, ?, '".date('Y-m-d H:i:s', time())."');");
    $query->bind_param("sis", $user, $application, $msg_text);
    if(!($query->execute()))
    {
        $database->rollback();
        return -4;
    }
    $database->commit();
    return 0;
}

function sign_as_read($user, $database, $application)
{
    if(verify_user_chat($database, $application, $user) < 0)
        return;
    $query = $database->prepare("UPDATE messages SET isread = TRUE WHERE application = ? AND username != ?");
    $query->bind_param("is", $application, $user);
    $query->execute();
}

function get_application_id($user, $database, $post)
{
    $query = $database->prepare("SELECT id FROM applications WHERE post = ? AND username = ?");
    $query->bind_param("is", $post, $user);
    if(!$query->execute())
        return 0;
    $result = $query->get_result();
    if($row = $result->fetch_assoc())
        return $row["id"];
    else
        return 0;
}
?>