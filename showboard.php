<?php
session_start();
require('bin/connectdb.php');
$show_board = '';
$chk_rows_board = 0;
$rs_board = '';
if (isset($_GET['delID']) && isset($_GET['cg_id'])) {//ต้องการลบกระทู้
    require('check_admin.php'); //ตรวจสอบว่าเป็นadminกดลบหรือป่าว ถ้าไม่ใช่ เราจะไม่ให้ลบกระทู้ได้
    $id = $_GET['delID'];
    $cg_id = $_GET['cg_id'];
    mysqli_query($con, 'DELETE FROM board WHERE board_id=' . $id); //ลบกระทู้หลัก
    mysqli_query($con, 'DELETE FROM board WHERE board_parent_id=' . $id); //ลบความคิดเห็นทั้งหมดในกระทู้
    header('Location:showboard.php?id=' . $cg_id);
    exit();
}
if (isset($_GET['id'])) {
    $rs_cg = mysqli_query($con, 'SELECT cg_name,cg_id FROM category WHERE cg_id=' . $_GET['id']); //นั
    $show_board = mysqli_fetch_assoc($rs_cg); //นับจำนวนแถวของหมวดกระทู้
    if (isset($show_board['cg_name'])) {//ถ้าชื่อหมวดไม่เป็นค่าว่างแสดงว่ามีหมวดนี้อยู่ในฐานข้อมูลจริงๆ
        // Join 2 เทเบิล board และ member  เพื่อดึงค่าของกระทู้,ข้อมูลของสมาชิกมาแสดง 
        //โดยเรียงตามข้อมูลของกระทู้ที่อัพเดทล่าสุด (board_time_update)
        $rs_board = mysqli_query($con, "SELECT b.board_id,b.board_topic,b.board_views,b.board_replies,m.mem_name,m.mem_id
 FROM board As b LEFT JOIN member As m ON b.mem_id=m.mem_id
  WHERE b.cg_id='" . $_GET['id'] . "' AND b.board_parent_id=0 
 ORDER BY b.board_time_update DESC");
        $chk_rows_board = mysqli_num_rows($rs_board); //นับจำนวนแถวของกระทู้
    } else {//ถ้าเป็นค่าว่าง แสดงว่าไม่มีหมวดนี้อยู่ในฐานข้อมูล ให้Redirectไปหน้า index.php
        header('Location:index.php');
    }
} else {//ไม่พบพารามิเตอร์ $_GET['id'] .ให้กลับไปหน้าแรก
    header('Location:index.php');
    exit();
}
?>
<html>
    <head>
        <?php require('head.php'); ?>
        <title><?php echo $show_board['cg_name']; ?></title>
    </head>
    <body>
        <?php require('menu.php'); ?>
        <div class="container">
            <?php require('header.php'); ?>
            <div class="row ws-content">
                <ol class="breadcrumb">
                    <li><a href="index.php">Home</a></li>
                    <li class="active"><?php echo $show_board['cg_name']; ?></li>
                </ol>
                <h1><?php echo $show_board['cg_name']; ?></h1>
                <table class="table table-bordered table-hover">
                    <thead>
                        <?php if (!empty($_SESSION['mem_id'])) { ?>
                            <tr>
                                <th colspan="3"><span class="btn btn-default" ><a href="board_add.php?id=<?php echo $_GET['id'] ?>">ตั้งกระทู้</a></span></th>
                            </tr>
                        <?php } ?>
                        <tr>
                            <th>หัวข้อกระทู้</th><th class="hidden-xs">ความคิดเห็น</th><th class="hidden-xs">เข้าชม</th></tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($chk_rows_board > 0) {//จำนวนแถวมากกว่า 0 แสดงว่ามีข้อมูล
                            while ($show_board = mysqli_fetch_assoc($rs_board)) {
                                $board_id = $show_board['board_id'];
                                $mem_id = $show_board['mem_id'];
                                $mem_name = $show_board['mem_name'];
                                $board_topic = $show_board['board_topic'];
                                $board_views = $show_board['board_views'];
                                $board_replies = $show_board['board_replies'];
                                ?>
                                <tr>
                                    <td style="width:80%">
                                        <a href="viewboard.php?id=<?php echo $board_id; ?>"><?php echo $board_topic; ?></a>
                                        <br />
                                        โพสโดย : <?php echo $mem_name; ?>
 
                                        <?php
                                        if (isset($_SESSION['mem_id'])) {
                                            if ($_SESSION['mem_level'] == 1 || $mem_id == $_SESSION['mem_id']) {
                                                ?>
                                                (<a href="board_edit.php?id=<?php echo $board_id; ?>&cg_id=<?php echo $_GET['id'] ?>">แก้ไข</a>
                                                <?php if ($_SESSION['mem_level'] == 1) {//ลบได้เฉพาะ admin เท่านั้น?>
                                                    /
                                                    <a href="showboard.php?delID=<?php echo $board_id; ?>&cg_id=<?php echo $_GET['id'] ?>" onClick="return confirm('ยืนยันการลบข้อมูล')">ลบ</a>
                                                <?php } ?>)
                                                <?php }
                                        }
                                        ?>
 
                                    </td>
                                    <td style="width:10%" class="hidden-xs"><?php echo $board_replies; ?></td>
                                    <td style="width:10%" class="hidden-xs"><?php echo $board_views; ?></td>
                                </tr>
                                <?php
                            }
                        } else { //ไม่มีข้อมูลหมวดกระทู้
                            ?>
                            <tr>
                                <td colspan="3" align="center"><strong>ไม่พบกระทู้</strong></td>
                            </tr>
<?php } ?>
                    </tbody>
                </table>
            </div>
<?php require('footer.php'); ?>
        </div>      
    </body>
</html>