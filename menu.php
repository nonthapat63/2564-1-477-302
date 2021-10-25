<div class="navbar navbar-default navbar-fixed-top" role="navigation" style="background-color:#E1E1E1;">
    <div class="container">
        <div class="row">
 
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div class="navbar-collapse collapse">
 
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="index.php">Home</a></li>
 
                    <?php
                    if (empty($_SESSION['mem_id'])) {
                        ?>
                        <li><a href="login.php">เข้าสู่ระบบ</a></li>    
                        <li><a href="register.php">สมัครสมาชิก</a></li>   
                        <?php
                    } else {
                        ?>   
                        <li> <a href="#">ยินดีต้อนรับ : <b><?php echo $_SESSION['mem_name']; ?></b></a></li>
                        <?php
                        if ($_SESSION['mem_level'] == 1) {
                            ?>
                            <li> <a href="category.php">จัดการหมวดกระทู้</a></li>
                        <?php } ?>
                        <li><a href="logout.php">ออกจากระบบ</a></li>
                        <?php
                    }
                    ?>             
 
                </ul>
            </div>
        </div>
    </div>    
</div>