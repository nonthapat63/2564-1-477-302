<?php
require('bin/connectdb.php');
$isAvailable = true;
switch ($_POST['type']) {
    case 'email':
        //ตรวจสอบอีเมลว่าซ้ำหรือไม่
        $email = $_POST['mem_email'];
        $rs_email = mysqli_query($con, "SELECT COUNT(*) As cEmail FROM member WHERE mem_email='$email' ");
        $show_rs_email = mysqli_fetch_assoc($rs_email);
        if ($show_rs_email['cEmail'] > 0) {
            $isAvailable = false;
        }
        break;
    case 'nameMember':
        //ตรวจสอบชื่อเรียกในเว็บว่าซ้ำหรือไม่
        $name = $_POST['mem_name'];
        $rs_name = mysqli_query($con, "SELECT COUNT(*) As cName FROM member WHERE mem_name='$name' ");
        $show_rs_name = mysqli_fetch_assoc($rs_name);
        if ($show_rs_name['cName'] > 0) {
            $isAvailable = false;
        }
        break;
    default:
        //ตรวจสอบ Username ว่าซ้ำหรือไม่
        $username = $_POST['mem_user'];
        $rs_username = mysqli_query($con, "SELECT COUNT(*) As cUsername FROM member WHERE mem_user='$username' ");
        $show_rs_username = mysqli_fetch_assoc($rs_username);
        if ($show_rs_username['cUsername'] > 0) {
            $isAvailable = false;
        }
        break;
}
// คืนค่าแบบ JSON
echo json_encode(array(
    'valid' => $isAvailable,
));
?>