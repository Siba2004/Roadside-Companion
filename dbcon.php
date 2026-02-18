<?php
$conn=new mysqli("localhost","root","2255","road_side_companion","3306");
if($conn->connect_error){
    die("Error : ".$conn->connect_error);
}
?>