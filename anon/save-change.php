<?php
session_start();
    include('../database/dbcon.php');
    // tìm giỏ hàng tương ứng vs id ng dùng
    $cartID = $_COOKIE['cart'];
    // tìm xem có bao nhiêu cặp key và value trong array
    $sum = count($_POST['productID']);
    // chạy vòng lặp để update lại số lượng sản phẩm trong giỏ hàng
    for ($i=0; $i < $sum; $i++) {
        $productID = $_POST["productID"][$i];
        $quantity = $_POST["quantity"][$i];
        mysqli_query($con, "UPDATE Anon_Cart_Product SET Quantity = '$quantity' WHERE CartID = '$cartID' AND ProductID = '$productID'");
    }
    echo "<script>window.open('view_cart.php', '_self')</script>";

?>