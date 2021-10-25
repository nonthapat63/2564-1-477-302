<?php
session_start();
session_destroy();//ล้างค่าในตัวแปร session ทิ้งทั้งหมด
header('Location:index.php');
?>