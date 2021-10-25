<?php
session_start();
require('bin/connectdb.php'); //เรียกไฟล์เชื่อมต่อกับฐานข้อมูล
$show_topic_view = '';
$rs_board = '';
if (isset($_GET['delTopicID'])) {//ต้องการลบกระทู้
    require('check_admin.php'); //ตรวจสอบว่าเป็นadminกดลบหรือป่าว ถ้าไม่ใช่ เราจะไม่ให้ลบกระทู้ได้
    $id = $_GET['delTopicID'];
    mysqli_query($con, 'DELETE FROM board WHERE board_id=' . $id); //ลบกระทู้หลัก
    mysqli_query($con, 'DELETE FROM board WHERE board_parent_id=' . $id); //ลบความคิดเห็นทั้งหมดในกระทู้
    header('Location:viewboard.php?id=' . $id);
    exit();
}
if (isset($_GET['delAnsID']) && isset($_GET['topic_id'])) {//ต้องการลบกระทู้ความคิดเห็น
    $id = $_GET['delAnsID'];
    $topic_id = $_GET['topic_id'];
    mysqli_query($con, 'DELETE FROM board WHERE board_id=' . $id); //ลบความคิดเห็น
    header('Location:viewboard.php?id=' . $topic_id);
    exit();
}
 
if (isset($_POST['btSaveRep'])) {//มีการคลิกที่ปุ่ม แสดงความคิดเห็น
    if (empty($_SESSION['mem_id'])) {//ถ้าไม่ใช่สมาชิก
        header('Location:index.php'); //ให้กลับไปหน้าหลัก
        exit(); //หยุดทำงานถึงบรรทัดตรงนี้
    }
 
    $id = $_GET['id'];
    $mem_id = $_SESSION['mem_id'];
    if (!empty($_POST['board_detail'])) {
        $boardDetail = $_POST['board_detail'];
        mysqli_query($con, "INSERT board(board_parent_id,mem_id,board_detail,board_time_add)
   VALUES($id,$mem_id,'$boardDetail',SYSDATE()) ");
        mysqli_query($con, 'UPDATE board  As b LEFT JOIN category As c ON b.cg_id=c.cg_id
  SET b.board_replies=b.board_replies+1,c.cg_replie_totals=c.cg_replie_totals+1,b.board_time_update=SYSDATE()
  WHERE b.board_id=' . $id); //Update จำนวนความคิดเห็นในกระทู้นั้นๆ
    }
    header('Location:viewboard.php?id=' . $id);
    exit();
}
if (isset($_GET['id'])) {//พบว่ามีส่งเมธอดชื่อ id เข้ามา
    $rs_topic_view = mysqli_query($con, 'SELECT b.board_id,b.board_topic,b.board_detail,b.board_time_add,c.cg_id,c.cg_name
  FROM board As b 
  LEFT JOIN category As c ON b.cg_id=c.cg_id 
  WHERE b.board_id=' . $_GET['id']);
    $show_topic_view = mysqli_fetch_assoc($rs_topic_view);
    if (empty($show_topic_view['board_id'])) {//ฟิลด์ board_id เป็นค่าว่างแสดงว่าไม่มีกระทู้นี้อยู่ในฐานข้อมูล
        header('Location:index.php'); //ให้กลับไปหน้าหลัก
    } else {
        if (empty($_GET['notview'])) {//ค่า empty (ว่าง) แสดงว่าให้updateจำนวนผู้เข้าชมได้ ถ้าไม่ empty แสดงว่าห้ามupdateจำนวน
            mysqli_query($con, 'UPDATE board SET board_views=board_views+1 WHERE board_id=' . $_GET['id']); //Update จำนวนผู้เข้าชมของกระทู้นั้น
        }
    }
} else {//ไม่พบค่า id ที่ส่งมา
    header('Location:index.php'); //กลับไปหน้าหลัก
}
?>
<html>
    <head>
        <?php require('head.php'); ?>
        <link rel="stylesheet" type="text/css" href="btvalidate/dist/css/bootstrapValidator.min.css"/>
        <script type="text/javascript" src="btvalidate/dist/js/bootstrapValidator.min.js"></script>
        <title><?php echo $show_topic_view['board_topic']; ?></title>
    </head>
    <body>
        <?php require('menu.php'); ?>
        <div class="container">
            <?php require('header.php'); ?>
            <div class="row ws-content">
                <ol class="breadcrumb">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="showboard.php?id=<?php echo $show_topic_view['cg_id']; ?>"><?php echo $show_topic_view['cg_name']; ?></a></li>
                    <li class="active"><?php echo $show_topic_view['board_topic']; ?></li>
                </ol>
                <div>
                    <h1><?php echo $show_topic_view['board_topic']; ?></h1>
                    <?php
                    $rs_board = mysqli_query($con, 'SELECT b.board_id,b.mem_id,b.board_topic,b.board_detail,b.board_time_add,c.cg_id,c.cg_name,m.mem_name,m.mem_image
  FROM board As b 
  LEFT JOIN category As c ON b.cg_id=c.cg_id 
  LEFT JOIN member As m ON b.mem_id=m.mem_id 
  WHERE b.board_id=' . $_GET['id'] . ' OR b.board_parent_id=' . $_GET['id'] . ' ORDER BY b.board_time_add ASC');
                    $rowNo = 0;
                    while ($show_board = mysqli_fetch_assoc($rs_board)) {
                        $board_id = $show_board['board_id'];
                        $cg_id = $show_board['cg_id'];
                        ?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <div style="display:table-cell;padding-right:5px;" class="hidden-xs">
                                    <?php
                                    $userIcon = 'usericon.jpg';
                                    if (!empty($show_board['mem_image']))
                                        $userIcon = $show_board['mem_image'];
                                    ?>
                                    <img src="images/member/<?php echo $userIcon; ?>" width="50" height="50">
                                </div>
                                <div style="display:table-cell;vertical-align:top;width:100%;"> 
                                    <div style="text-align:right;color:#C8C8C8;border-bottom:1px dashed #C8C8C8;padding-bottom:4px;">
                                        <?php
                                        $linkEdit = "board_edit.php?id=$board_id&cg_id=$cg_id";
                                        $linkDel = 'viewboard.php?delTopicID=' . $board_id;
                                        if ($rowNo != 0) {
                                            $linkEdit = "board_ans_edit.php?id=$board_id&topic_id=" . $_GET['id'];
                                            $linkDel = 'viewboard.php?delAnsID=' . $board_id . '&topic_id=' . $_GET['id'];
                                            ?>
                                            <b> ความคิดเห็นที่  <?php echo $rowNo; ?></b>
                                        <?php } else { ?>
                                            กระทู้หลัก
                                        <?php } ?>
                                        By : <span style="color:#060"><?php echo $show_board['mem_name'] ?></span>
                                        Date : <?php echo $show_board['board_time_add']; ?>
                                        <span style="color:#999">    <?php
                                            if (isset($_SESSION['mem_id'])) {
                                                if ($_SESSION['mem_level'] == 1 || $show_board['mem_id'] == $_SESSION['mem_id']) {
                                                    ?>
                                                    (<a href="<?php echo $linkEdit; ?>">แก้ไข</a>
                                                    <?php if ($_SESSION['mem_level'] == 1 && $rowNo == 0) {//ลบได้เฉพาะ admin เท่านั้น?>
                                                        /<a href="<?php echo $linkDel; ?>" onClick="return confirm('ยืนยันการลบข้อมูล')">ลบ</a>
                                                    <?php } else if ($rowNo > 0) { //สามาชิกสามารถลบความคิดเห็นของตัวเองได้?>
                                                        /<a href="<?php echo $linkDel; ?>" onClick="return confirm('ยืนยันการลบข้อมูล')">ลบ</a>
                                                    <?php } ?>
                                                    )
                                                <?php }
                                            } ?></span>
                                    </div>
                                    <div style="padding-top:4px;">
    <?php echo $show_board['board_detail']; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php $rowNo++;
                    } ?>
 
<?php if (!empty($_SESSION['mem_name'])) { ?>
                        <div class="col-md-7  col-sm-7 col-md-offset-2 col-sm-offset-2">
                            <h4>แสดงความคิดเห็น</h4>    
                            <form  method="post" enctype="multipart/form-data" id="boardReplieForm" name="boardReplieForm" action="">
 
                                <div class="form-group">
                                    <label for="Category Description">รายละเอียด</label>
                                    <textarea class="form-control" id="board_detail"  name="board_detail" placeholder="ใส่ความคิดเห็นตรงนี้" rows="10"></textarea>
                                </div>
                                <div class="form-group">
                                    แสดงความคิดเห็นโดย : <span style="color:#963"><?php echo $_SESSION['mem_name']; ?></span>
                                </div>
                                <div class="form-group">
                                    <input type="submit" class="btn btn-primary" name="btSaveRep" value="แสดงความคิดเห็น" >
                                </div>
                            </form>
                        </div>
            <?php } ?>
                </div>
            </div>
<?php require('footer.php'); ?>
        </div>
        <script>
                                            $(document).ready(function() {
                                                $('#boardReplieForm').bootstrapValidator({//ตรวจสอบการกรอกแสดงความคิดเห็น
                                                    feedbackIcons: {
                                                        valid: 'glyphicon glyphicon-ok',
                                                        invalid: 'glyphicon glyphicon-remove',
                                                        validating: 'glyphicon glyphicon-refresh'
                                                    },
                                                    fields: {
                                                        board_detail: {
                                                            validators: {
                                                                notEmpty: {
                                                                    message: 'กรุณากรอกข้อความด้วย'
                                                                }
                                                            }
                                                        }
                                                    }
                                                });
                                            });
        </script> 
    </body>
</html>