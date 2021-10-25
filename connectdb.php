<?php
$host = "localhost";
$db_username = "root";
$db_passwd = "";
$db_name = "pet_db";

//conect to database
$con = mysqli_connect($host, $db_username, $db_passwd, $db_name)
or die("Error " . mysqli_error($con));
mysqli_set_charset( $con, 'utf8mb4');

?>

<!-- mysqli_query("SET collation_connection = utf8mb4");
mysqli("SET character_set_results=utf8mb4");
mysqli("SET collation_connection=utf8mb4");
mysqli("SET NAMES 'utf8mb4'"); --!>