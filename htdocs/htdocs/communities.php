<?php 
      require_once "./php/common.php";
      require_once "./php/display.php";
      require_once "./php/database.php";
      session_start();
      check_user();
      head();
    ?>
    <title>Communities</title>
    <script type="module" src="javascript/communities.js?v=0"></script>
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
          ?>
          <div id = "search">
            <h2>Search for communities
              <input id = "search_bar" placeholder = "Search" type="text">
            </h2>
            <div id="sugg">
              
              <h2>
                Suggested communities
              </h2>
              <div id = "sugg_comm"><?php communities($database, $_SESSION["name"], "sugg"); ?></div>
            </div>
            <div id="search_res"></div>
          </div>
          <div id="yours">
            <h2>Your communities</h2>
            <div id = "yours_comm"><?php communities($database, $_SESSION["name"], "yours"); ?></div>
          </div>
        </main>
    </div>
    <?php bottomnav()?>
  </body>
</html>