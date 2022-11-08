<?php
session_start();
if (isset($_SESSION['id'])) {
    include('../database/dbcon.php');
    $id = $_SESSION['id'];
    // tìm giỏ hàng tương ứng vs id ng dùng
    $sql = mysqli_query($con, "SELECT * FROM Cart WHERE AccountID = '$id'");
    $row = mysqli_fetch_assoc($sql);
    $cartID = $row['CartID'];

    // tìm xem có bao nhiêu cặp key và value trong array
    $sum = count($_POST['productID']);
    // chạy vòng lặp để update lại số lượng sản phẩm trong giỏ hàng
    for ($i=0; $i < $sum; $i++) {
        $productID = $_POST["productID"][$i];
        $quantity = $_POST["quantity"][$i];
        mysqli_query($con, "UPDATE Product_Cart SET Quantity = '$quantity' WHERE CartID = '$cartID' AND ProductID = '$productID'");
    }
    echo "<script>window.open('view_cart.php', '_self')</script>";
} else{
    echo "<script>window.open('../anon/homepage.php', '_self')</script>";
     exit();
}
?>