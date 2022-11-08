<?php
session_start ();
include('../database/dbcon.php');
//initialise variable
$cartID = $_COOKIE['cart'];
$cid = $_GET['cid'];
$pid = $_POST['pid'];
if(!isset($_POST['pid'])){
    echo "<script>
    window.open('view_product.php?cid=$cid&error=Vui lòng chọn Size', '_self')
    </script>";
} else {
    $sql = mysqli_query($con, "SELECT * FROM Anon_Cart WHERE CartID = '$cartID'");
    $row = mysqli_fetch_assoc($sql);
    $quantity = $_POST['quantity'];
    $sql1 = mysqli_query($con, "SELECT * FROM Product WHERE ProductID = '$pid'");
    $row1 = mysqli_fetch_assoc($sql1);
    $stock = $row1['ProductQuantity'];

    $sql2 = mysqli_query($con, "SELECT * FROM Anon_Cart_Product WHERE CartID = '$cartID' AND ProductID = '$pid'");
    $row2 = mysqli_fetch_assoc($sql2);
    $num = mysqli_num_rows($sql2);

    $sql3 = mysqli_query($con, "SELECT * FROM Categories WHERE CategoryID = '$cid'");
    $row3 = mysqli_fetch_assoc($sql3);

    if ($quantity>$stock) {
        echo "<script>
    window.open('view_product.php?cid=$cid&error=Vượt quá số lượng có sẵn của sản phẩm', '_self')
    </script>";
    } elseif ($quantity==0) {
        echo "<script>
    window.open('view_product.php?cid=$cid&error=Số lượng sản phẩm không hợp lệ', '_self')
    </script>";
    } elseif ($num == 1) {
        $cart_quantity = $row2['Quantity'];
        $total_quantity = $cart_quantity + $quantity;
        $allow = $stock - $cart_quantity;
        if ($cart_quantity == $stock) {
            echo "<script>
        window.open('view_product.php?cid=$cid&error=Vượt quá số lượng có sẵn của sản phẩm', '_self')
        </script>";
        } elseif ($total_quantity>$stock) {
            echo "<script>
        window.open('view_product.php?cid=$cid&error=Bạn đã có $cart_quantity sản phẩm trong giỏ hàng, bạn chỉ có thể thêm $allow sản phẩm', '_self')
        </script>";
        } elseif ($total_quantity<=$stock) {
            mysqli_query($con, "UPDATE Anon_Cart_Product SET Quantity = (Quantity+'$quantity') WHERE CartID = '$cartID' AND ProductID = '$pid'");
            echo "<script>window.open('view_product.php?cid=$cid&success=Đã Thêm Vào Giỏ Hàng', '_self')</script>";
        }
    } elseif ($num == 0) {
        $sql = mysqli_query($con, "SELECT * FROM Anon_Cart WHERE CartID = '$cartID'");
        $num = mysqli_num_rows($sql);
        if ($num == 0) {
            mysqli_query($con, "INSERT INTO Anon_Cart(`CartID`) VALUES('$cartID')");
        }
        mysqli_query($con, "INSERT INTO Anon_Cart_Product(`ProductID`,`CartID`,`Quantity`)  VALUES('$pid', '$cartID', '$quantity')");
        echo "<script>window.open('view_product.php?cid=$cid&success=Đã Thêm Vào Giỏ Hàng', '_self')</script>";
    }
}
?>
