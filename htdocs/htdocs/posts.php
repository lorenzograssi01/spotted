   <?php 
      require_once "./php/common.php"; 
      require_once "./php/display.php";
      require_once "./php/database.php";
      session_start();
      check_user();
      head();
    ?>
    <title>Home</title>
    <script type="module" src="javascript/posts.js?v=0"></script>
    <link rel = "stylesheet" href = "./css/posts.css?v=0">
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
            echo "<div id='newpostcontainer'>";
            echo "<div class = 'addpost' id = 'addpost'><textarea placeholder = 'Add your post' class= 'newpostarea' id='newpostvalue'></textarea><div id = 'newpostbar'><div class = 'hidden' id = 'anon_bar'><input type = 'checkbox' id = 'anon'><label class='clickable' for='anon'>Anonymous</label></div><label id = 'select_comm' class = 'select_comm' for = 'newpost_communities'>Select community:</label>";
            dropdown_menu(get_your_communities_name($database, $_SESSION["name"]), "community", "@", "newpost_communities");
            echo "<div id = 'send_wrap' class = 'hidden'><span id = 'change_community'>×</span><button id='send_post'>POST</button></div></div></div>";
            echo "</div>";
            echo "<div id='posts'>";
            display_posts($_SESSION["name"], $database);
            echo "</div>";
            echo "<button id='loadmore'>LOAD MORE</button>";
          ?>
        </main>
    </div>
    <?php bottomnav()?>
  </body>
</html>