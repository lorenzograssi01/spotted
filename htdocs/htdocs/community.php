<?php 
      require_once "./php/common.php";
      require_once "./php/display.php";
      require_once "./php/database.php";
      session_start();
      check_user();
      head();
      if(!isset($_GET["community"]))
      {
        header("Location: ./communities.php");
      }
    ?>
    <title><?php echo $_GET["community"] ?></title>
    <link rel='stylesheet' href='./css/community.css?v=0'>
    <script type="module" src="javascript/community.js?v=0"></script>
  </head>
  <body>
    <?php 
      navbar();
      sidenav();
    ?>
      <div id='vertical'>
        <main>
          <?php
            $database = connect();
            $comm = get_community($_SESSION['name'], $database, $_GET['community']);
            if(!$comm)
            {
              echo "<script type = 'module'>error('This community doesn\'t exist!');</script>";
            }
            else
            {
              echo "<div class = 'pre' id='community'>";
              display_community($_SESSION['name'], $database, $comm, null);
              echo "</div><div id='newpostcontainer'>";
              echo "<div ";
              if(!$comm["subscribed"])
                echo "class='hidden addpost' ";
              else
                echo "class = 'addpost' ";
              echo "id = 'addpost+".$comm["name_"]."'><textarea placeholder = 'Post something in @".$comm["name_"]."' class= 'newpostarea' id='newpostvalue+".$comm["name_"]."'></textarea><div id = 'newpostbar'><div><input type = 'checkbox' id = 'anon'><label class='clickable' for='anon'>Anonymous</label></div><button id='send_post'>POST</button></div></div>";
              echo "</div>";
              echo "<div id='posts'>";
              display_community_posts($database, $comm);
              echo "</div>";
              echo "<button id='loadmore'>LOAD MORE</button>";
            }
          ?>
        </main>
    </div>
    <?php bottomnav() ?>
  </body>
</html>