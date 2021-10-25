<?php
session_start();
if (empty($_SESSION['mem_id'])) {//ไม่พบค่าเซสชั่น mem_id แสดงว่าไม่ใช่สมาชิก จึงไม่สามารถตั้งกระทู้ได้
    header('Location:index.php');
}
require('bin/connectdb.php'); //เรียกไฟล์เชื่อมต่อกับฐานข้อมูล
if (!empty($_POST['btSave'])) {//มีการคลิกที่ปุ่มบันทึกตั้งกระทู้
    $msgError = '';
    if (!empty($_POST['board_topic']) || !empty($_POST['board_detail'])) {
        $cg_id = $_GET['id']; //รหัสหมวดกระทู้
        $board_topic = trim($_POST['board_topic']); //หัวข้อกระทู้
        $board_detail = nl2br($_POST['board_detail']); //รายละเอียดกระทู้
        mysqli_query($con, "INSERT INTO board(cg_id,board_topic,board_detail,board_time_add,board_time_update,mem_id) 
  VALUES($cg_id,'$board_topic','$board_detail',SYSDATE(),SYSDATE()," . $_SESSION['mem_id'] . ")") or die(mysqli_error());
        mysqli_query($con, "UPDATE category SET cg_topic_totals=cg_topic_totals+1 WHERE cg_id=$cg_id");
        header("Location:showboard.php?id=" . $_GET['id'] . '&notview=1');
    } else {
        $msgError.='กรุณากรอกหัวข้อกระทู้และรายละเอียดของกระทู้ด้วย<br />';
    }
    if (empty($msgError)) {
        //หากสมาชิกพิมพ์ข้อมูลถูกต้อง ให้Redirect หน้าไปที่ไฟล์ category.php
        header("Location:showboard.php?id=" . $_GET['id']);
    } else {
        //หากกรอกข้อมูลไม่ถูกต้อง ให้สร้างตัวแปร session มารับค่าเพื่อแจ้งให้ทราบถึงปัญหาที่เกิดขึ้น
        $_SESSION['message_error'] = $msgError;
    }
}

$show_board = '';
if (!empty($_GET['id'])) {
    $rs_cg = mysqli_query($con, 'SELECT cg_name,cg_id FROM category WHERE cg_id=' . $_GET['id']);
    $show_board = mysqli_fetch_assoc($rs_cg); //นับจำนวนแถวของหมวดกระทู้
    if (empty($show_board['cg_name'])) {
        header('Location:index.php');
    }
} else {//ไม่พบพารามิเตอร์ $_GET['id'] .ให้กลับไปหน้าแรก
    header('Location:index.php');
}
?>
<html>
    <head>
        <?php require('head.php'); ?>
        <link rel="stylesheet" type="text/css" href="btvalidate/dist/css/bootstrapValidator.min.css"/>
        <script type="text/javascript" src="btvalidate/dist/js/bootstrapValidator.min.js"></script>
        <title>ตั้งกระทู้ห้อง <?php echo $show_board['cg_name']; ?></title>
    </head>
    <body>
        <?php require('menu.php'); ?>
        <div class="container">
            <?php require('header.php'); ?>
            <div class="row ws-content">
                <ol class="breadcrumb">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="showboard.php?id=<?php echo $show_board['cg_id']; ?>"><?php echo $show_board['cg_name']; ?></a></li>
                    <li class="active">ตั้งกระทู้</li>
                </ol>
                <div class="col-md-7  col-sm-7 col-md-offset-2 col-sm-offset-2">
                    <h1>ตั้งกระทู้</h1>
                    <?php
                    if (!empty($_SESSION['message_error'])) {
                        //แสดงปัญที่เกิดขึ้นจากการไม่กรอกชื่อหมวดกระทู้
                        ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $_SESSION['message_error']; ?>
                        </div>
                        <?php
                        $_SESSION['message_error'] = '';
                    }
                    ?>
                    <form  method="post" enctype="multipart/form-data" id="boardForm" name="boardForm" action="">
                        <div class="form-group">
                            <label for="Category Name">หัวข้อกระทู้</label>
                            <input type="text" class="form-control" id="board_topic" name="board_topic" placeholder="หัวข้อกระทู้">
                        </div>
                        <div class="form-group">
                            <label for="Category Description">รายละเอียด</label>
                            <textarea class="form-control" id="board_detail"  name="board_detail" placeholder="รายละเอียดของกระทู้" rows="10"></textarea>
                        </div>
                        <div class="form-group">
                            ตั้งกระทู้โดย : <b><?php echo $_SESSION['mem_name']; ?></b>
                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary" name="btSave" value="บันทึกตั้งกระทู้" >
                        </div>
                    </form>
                </div>
            </div>
            <?php require('footer.php'); ?>
        </div>
        <script>
            $(document).ready(function() {
                $('#boardForm').bootstrapValidator({
                    feedbackIcons: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                        board_topic: {
                            validators: {
                                notEmpty: {
                                    message: 'กรุณากรอก หัวข้อกระทู้ ด้วย'
                                }
                            }
                        },
                        board_detail: {
                            validators: {
                                notEmpty: {
                                    message: 'กรุณากรอกรายละเอียดของกระทู้ด้วย'
                                }
                            }
                        }
                    }
                });
            });
        </script>    
    </body>
</html>