<?php
session_start();
session_destroy();
setcookie("quicklab_login","",time()-3600,"/");
header("location: home.php");
?>