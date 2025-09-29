<?php
    require_once "database.php";
    function sub_icon()
    {
        return "<svg class='subicon' width=40px height= 40px viewBox='-2 -2 28 28'><path d='M20.822 18.096c-3.439-.794-6.64-1.49-5.09-4.418 4.72-8.912 1.251-13.678-3.732-13.678-5.082 0-8.464 4.949-3.732 13.678 1.597 2.945-1.725 3.641-5.09 4.418-3.073.71-3.188 2.236-3.178 4.904l.004 1h23.99l.004-.969c.012-2.688-.092-4.222-3.176-4.935z'></path></svg>";
    }
    function dropdown_menu($options, $attributename = "option", $before = "", $id = "", $class = "")
    {
        echo "<select id = '".$id."'";
        if($class != "")
        echo "class = '".$class."'";
        echo "><option label = 'COMMUNITY' value = '' selected disabled hidden></option>";
        while($row = $options->fetch_assoc())
        {
            $elem = $row[$attributename];
            echo "<option value = '".$elem."'>".$before.$elem."</option>";
        }
        echo "</select>";
    }
    function communities($database, $user, $type, $str = "")
    {
        $comm;
        if($type == "yours")
            $comm = get_your_communities($database, $user);
        elseif($type == "sugg")
            $comm = get_suggested_communities($database, $user);
        elseif($type == "search")
            $comm = get_communities_search($database, $user, $str);
        else//($type == "adv_search")
            $comm = get_communities_search($database, $user, $str, true);
        $i = 0;
        while($row = $comm->fetch_assoc())
        {
            display_community($_SESSION["name"], $database, $row);
            $i++;
        }
        return $i;
    }
    function display_community($active_user, $database, $community)
    {
        echo "<div ";
        if($community["subscribed"])
            echo "class= 'community community_subscribed'";
        else
            echo "class= 'community community_unsubscribed'";
        echo " id = 'community+".$community['name_']."'><div class = 'community_title'><a href='./community.php?community=".$community["name_"]."'>&#64;".$community['name_']."</a></div><div class = 'community_desc'>".nl2br(htmlspecialchars($community['description']))."</div>";
        echo "<div class = 'subbar'><div class='nsubs'>";
        echo "<span class = 'subcount' id = 'subs_count+".$community['name_']."'>".$community["count"]."</span>";
        echo sub_icon(); 
        echo "</div>";
        if(!$community["subscribed"])
            echo "<button id='subscribe_button+".$community['name_']."' class='subbutton subscribe'>SUBSCRIBE</button>";
        else
            echo "<button id='unsubscribe_button+".$community['name_']."' class='subbutton unsubscribe'>UNSUBSCRIBE</button>";
        echo "</div></div>";
    }
    function social_names($social, $username)
    {
        if($social == "instagram")
            return "Instagram: &#64;".$username;
        if($social == "facebook")
            return "Facebook: &#64;".$username;
        if($social == "snapchat")
            return "Snapchat: &#64;".$username;
    }
    function social_links($social, $username)
    {
        if($social == "instagram")
            return "https://www.instagram.com/".$username;
        if($social == "facebook")
            return "https://www.facebook.com/".$username;
        if($social == "snapchat")
            return "https://www.snapchat.com/add/".$username;
    }
    function display_user($user)
    {
        echo user_html($user);
    }
    function user_html($user, $showid = false, $buttons = -1)
    {
        $id = "user+".$user["username"];
        if($showid)
            $id .= "+".$user["id"];
        if($buttons != 1)
            $userHTML = "<div class='user' id='".$id."'>";
        else
            $userHTML = "<div class='user useraccepted' id='".$id."'>";
        $userHTML .= "<div class='userbody'>";
        $userHTML .= "<div class='user_username'><a href = './user.php?user=".$user["username"]."'>".$user["username"]."</a></div>";
        $userHTML .= "<div class='user_description'>".nl2br(htmlspecialchars($user["description"]))."</div>";
        $userHTML .= "<div class='user_contacts'>";
        if($user["favorite"] && $user[$user["favorite"]])
            $userHTML .= "<div class='favorite_contact'><a target='_blank' href = '".social_links($user["favorite"], $user[$user["favorite"]])."'>".social_names($user["favorite"], $user[$user["favorite"]])."</a></div>";
        else
            $userHTML .= "<div class='favorite_contact'>No contacts</div>";
        if($user["instagram"] && $user["favorite"] != "instagram")
            $userHTML .= "<div class='other_contact'><a target='_blank' href = '".social_links("instagram", $user["instagram"])."'>".social_names("instagram", $user["instagram"])."</a></div>";
        if($user["facebook"] && $user["favorite"] != "facebook")
            $userHTML .= "<div class='other_contact'><a target='_blank' href = '".social_links("facebook", $user["facebook"])."'>".social_names("facebook", $user["facebook"])."</a></div>";
        if($user["snapchat"] && $user["favorite"] != "snapchat")
            $userHTML .= "<div class='other_contact'><a target='_blank' href = '".social_links("snapchat", $user["snapchat"])."'>".social_names("snapchat", $user["snapchat"])."</a></div>"; 
        $userHTML .= "</div>";
        $userHTML .= "</div>";
        $userHTML .= "<div class='userbuttons'>";
        if($buttons == 0)
        {
            $userHTML .= "<button id = 'accept+".$id."'>ACCEPT</button>";
        }
        elseif($buttons == 1)
        {
            $userHTML .= "<button id = 'chat+".$id."'>CHAT</button>";
            $userHTML .= "<button id = 'cancel+".$id."'>UNACCEPT</button>";
        }
        elseif($buttons == -1 && $user["username"] == $_SESSION["name"])
        {
            $userHTML .= "<button id = 'edit+".$id."'>EDIT PROFILE</button>";
        }
        $userHTML .= "</div>";
        $userHTML .= "</div>";
        return $userHTML;
    }
    function display_user_posts($database, $user)
    {
        $posts = get_user_posts($_SESSION["name"], $database, $user, $_SESSION["name"] == $user);
        while($row = $posts->fetch_assoc())
        {
            display_post($database, $row, $row["editable"]);
        }
    }
    function display_community_posts($database, $community)
    {
        $posts = get_community_posts($database, $community['name_']);
        while($row = $posts->fetch_assoc())
        {
            display_post($database, $row, $community["subscribed"]);
        }
    }
    function display_posts($active_user, $database, $offset = 0, $nposts = 15)
    {
        $posts = get_posts($active_user, $database, $offset, $nposts);
        while($row = $posts->fetch_assoc())
        {
            display_post($database, $row);
        }
    }
    function display_post($database, $post, $editable = true)
    {
        echo post_html($database, $post, $editable);
    }
    function time_elapsed($date)
    {
        $interval = strtotime(date('Y-m-d H:i:s', time())) - strtotime($date);
        if($interval < 60)
            return $interval." sec";
        $interval = floor($interval / 60);
        if($interval < 60)
            return $interval." min";
        $interval = floor($interval / 60);
        if($interval < 24)
            return $interval." h";
        $interval = floor($interval / 24);
        if($interval < 31)
            return $interval." d";
        $interval = floor($interval / 30.436875);
        if($interval < 12)
            return $interval." mo";
        $interval = floor($interval / 12);
            return $interval." y";
    }
    function like_icon()
    {
        return "<svg class='likeicon' width='40px' height='40px' viewBox='-2 -3 28 28'><path d='M12 4.435c-1.989-5.399-12-4.597-12 3.568 0 4.068 3.06 9.481 12 14.997 8.94-5.516 12-10.929 12-14.997 0-8.118-10-8.999-12-3.568z'></path></svg>";
    }
    function comm_icon()
    {
        return "<svg class='commicon' width=40px height= 40px viewBox='-2 -3 28 28'><path d='M12 1c-6.628 0-12 4.573-12 10.213 0 2.39.932 4.591 2.427 6.164l-2.427 5.623 7.563-2.26c9.495 2.598 16.437-3.251 16.437-9.527 0-5.64-5.372-10.213-12-10.213z'></path></svg>";
    }
    function apply_icon()
    {
        return "<svg class='appicon' width=40px height= 40px viewBox='-2 -2 28 28'><path d='M12 .587l3.668 7.568 8.332 1.151-6.064 5.828 1.48 8.279-7.416-3.967-7.417 3.967 1.481-8.279-6.064-5.828 8.332-1.151z'></path></svg>";
    }

    function dotdotdot($type)
    {
        return "<svg class='dotdotdot_".$type."' width=40px height= 40px viewBox='-2 -5 28 28'><path d='m12 16.495c1.242 0 2.25 1.008 2.25 2.25s-1.008 2.25-2.25 2.25-2.25-1.008-2.25-2.25 1.008-2.25 2.25-2.25zm0-6.75c1.242 0 2.25 1.008 2.25 2.25s-1.008 2.25-2.25 2.25-2.25-1.008-2.25-2.25 1.008-2.25 2.25-2.25zm0-6.75c1.242 0 2.25 1.008 2.25 2.25s-1.008 2.25-2.25 2.25-2.25-1.008-2.25-2.25 1.008-2.25 2.25-2.25z'></path></svg>";
    }
    
    function post_html($database, $post, $editable = true)
    {
        if($post["open"])
            $postHTML = "<div class='post' id = 'post_".$post['id']."'>";
        else
            $postHTML = "<div class='post post_close' id = 'post_".$post['id']."'>";
        $postHTML .= "<div class='titlebar'>";
        if($_SESSION["name"] == $post["username"])
            $title_own_class = "title_own";
        else
            $title_own_class = "title_ext";
        if($post['anonym'] == 0)
            $postHTML .= "<p class='title username ".$title_own_class."'><a href='./user.php?user=".$post["username"]."'>".$post['username']."</a></p>";
        else
            $postHTML .= "<p class='title anonym ".$title_own_class."'>"."Anonymous"."</p>";
        $postHTML .= "<p class='title post_community'><a href = './community.php?community=".$post['community']."'>&#64;".$post['community']."</a></p>";
        $postHTML .= "<p class = 'title date'><time datetime = '".$post["creation_time"]."'>".time_elapsed($post["creation_time"])."</time></p>";
        if($_SESSION["name"] == $post["username"])
            $postHTML .= "<div class = 'dotdotdot_wrap' id = 'dotdotdot_".$post['id']."_own_".$post["open"]."'>".dotdotdot("post")."</div></div>";
        else
            $postHTML .= "<div class = 'dotdotdot_wrap' id = 'dotdotdot_".$post['id']."_ext_".$post['open']."'>".dotdotdot("post")."</div></div>";
        $postHTML .= "<p class='postcontent'>".nl2br(htmlspecialchars($post['content']))."</p>";
        if($editable)
            $postHTML .= "<div class = 'postbar' id = 'postbar_".$post['id']."'>";
        else
            $postHTML .= "<div class = 'postbar' id = 'unedit_postbar_".$post['id']."'>";
        if(is_liked($database, $post['id'], $_SESSION['name']))
            $postHTML .= "<div class='likes liked' id='liked_".$post['id']."'>";
        else
            $postHTML .= "<div class='likes unliked' id='unliked_".$post['id']."'>";
        $postHTML .= "<span class='likecount'>".$post['nlikes']."</span>".like_icon()."</div>";
        $postHTML .= "<div class='ncomments showcomments' id='showcomments_".$post['id']."_0'><span class='commcount'>".$post['ncomments']."</span>".comm_icon()."</div>";
        if($_SESSION["name"] == $post["username"])
            $postHTML .= "<div class='apply_button show_applicants' id='showapp_".$post['id']."'>";
        else
        {
            $app_status = is_applied($database, $post['id'], $_SESSION['name']);
            if($app_status == 2)
                $postHTML .= "<div class='apply_button applied accepted_app' id='acceptedapp_".$post['id']."_".get_application_id($_SESSION["name"], $database, $post['id'])."'>";
            elseif($app_status == 1)
                $postHTML .= "<div class='apply_button applied pending_app' id='applied_".$post['id']."'>";
            elseif($app_status == 0)
                $postHTML .= "<div class='apply_button unapplied' id='unapplied_".$post['id']."'>";
        }
        $postHTML .= "<span class='applycount'>".$post["napplies"]."</span>".apply_icon()."</div>";
        $postHTML .= "</div><div class='comments' id='comments_".$post['id']."'><div id = 'addcommentcontainer_".$post['id']."'></div><div id='comments_div_".$post['id']."'></div><div id = 'loadmore_comments_containet_".$post['id']."'></div></div>";
        $postHTML .= "<div class='applicants' id='applicants_".$post['id']."'><div id='applicants_div_".$post['id']."'></div><div id = 'loadmore_app_containet_".$post['id']."'></div></div></div>";
        return $postHTML;
    }
    function comment_struct($username, $content, $id, $creation_date)
    {
        if($_SESSION["name"] == $username)
            $title_own_class = "title_own";
        else
            $title_own_class = "title_ext";

        $postHTML = "<div class='comment' id = 'comment_".$id."'>";
        $postHTML .= "<div class = 'titlebar'><p class='commenttitle username ".$title_own_class."'><a href='./user.php?user=".$username."'>".$username."</a></p><p class = 'commenttitle date'><time datetime = '".$creation_date."'>".time_elapsed($creation_date)."</time></p>";
        if($_SESSION["name"] == $username)
            $postHTML .= "<div class = 'dotdotdot_wrap_comm' id = 'dotdotdot_".$id."_own'>".dotdotdot("comment");
        else
            $postHTML .= "<div class = 'dotdotdot_wrap_comm' id = 'dotdotdot_".$id."_ext'>".dotdotdot("comment");
        $postHTML .= "</div></div><p class='commentcontent'>".nl2br(htmlspecialchars($content))."</p>";
        $postHTML .= "</div>";
        return $postHTML;
    }
    function comment_html($post)
    {
        return comment_struct($post['username'], $post['content'], $post['id'], $post['creation_time']);
    }
    function display_chats($database, $open)
    {
        if($open)
            $chats = get_chats($database, $_SESSION["name"]);
        else
            $chats = get_matches($database, $_SESSION["name"]);
        while($row = $chats->fetch_assoc())
        {
            display_chat($row, $open);
        }
    }
    function display_chat($row, $open)
    {
        echo chat_html($row, $open);
    }
    function chat_html($row, $open)
    {
        $chatHTML = "<div ";
        if($open)
            $chatHTML .= "class = 'chat open'";
        else
            $chatHTML .= "class = 'chat'";
        if($row["username"] == $_SESSION["name"])
            $chatHTML .= " id = 'chat_".$row["application"]."_own'>";
        if($row["applicant"] == $_SESSION["name"])
            $chatHTML .= " id = 'chat_".$row["application"]."_ext'>";

            
        if($_SESSION["name"] == $row["username"])
            $title_own_class = "title_own";
        else
            $title_own_class = "title_ext";
            
        if($_SESSION["name"] == $row["applicant"])
            $title_own_class_applicant = "title_own";
        else
            $title_own_class_applicant = "title_ext";

        $chatHTML .= "<div class = 'chat_bar'><p class='title username ".$title_own_class."'><a href = './user.php?user=".$row["username"]."'>" . $row["username"] . "</a></p><p class = 'title post_community'><a href = './community.php?community=".$row['community']."'>&#64;".$row['community']."</a></p><p class = 'title arrow'>&rarr;</p><p class = 'title username applicant ".$title_own_class_applicant."'><a href = './user.php?user=".$row["applicant"]."'>".$row["applicant"]."</a></p>";
        $chatHTML .= "<div class = 'dotdotdot_wrap' id = 'dotdotdot_".$row['application']."_".$row["post_id"]."'>".dotdotdot("post")."</div></div>";
        $chatHTML .= "<p class='postcontent'>".htmlspecialchars($row["content"])."</p>";

        if($open)
        {
            $chatHTML .= "<div id = 'lastmsg_". $row["last_msg_id"];
            if(!$row["isread"] && $row["msg_username"] != $_SESSION["name"])
                $chatHTML .= "' class = 'unread lastmessage'>";
            else
                $chatHTML .= "' class = 'lastmessage'>";
            $chatHTML .= "<p class='message_username'>".$row["msg_username"]."</p><p class='msg_column'>:</p><p";
            if($row["msg_username"] == $_SESSION["name"])
                $chatHTML .= " class='own msg_text'>";
            else
                $chatHTML .= " class='msg_text'>";
            $chatHTML .= htmlspecialchars($row["msg_text"])."</p>";
            $chatHTML .= "</div>";
        }

        $chatHTML .= "</div>";
        return $chatHTML;
    }
    function display_messages($database, $application, $last_message = 0, $first_message = PHP_INT_MAX)
    {
        $messages = get_messages($database, $application, $_SESSION["name"], $last_message, $first_message);
        while($message = $messages->fetch_assoc())
        {
            echo message_html($message);
        }
    }
    function message_html($message)
    {
        if($message["username"] == $_SESSION["name"])
            $messageHTML = "<div class='own message'";
        else
            $messageHTML = "<div class='message'";
        $messageHTML .= " id='message+".$message["id"]."+".$message["username"]."'>";
        $messageHTML .= nl2br(htmlspecialchars($message["msg_text"]));
        $messageHTML .= "</div>";
        return $messageHTML;
    }
?>