<?php
session_start();
if (!empty($_POST['btRegister'])) {
    require('bin/connectdb.php'); //ไฟล์เก็บคำสั่งเชื่อมต่อกับฐานข้อมูล connectdb.php ศึกษาจาก http://php-for-ecommerce.blogspot.com/2014/07/webboard-php-mysql-2.html#connectdb
    $msgError='';
    $username = '';
    $pass = '';
    $email = '';
    $name = '';
    $mem_image = '';
    $fileType = '';
    $filename = '';
  //ตรวจสอบ Username ว่ามีค่าว่างหรือไม่
    if (!empty($_POST['mem_user'])) {
        $username = $_POST['mem_user'];
  //Patternตรวจสอบการกรอกข้อมูลรองรับ a-z,A-Z,ตัวเลข ตั้งแต่ 4-20 ตัวอักษร
  //ศึกษาเพิ่มเติมจาก http://php-for-ecommerce.blogspot.com/2012/09/validate-form-regular-expression.html
        $chkInputUser = '/^[a-zA-Z0-9]{4,20}$/';        
        if (!preg_match($chkInputUser, $username, $regs)) {  //ตรวจสอบการกรอกข้อมูลของ Username ผ่าน
            $msgError .= 'Username ต้องมีขนาดตัวอักษร  4-20 ตัวอักษรภาษาอังกฤษและตัวเลขเท่านั้น<br />';
        }
        //ตรวจสอบ Username ว่าซ้ำหรือไม่
        $rs_username = mysqli_query($con, "SELECT COUNT(*) As cUsername FROM member WHERE mem_user='$username' ");
        $show_rs_username = mysqli_fetch_assoc($rs_username);
        if ($show_rs_username['cUsername'] > 0) {
            $msgError .= 'Username นี้มีผู้ใช้งานแล้ว<br />';
        }
    } else {//ถ้ามีค่าว่างให้แจ้งเออเร่อดังนี้
        $msgError .= 'กรุณากรอก Username ด้วย<br />';
    }
 //ตรวจสอบรหัสผ่านว่ามีค่าว่างหรือไม่
    if (!empty($_POST['mem_pass']) && !empty($_POST['repass'])) {
        $pass = $_POST['mem_pass'];
        $repass = $_POST['repass'];
        if ($pass != $repass) {//ตรวจสอบรหัสผ่านว่าตรงกันทั้งสองช่องหรือไม่
            $msgError .= 'รหัสผ่านทั้งสองช่องไม่ตรงกัน<br />';
        }
    } else {//ถ้ามีค่าว่างให้แจ้งเออเร่อดังนี้
       $msgError .= 'กรุณากรอกรหัสผ่านทั้งสองช่องด้วย<br />';
    }
       //ตรวจสอบอีเมลว่ามีค่าว่างหรือไม่
    if (!empty($_POST['mem_email'])) {
        $email = $_POST['mem_email'];
        $chkInputEmail = '/^([a-zA-Z0-9_.-])+@([a-zA-Z0-9._-])+\.([a-zA-Z])+$/'; //Patternตรวจสอบอีเมล
        //ศึกษาเพิ่มเติมจาก http://php-for-ecommerce.blogspot.com/2012/09/validate-form-regular-expression.html
        if (!preg_match($chkInputEmail, $email, $regs)) {  //ตรวจสอบการกรอกข้อมูลEmailผ่าน
          $msgError .= 'รูปแบบอีเมลไม่ถูกต้อง<br />';
        }
        //ตรวจสอบอีเมลว่าซ้ำหรือไม่
        $rs_email = mysqli_query($con, "SELECT COUNT(*) As cEmail FROM member WHERE mem_email='$email' ");
        $show_rs_email = mysqli_fetch_array($rs_email);
        if ($show_rs_email['cEmail'] > 0) {
           $msgError .= 'อีเมลนี้มีผู้ใช้งานแล้ว<br />';
        }
    } else {//ถ้ามีค่าว่างให้แจ้งเออเร่อดังนี้
       $msgError .= 'กรุณากรอกอีเมลด้วย<br />';
    }
     //ตรวจสอบ ชื่อเรียกในเว็บ ว่ามีค่าว่างหรือไม่
    if (!empty($_POST['mem_name'])) {
        $name = $_POST['mem_name'];
        //ตรวจสอบชื่อเรียกในเว็บว่าซ้ำหรือไม่
        $rs_name = mysqli_query($con, "SELECT COUNT(*) As cName FROM member WHERE mem_name='$name' ");
        $show_rs_name = mysqli_fetch_assoc($rs_name);
        if ($show_rs_name['cName'] > 0) {
           $msgError .= 'ชื่อนี้มีผู้ใช้งานแล้ว<br />';
        }
    } else {//ถ้ามีค่าว่างให้แจ้งเออเร่อดังนี้
        $msgError .= 'กรุณากรอกชื่อ ชื่อแสดงในเว็บ ด้วย<br />';
    }
    //ถ้ารูปประจำตัวไม่เป็นค่าว่าง
    if (!empty($_FILES['mem_image']['name'])) {
        $mem_image = $_FILES['mem_image'];
        $fileType = strtolower(end(explode('.', $mem_image['name'])));        
        if ($fileType != 'jpeg' && $fileType != 'jpg' && $fileType != 'png' &&  $fileType != 'gif') {
   //ตรวจสอบนามสกุลไฟล์ ถ้าไม่มี .jpeg,jpg,png,gif  ให้แสดงเออเร่อดังนี้
           $msgError .= 'นามสกุลไฟล์ไม่ถูกต้อง<br />';
        } else {//หากมีนามสกุลไฟล์ถูกต้อง ให้ใช้ตัวแปร $filename รับชื่อรูปประจำตัว
            $filename = date("dmyHis") . '.' . $fileType;
        }
    }
 
    if (empty($msgError)) {//หากไม่มีข้อความเออเร่อ แสดงว่ากรอกข้อมูลถูกต้องหมดแล้ว
 //ให้บันทึกลงฐานข้อมูล
        mysqli_query($con, "INSERT INTO member(mem_user,mem_pass,mem_email,mem_name
            ,mem_image)  VALUE('$username','$pass','$email','$name','$filename')");
    
        if (!empty($filename)) {//ตรวจสอบชื่อไฟล์ ถ้าไม่เป็นค่าว่าง ให้อัพโหลดไฟล์รูปประจำตัวไปเก็บไว้ใน โฟลเดอร์ images/member
            move_uploaded_file($mem_image['tmp_name'], "images/member/" . $filename);
        }
  //สร้างตัวแปร session มารับค่าเพื่อแจ้งใหสมาชิกทราบว่า ลงทะเบียนเสร็จแล้ว
        $_SESSION['message_success'] = 'ลงทะเบียนเสร็จสมบูรณ์แล้ว';
    } else {//หากมีข้อความเออเร่อ 
 //ให้สร้างตัวแปร sessiion มารับค่าเพื่อแจ้งให้สมาชิกถึงปัญหาที่เกิดขึ้น
         $_SESSION['message_error']= $msgError;
    }
}
?>
<html>
    <head>
        <?php require('head.php'); ?>
        <link rel="stylesheet" type="text/css" href="btvalidate/dist/css/bootstrapValidator.min.css"/>
        <script type="text/javascript" src="btvalidate/dist/js/bootstrapValidator.min.js"></script>
        <title>สมัครสมาชิก</title>
    </head>
    <body>
        <?php require('menu.php'); ?>
        <div class="container">
            <?php require('header.php'); ?>
            <div class="row ws-content">
                <div class="col-md-4  col-sm-4 col-md-offset-4 col-sm-offset-4">
                    <h1>สม้ครสมาชิก</h1>
                    <?php
     //พบตัวแปร session ชื่อ message_success  แสดงว่าลงทะเบียนเสร็จสมบูรณ์แล้ว                    
                    if (!empty($_SESSION['message_success'])) {
                        ?>
                        <div class="alert alert-success" role="alert">
                            <?php 
       //ให้แสดงข้อความแจ้งให้สมาชิกทราบดังนี้
       echo $_SESSION['message_success']; 
       ?><br />
                            <span>คลิก <a href="login.php">ที่นี้</a> เพื่อเข้าสู่ระบบ</span>
                        </div>
                        <?php
                        $_SESSION['message_success'] = '';
                    }
                    ?>
                    <?php
     //พบตัวแปร session ชื่อ message_error  แสดงว่ามีปัญหาเกิดขึ้น จากการกรอกข้อมูลของสมาชิก
                    if (!empty($_SESSION['message_error'])) {
                        ?>
                        <div class="alert alert-danger" role="alert">
                            <?php
       //ให้แสดงข้อความแจ้งให้สมาชิกทราบดังนี้ 
       echo $_SESSION['message_error']; 
       ?>
                        </div>
                        <?php
                        $_SESSION['message_error'] = '';
                    }
                    ?>
                    <form  method="post" enctype="multipart/form-data" id="registrationForm" name="registrationForm" action="">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="mem_user" name="mem_user" placeholder="Username">
                        </div>
                        <div class="form-group">
                            <label for="password">รหัสผ่าน</label>
                            <input type="password" class="form-control" id="mem_pass"  name="mem_pass" placeholder="รหัสผ่าน">
                        </div>
                        <div class="form-group">
                            <label for="repassword">ยืนยันรหัสผ่าน</label>
                            <input type="password" class="form-control" id="repass" name="repass" placeholder="ยืนยันรหัสผ่าน">
                        </div>
                        <div class="form-group">
                            <label for="name">ชื่อแสดงในเว็บ</label>
                            <input type="text" class="form-control" id="mem_name"  name="mem_name" placeholder="ชื่อแสดงในเว็บ">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="text" class="form-control" id="mem_email"  name="mem_email" placeholder="อีเมล">
                        </div>
                        <div class="form-group">
                            <label for="image member">รูประจำตัว</label>
                            <input type="file" id="mem_image" name="mem_image">
                        </div>
                        <div class="form-group">
                        <input type="submit" class="btn btn-primary" name="btRegister" value="ลงทะเบียน" >
                        <input type="reset" class="btn btn-primary" name="reset" value="Reset">
                        </div>
                    </form>
                </div>
            </div>
            <?php require('footer.php'); ?>
        </div>  
        <script>
           $(document).ready(function() {
                $('#registrationForm').bootstrapValidator({
                    feedbackIcons: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                        mem_user: {//ตรวจสอบการกรอกข้อมูลของ textfield ชื่อ mem_user (name="mem_user")
                            validators: {
                                notEmpty: {//ตรวจสอบค่าว่างของ mem_user
                                    message: 'กรุณากรอก Username ด้วย'
                                },
                                stringLength: {//ตรวจสอบขนาดตัวอักษรของ mem_user
                                    min: 4,
                                    max: 20,
                                    message: 'Username ต้องมีขนาดตัวอักษร  4-20 ตัวอักษรเท่านั้น'
                                },
                                regexp: {//ตรวจสอบการกรอกข้อมูลที่รองรับเฉพาะตัวอักษรภาษาอังกฤษและตัวเลขเท่านั้นของ mem_user
                                    regexp: /^[a-zA-Z0-9]+$/,
                                    message: 'กรอกข้อมูลไม่ถูกต้อง รองรับภาษาอังกฤษและตัวเลขเท่านั้น'
                                },
                                different: {//ค่าใน mem_user ต้องไม่ตรงกับ mem_pass
                                    field: 'mem_pass',
                                    message: 'Username ต้องมีค่าไม่ตรงกับรหัสผ่าน'
                                }, remote: {//ส่งค่าไปตรวจสอบในฐานข้อมูลว่ามีผู้ใช้งานชื่อ username นี้แล้วหรือยัง
                                    message: 'Username นี้มีผู้ใช้งานแล้ว',
                                    url: 'check_data_ajax.php',
                                    data: {
                                        type: 'username'
                                    }
                                }
                            }
                        },
                        mem_pass: {//ตรวจสอบการกรอกข้อมูลของ textfield ชื่อ mem_pass (name="mem_pass")
                            validators: {
                                notEmpty: {//ตรวจสอบค่าว่าง
                                    message: 'กรุณากรอก รหัสผ่าน ด้วย'
                                },
                                stringLength: {//ตรวจสอบขนาดตัวอักษร
                                    min: 4,
                                    max: 20,
                                    message: 'รหัสผ่านต้องมีขนาด 4-20 ตัวอักษร'
                                }
                            }
                        },
                        repass: {//ตรวจสอบการกรอกข้อมูลของ textfield ชื่อ repass (name="repass")
                            validators: {
                                notEmpty: {//ตรวจสอบค่าว่าง
                                    message: 'กรุณากรอก ยืนยันรหัสผ่าน ด้วย'
                                }, identical: {//ตรวจสอบค่าต้องตรงกับ textfield ชื่อ mem_pass
                                    field: 'mem_pass',
                                    message: 'ค่าต้องตรงกับรหัสผ่าน'
                                }
                            }
                        },
                        mem_email: {//ตรวจสอบการกรอกข้อมูลของ textfield ชื่อ mem_email (name="mem_email")
                            validators: {
                                notEmpty: {//ตรวจสอบค่าว่าง
                                    message: 'กรุณากรอก Email ด้วย'
                                },
                                emailAddress: {//ตรวจสอบรูปแบบอีเมลว่ากรอกถูกต้องตามรูปแบบหรือไม่
                                    message: 'รูปแบบอีเมลไม่ถูกต้อง'
                                }, remote: {//ส่งค่าไปตรวจสอบในฐานข้อมูลว่ามีผู้ใช้อีเมลนี้แล้วหรือยัง
                                    message: 'Email นี้มีผู้ใช้งานแล้ว',
                                    url: 'check_data_ajax.php',
                                    data: {
                                        type: 'email'
                                    }
                                }
                            }
                        },
                        mem_name: {//ตรวจสอบการกรอกข้อมูลของ textfield ชื่อ mem_name (name="mem_name")
                            validators: {
                                notEmpty: {//ตรวจสอบค่าว่าง
                                    message: 'กรุณากรอกชื่อแสดงในเว็บด้วย'
                                }, remote: {//ส่งค่าไปตรวจสอบในฐานข้อมูลว่ามีผู้ใช้ชื่อนี้แล้วหรือยัง
                                    message: 'ชื่อนี้มีผู้ใช้งานแล้ว',
                                    url: 'check_data_ajax.php',
                                    data: {
                                        type: 'nameMember'
                                    }
                                }
                            }
                        },
                        mem_image: {//ตรวจสอบการกรอกข้อมูลของ File Field ชื่อ mem_image (name="mem_image")
                            validators: {
                                file: {//ตรวจสอบนามสกุลไฟล์ รองรับ jpeg,jpg,png,gif และขนาดไม่เกิน 2MB
                                    extension: 'jpeg,jpg,png,gif',
                                    type: 'image/jpeg,image/jpg,image/png,image/gif',
                                    maxSize: 2048 * 1024, // 2 MB
                                    message: 'รองรับนามสกุล jpg,jpeg,png,gif และขนาดต้องไม่เกิน 2MB'
                                }
                            }
                        }
                    }
                });
            });
        </script>    
    </body>
</html>