    <?php 
        require_once "./php/common.php"; 
        require_once "./php/display.php";
        require_once "./php/database.php";
        session_start();;
        check_user();
        head();
        $own = false;
        if(!isset($_GET["user"]))
        {
            $_GET["user"] = $_SESSION["name"];
            $own = true;
        }
        $user = $_GET["user"];
    ?>
    <title><?php echo $user?></title>
    <link rel='stylesheet' href='./css/user.css?v=0'>
    <script type="module" src="javascript/user.js?v=0"></script>
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
                    $user = get_user($database, $user);
                    if(!$user)
                    {
                        echo "<div class = 'err'>This user doesn't exist</div>";
                        exit();
                    }
                    echo "<div class = 'pre' id='user'>";
                    display_user($user);
                    echo "</div>";
                    echo "<div id='posts'>";
                    display_user_posts($database, $user["username"]);
                    echo "</div>";
                    echo "<button id='loadmore'>LOAD MORE</button>";
                ?>
            </main>
        </div>
    <?php bottomnav() ?>
  </body>
</html>