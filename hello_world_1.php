<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<?php     
        /*$txt1 = "I am ";  //string
        $age = 40;       //number
        $txt2 = " years old "; //string
        $_txt1 = "";
        $chk = true; 
        
        if ($chk) {
            //true
        } else {
            //false
        }*/

        //echo $txt1;  
        //echo $age;
        //echo $txt2;
        //echo $text1 . $age . $text2;

        $semester_Fee = 16000;
        $g_discount = 0.5;
        $u_discount = 0.2;
        
        $fee_after_u_discount = $semester_Fee * (1-$u_discount);
        echo "Tuition Fee after university discount (20%) " . $fee_after_u_discount . " baht";

        $fee_after_g_discount = $fee_after_u_discount * (1 - ($g_discount - $u_discount));
        echo "<div>Tuition Fee after government discount (30%) " . $fee_after_g_discount . " baht </div>";

        $discount = 0.2;
        
        $hello = "Hello \"Monday\"";
        echo "This is an example of string: $hello";
        echo "<br>";
        echo 'This is an example of string: ' . $hello; 


    ?>

    <!-- <div style="color:green">
        <h1>
            <?php echo "Hello world in PHP"; ?>
        </h1>
    </div> -->
</body>
</html>