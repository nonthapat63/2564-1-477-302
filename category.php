<?php
session_start();
require('check_admin.php');
require('bin/connectdb.php');
if(!empty($_POST['btOrderSave'])){//กดปุ่มบันทึการจัดเรียง
 foreach($_POST['cg_order'] as $cg_id => $cg_order){
  //Update การจัดเรียงลำดับของหมวดกระทู้
    mysqli_query($con, "UPDATE category SET cg_order='$cg_order' WHERE cg_id='$cg_id'");
 } 
}
if(!empty($_GET['del'])){//กดลบหมวดกระทู้
 mysql_query($con, 'DELETE FROM category  WHERE cg_id='.$_GET['del']);
 header('category.php');
}
//แสดงหมวดหมุ่กระทู้ทั้งหมดโดยเรียงตามลำดับ cg_order จากน้อยไปมาก
$rs_category= mysqli_query($con, "SELECT cg_id,cg_name FROM category ORDER BY cg_order ASC"); 
$chk_rows_category = mysqli_num_rows($rs_category);//นับจำนวนแถวของหมวดกระทู้
?>
<html>
    <head>
        <?php require('head.php'); ?>
        <title>จัดการหมวดกระทู้</title>
    </head>
    <body>
        <?php require('menu.php'); ?>
        <div class="container">
            <?php require('header.php'); ?>
            <div class="row ws-content">
             <div class="col-md-10 col-sm-10 col-md-offset-1 col-sm-offset-1">
               <form id="categoryForm" name="categoryForm" method="post" action="" >
                 <table class="table table-bordered table-hover">
                   <thead>
                     <tr>
                       <th colspan="4">
                       <div style="float:right">
                       <span class="btn btn-default" ><a href="category_add.php">เพิ่มหมวดกระทู้</a></span>
                       <input type="submit" name="btOrderSave" id="btOrderSave" class="btn btn-primary" value="บันทึกการจัดเรียง" >
                       </div>
                       </th>
                     </tr>
                     <tr>
                       <th style="text-align:center">ลำดับ</th>
                       <th>ชื่อหมวดกระทู้</th>
                       <th>เรียงลำดับ</th>
                       <th>จัดการ</th>
                     </tr>
                   </thead>
                   <tbody>
                   <?php if($chk_rows_category>0) {//จำนวนแถวมากกว่า 0 แสดงว่ามีข้อมูล
        $order_i=1;
        while($show_category=mysqli_fetch_assoc($rs_category)){
         $cg_id=$show_category['cg_id'];
        ?>
                     <tr>
                       <td style="width:10%;text-align:center"><?php echo  $order_i;?></td>
                       <td style="width:70%"><?php echo $show_category['cg_name'];?></td>
                       <td style="width:10%"><input type="text" name="cg_order[<?php echo  $cg_id;?>]"  class="form-control cg_order" value="<?php echo  $order_i;?>" ></td>
                       <td style="width:10%">
                       <a href="category_edit.php?edit=<?php echo  $cg_id;?>">แก้ไข</a> /
                       <a href="category.php?del=<?php echo  $cg_id;?>" onClick="return confirm('ยืนยันการลบหมวดกระทู้นี้')">ลบ</a></td>
                     </tr>
                     <?php 
      $order_i++;
        }
      }else{ //ไม่มีข้อมูลหมวดกระทู้
      ?>
                     <tr>
                       <td colspan="4" align="center">ไม่พบข้อมูล</td>
                     </tr>
                     <?php } ?>
                   </tbody>
                 </table>
               </form>
             </div>
            </div>
            <?php require('footer.php'); ?>
        </div>      
    </body>
</html>