<?php
session_start();
require('bin/connectdb.php');
//แสดงหมวดหมุ่กระทู้ทั้งหมดโดยเรียงตามลำดับ cg_order จากน้อยไปมาก
$rs_category = mysqli_query($con, "SELECT * FROM category ORDER BY cg_order ASC");
$chk_rows_category = mysqli_num_rows($rs_category); //นับจำนวนแถวของหมวดกระทู้

?>
<html>
    <head>
        <?php require('head.php'); ?>
        <title>เว็บบอร์ด</title>
    </head>
    <body>
        <?php require('menu.php'); ?>
        <div class="container">
            <?php require('header.php'); ?>
            <div class="row ws-content">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>หมวดกระทู้</th><th class="hidden-xs">หัวข้อกระทู้</th>
                            <th class="hidden-xs">ความคิดเห็น</th></tr>
                    </thead>
                    <tbody>
                        <?php
                        //if ($rs_category > 0) {//จำนวนแถวมากกว่า 0 แสดงว่ามีข้อมูล
                            
                        if ($chk_rows_category > 0) {//จำนวนแถวมากกว่า 0 แสดงว่ามีข้อมูล
                            while ($show_category = mysqli_fetch_array($rs_category)) {
                                $cg_id = $show_category['cg_id'];
                                $cg_name = $show_category['cg_name'];
                                $cg_des = $show_category['cg_des'];
                                $cg_v = $show_category['cg_replie_totals'];
                                $cg_tp_total = $show_category['cg_topic_totals'];
                                ?>
                                <tr>
                                    <td style="width:80%">
                                        <a href="showboard.php?id=<?php echo $cg_id; ?>"><?php echo $cg_name; ?></a>
                                        <br /><?php echo $cg_des; ?>
                                    </td>
                                    <td style="width:10%" class="hidden-xs"><?php echo $cg_tp_total; ?></td>
                                    <td style="width:10%" class="hidden-xs"><?php echo $cg_v; ?></td>
                                </tr>
                                <?php
                            }
                        } else { //ไม่มีข้อมูลหมวดกระทู้
                            ?>
                            <tr>
                                <td colspan="3" align="center"><strong>ไม่พบข้อมูล</strong></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <?php require('footer.php'); ?>
        </div>      
    </body>
</html>