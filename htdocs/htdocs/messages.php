<?php 
      require_once "./php/common.php";
      require_once "./php/display.php";
      require_once "./php/database.php";
      session_start();
      check_user();
      head();
    ?>
    <title>Messages</title>
    <link rel='stylesheet' href='./css/messages.css?v=0'>
    <script type="module" src="javascript/messages.js?v=0"></script>
  </head>
  <body>
    <?php 
      navbar();
      sidenav();
      $database = connect();
    ?>
    <div id='vertical'>
      <main>
        <div id = "open_chats">
          <h2>Open chats</h2>
          <div id = "open_chats_div" class = "chats"><?php display_chats($database, true); ?></div>
          <button id = "load_more_open" class='loadmore'>LOAD MORE</button>
        </div>
        <div id = "potential_chats">
          <h2>Matches</h2>
          <div id = "potential_chats_div" class = "chats"><?php display_chats($database, false); ?></div>
          <button id = "load_more_potential" class='loadmore'>LOAD MORE</button>
        </div>
      </main>
    </div>
    <?php bottomnav() ?>
  </body>
</html>