<?php
if($_SESSION['mem_level']!=1){//หากไม่ใช่ admin
header('Location:index.php'); //ให้รีไดเร็คไปหน้า index.php
exit();
}
?>