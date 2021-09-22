<?php
    //1.connect to mysql database
    //host, db_username, db_password, db_name
    $host = "localhost";
    $db_username = "root";
    $db_passwd = "";
    $db_name = "simple_db";

    //conect to database
    $con = mysqli_connect($host, $db_username, $db_passwd, $db_name)
    or die("Error " . mysqli_error($con));
?>
