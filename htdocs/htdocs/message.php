<?php 
      require_once "./php/common.php"; 
      require_once "./php/display.php";
      require_once "./php/database.php";
      session_start();
      check_user();
      $database = connect();
      if(!isset($_GET["id"]) || verify_user_chat($database, $_GET["id"], $_SESSION["name"]) != 0)
        header("Location: ./messages.php");
      head();
    ?>
    <title>Messages</title>
    <link rel='stylesheet' href='./css/message.css?v=0'>
    <script type="module" src="javascript/message.js?v=0"></script>
  </head>
  <body>
        <?php 
        navbar();
        sidenav();
        ?>
      <div id='vertical'>
        <main>
            <div id = "application_wrap"><?php
                echo chat_html(get_app_info($database, $_GET["id"]), false);
            ?></div>
            <div id = "messages_wrap">
                <div id = "messages_div" class = "messages_div"><?php
                    display_messages($database, $_GET["id"]);
                ?></div>
                <button id='loadmore'>LOAD MORE</button>
            </div>
            <div id="new_message_wrap">
                <textarea placeholder = "Message..." id = "new_message"></textarea>
                <button id = "sendmessage">SEND</button>
            </div>
        </main>
    </div>
    <?php bottomnav() ?>
  </body>
</html>
<?php
    sign_as_read($_SESSION["name"], $database, $_GET["id"]);
?>