<?php
session_start();
if (empty($_SESSION['mem_id'])) {//ไม่พบค่าเซสชั่น mem_id แสดงว่าไม่ใช่สมาชิก จึงไม่สามารถตั้งกระทู้ได้
    header('Location:index.php');
}
require('bin/connectdb.php'); //เรียกไฟล์เชื่อมต่อกับฐานข้อมูล
if (!empty($_POST['btSaveEdit'])) {//มีการคลิกที่ปุ่มบันทึกแก้ไขกระทู้
    $msgError = '';
    if (!empty($_POST['board_detail'])) {
        if (!empty($_GET['id']) && !empty($_GET['topic_id'])) {
            $id = $_GET['id']; //รหัสกระทู้
            $topic_id = $_GET['topic_id'];
            $board_detail = nl2br($_POST['board_detail']); //รายละเอียดกระทู้
            mysqli_query("UPDATE board SET board_detail='$board_detail',board_time_update=SYSDATE() 
  WHERE board_id=$id") or die(mysqli_error());
            header("Location:viewboard.php?id=$topic_id");
            exit();
        }
    } else {
        $msgError.='กรุณากรอกหัวข้อกระทู้และรายละเอียดของกระทู้ด้วย<br />';
    }
    if (empty($msgError)) {
        //หากสมาชิกพิมพ์ข้อมูลถูกต้อง ให้Redirect หน้าไปที่ไฟล์ category.php
        header("Location:อ.php?id=" . $_GET['cg_id']);
        exit();
    } else {
        //หากกรอกข้อมูลไม่ถูกต้อง ให้สร้างตัวแปร session มารับค่าเพื่อแจ้งให้ทราบถึงปัญหาที่เกิดขึ้น
        $_SESSION['message_error'] = $msgError;
    }
}
$show_board = '';
if (isset($_GET['topic_id']) && isset($_GET['id'])) {
    $rs_cg = mysqli_query($con, 'SELECT c.cg_id,c.cg_name,b.board_topic
  FROM board As b 
  LEFT JOIN category As c ON b.cg_id=c.cg_id 
  WHERE b.board_id=' . $_GET['topic_id']) or die(mysqli_error());
    $show_cg = mysqli_fetch_assoc($rs_cg);
    if (empty($show_cg['cg_name'])) {
        header('Location:index.php');
        exit();
    }
    $rs_board = mysqli_query($con, 'SELECT b.board_id,b.mem_id,b.board_topic,b.board_detail
  FROM board As b 
  LEFT JOIN member As m ON b.mem_id=m.mem_id 
  WHERE b.board_id=' . $_GET['id']);
    $show_board = mysqli_fetch_assoc($rs_board);
    if ($_SESSION['mem_level'] != 1 && $show_board['mem_id'] != $_SESSION['mem_id']) {  //ไม่ใช่Admin และไม่ใช่เจ้าของกระทู้ แสดงว่าไม่มีสิทธิ์จัดการในส่วนนี้
        header('Location:index.php'); //เด้งกลับไปหน้าหลัก
        exit();
    }
} else {//ไม่พบพารามิเตอร์ $_GET['id'] .ให้กลับไปหน้าแรก
    header('Location:index.php');
    exit();
}
?>
<html>
    <head>
        <?php require('head.php'); ?>
        <link rel="stylesheet" type="text/css" href="btvalidate/dist/css/bootstrapValidator.min.css"/>
        <script type="text/javascript" src="btvalidate/dist/js/bootstrapValidator.min.js"></script>
        <title>แก้ไขความคิดเห็นห้อง
            <?php echo $show_cg['cg_name']; ?>
        </title>
    </head>
    <body>
        <?php require('menu.php'); ?>
        <div class="container">
            <?php require('header.php'); ?>
            <div class="row ws-content">
                <ol class="breadcrumb">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="showboard.php?id=<?php echo $show_cg['cg_id']; ?>"><?php echo $show_cg['cg_name']; ?></a></li>
                    <li class="active">แก้ไขความคิดเห็น</li>
                </ol>
                <div class="col-md-7  col-sm-7 col-md-offset-2 col-sm-offset-2">
                    <h1>แก้ไขความคิดเห็น</h1>
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
                            <label for="Category Name">หัวข้อกระทู้</label><br />
                            <span><?php echo $show_cg['board_topic'] ?></span>
                        </div>
                        <div class="form-group">
                            <label for="Category Description">ความคิดเห็น</label>
                            <textarea class="form-control" id="board_detail"  name="board_detail" placeholder="รายละเอียดของกระทู้" rows="10"><?php echo str_replace('<br />', '', $show_board['board_detail']); ?></textarea>
                        </div>
                        <div class="form-group">
                            แก้ไขความคิดเห็นโดย : <b><?php echo $_SESSION['mem_name']; ?></b>
                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary" name="btSaveEdit" value="บันทึกกระทู้" >
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
                        board_detail: {
                            validators: {
                                notEmpty: {
                                    message: 'กรุณากรอก ความคิดเห็น ด้วย'
                                }
                            }
                        }
                    }
                });
            });
        </script>    
    </body>
</html>