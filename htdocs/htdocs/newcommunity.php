<?php 
      require_once "./php/common.php"; 
      require_once "./php/display.php";
      require_once "./php/database.php";
      session_start();
      check_user();
      $msg = "";
      if(isset($_POST["community_name"]) && isset($_POST["community_desc"]))
      {
          if(strlen($_POST["community_name"]) < 3)
              $msg = "<script type = 'module'>error('The community name must be at least 3 characters long!')</script>";
          elseif(strlen($_POST["community_desc"]) < 10)
              $msg = "<script type = 'module'>error('The community description must be at least 10 characters long!')</script>";
          elseif(strlen($_POST["community_name"]) > 30)
              $msg = "<script type = 'module'>error('The community name is too long')</script>";
          else
          {
              $database = connect();
              $res = create_community($database, $_SESSION["name"], $_POST["community_name"], $_POST["community_desc"]);
              if($res == 2)
                  $msg = "<script type = 'module'>error('This community already exists! Make sure you aren\'t creating a duplicate!')</script>";
              elseif($res == 1)
                  $msg = "<script type = 'module'>error('The community name can only contain letters, numbers and these characters: . - _')</script>";
              else
              {
                  subscribe($database, $_POST["community_name"], $_SESSION["name"]);
                  header("Location: ../communities.php");
              }
          }
      }
      head();
      if(!isset($_GET["new_name"]))
        $_GET["new_name"] = "";
    ?>
    <link href = "./css/createnew.css?v=0" rel = "stylesheet">
    <script type="module" src="javascript/newcommunity.js?v=0"></script>
    <title>New community</title>
  </head>
  <body>
        <?php 
            navbar();
            sidenav();
        ?>
      <div id='vertical'>
        <main>
            <h2>
                New community
            </h2>
            <form method = "post">
                <label for = "community_name">Name</label> 
                <input minlength = "3" maxlength = "30" placeholder = "Community name" id= "community_name" name = "community_name" type = "text" value = <?php echo "'" . $_GET["new_name"] . "'" ?>>
                <label for = "community_desc">Description</label> 
                <textarea minlength = "10" id = "community_desc" placeholder = "Describe the community! Include important keywords to help users find it!" name = "community_desc"></textarea>
                <button class = "send" type = "submit">CREATE</button>
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