<?php
session_start();
session_destroy();
setcookie("roadside_login","",time()-3600,"/");
header("location: home.php");
?>