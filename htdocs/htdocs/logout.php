  <?php 
    require_once "./php/common.php"; 
    session_start();
    session_destroy();
    header("Location: ./login.php");
  ?>