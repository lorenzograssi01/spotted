<?php 
      require_once "./php/common.php"; 
      require_once "./php/common.php";
      require_once "./php/database.php";
      session_start();
      check_user();
      $msg = "";
      $database = connect();
      $currentdata = get_user($database, $_SESSION["name"]);
      if(isset($_POST["description"]) && isset($_POST["instagram"]) && isset($_POST["facebook"]) && isset($_POST["snapchat"]) && isset($_POST["favorite"]))
      {
          if(strlen($_POST["instagram"]) > 30)
            $msg =  "<script type = 'module'>error('Instagram username is too long')</script>";
          elseif(strlen($_POST["facebook"]) > 50)
            $msg = "<script type = 'module'>error('Facebook username is too long')</script>";
          elseif(strlen($_POST["snapchat"]) > 15)
            $msg = "<script type = 'module'>error('Snapchat username is too long')</script>";
          elseif(strlen($_POST["favorite"]) == 0 && (strlen($_POST["snapchat"]) != 0 || strlen($_POST["facebook"]) != 0 || strlen($_POST["instagram"]) != 0)) 
            $msg = "<script type = 'module'>error('You must select a favorite contact')</script>";
          elseif($_POST["favorite"] && !$_POST[$_POST["favorite"]]) 
            $msg = "<script type = 'module'>error('Your favorite contact can\'t be blank')</script>";
          else
          {
              $result = update_user_info($database, $_SESSION["name"], $_POST);
              if($result == 0)
                  header("location: ./user.php");
              elseif($result == -1)
                $msg = "<script type = 'module'>error('Contact usernames can only contain letters, numbers and these characters: . - _')</script>";
              else   
                $msg = "<script type = 'module'>error('An error has occurred, try again')</script>";
          }
      }
      head();
      if(!isset($_GET["new_name"]))
        $_GET["new_name"] = "";
    ?>
    <script type="module" src="javascript/editprofile.js?v=0"></script>
    <link href = "./css/createnew.css?v=0" rel = "stylesheet">
    <title>Edit profile</title>
  </head>
  <body>
    <?php 
      navbar();
      sidenav();
    ?>
      <div id='vertical'>
        <main>
            <h2>
                Edit profile
            </h2>
            <form method = "post">
                <label for = "description">Description</label> 
                <textarea id = "description" placeholder = "Describe yourself! Include everything you'd like people to know about you!" name = "description" ><?php echo $currentdata["description"] ?></textarea>
                <section id = "contacts">
                    <p class = "section_title">Contacts (do not include handles, @, etc)</p>
                    <label for = "instagram">Instagram username</label> 
                    <input type = "text" id = "instagram" maxlength = "30" placeholder = "yourusername" name = "instagram" value = "<?php echo $currentdata["instagram"] ?>">
                    <label for = "facebook">Facebook username</label> 
                    <input type = "text" id = "facebook" minlength = "5" maxlength = "50" placeholder = "yourusername" name = "facebook" value = "<?php echo $currentdata["facebook"] ?>">
                    <label for = "snapchat">Snapchat username</label> 
                    <input type = "text" id = "snapchat" minlength = "3" maxlength = "15" placeholder = "yourusername" name = "snapchat" value = "<?php echo $currentdata["snapchat"] ?>">
                    <label for = "favorite">Favorite contact</label>
                    <select id = "favorite" name = "favorite">
                        <option label = "Select" hidden  <?php if($currentdata["favorite"] == "") echo "selected"?>></option>
                        <option id = "instagramfav" value = "instagram" <?php if($currentdata["favorite"] == "instagram") echo "selected"; if($currentdata["instagram"] == "") echo "disabled" ?>>Instagram</option>
                        <option id = "facebookfav" value = "facebook" <?php if($currentdata["favorite"] == "facebook") echo "selected"; if($currentdata["facebook"] == "") echo "disabled" ?>>Facebook</option>
                        <option id = "snapchatfav" value = "snapchat" <?php if($currentdata["favorite"] == "snapchat") echo "selected"; if($currentdata["snapchat"] == "") echo "disabled" ?>>Snapchat</option>
                    </select>
                </section>
                <button class = "send" type = "submit">UPDATE</button>
            </form>
            <?php
                if($msg)
                    echo $msg;
            ?>
        </main>
    </div>
    <?php bottomnav()?>
  </body>
</html>