<?php 
session_start ();
if (isset($_SESSION['id'])) {
    include('../database/dbcon.php');
    $id = $_SESSION['id'];
    $sql = mysqli_query($con, "SELECT * FROM Cart WHERE AccountID = '$id'");
    $row = mysqli_fetch_assoc($sql);
    $cartID = $row['CartID'];
    //initialise variable
    //thông tin khách hàng
    $fname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $payment = $_POST['payment'];  
    if ($payment == "cod") {          
        $payment1 = "COD";
        $status = "Đang Xử Lý";      
    }
    else {
        $payment1 = "Banking";
        $status = "Đã Hoàn Thành";
    }          
    $note = $_POST['note'];

    if(strlen($phone) > 10 || strlen($phone) < 10 || !preg_match('/^[0-9]+$/', $phone)){
        header("Location: ../user_cart/checkout.php?error=Số Điện Thoại Không Hợp Lệ");
        exit();
    } //check name
    elseif(strlen($fname) > 30){
        header("Location: ../user_cart/checkout.php?error=Tên Không Được Quá 30 Ký Tự");
        exit();
    } //check address
    elseif(strlen($address) > 200){
        header("Location: ../user_cart/checkout.php?error=Địa Chỉ Không Được Quá 200 Ký Tự");
        exit();
    } //check address
    elseif(strlen($note) > 5000){
        header("Location: ../user_cart/checkout.php?error=Ghi Chú Quá Dài");
        exit();
    } else {
        //tổng đơn
        $total = $_POST['total'];
        function generateRandomString($length = 10) {
            $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
            }
            return $randomString;
        }
        // tạo id random cho đơn hàng
        $orderID =  generateRandomString(6);

        // lưu dữ liệu vào bảng ORDER
        $order1 = mysqli_query($con, "INSERT INTO `Order` (`OrderID`,`AccountID`, `OrderStatus`, `PaymentStatus`,`TotalOrder`) VALUES
        ('$orderID', '$id', 'Chờ Xác Nhận', '$status', '$total')");
        // lưu dữ liệu vào bảng ORDER_DETAILS
        $order2 = mysqli_query($con, "INSERT INTO `Order_Details` 
        (`OrderID`, `CustomerName`, `CustomerAddress`,`CustomerPhoneNo`,`PaymentMethod`, `Note`) VALUES
        ('$orderID', '$fname', '$address', '$phone', '$payment1', '$note')");
        //thông tin sp
        $sql1 = mysqli_query($con, "SELECT p.ProductID AS ProductID, ProductName, Quantity, Price*Quantity AS QuantityPrice FROM product p
                                INNER JOIN Product_Cart pc ON p.ProductID = pc.ProductID
                                INNER JOIN Cart c ON pc.CartID = c.CartID
                                WHERE c.CartID = '$cartID' ORDER BY AddedDate DESC");
        $i = 1;
        // chạy vòng lặp để lưu dữ liệu vào bảng ORDER_PRODUCT                
        $count = count($_POST['productID']);
        for ($i=0; $i < $count; $i++){
            $productID = $_POST['productID'][$i];
            $quantity = $_POST['quantity'][$i]; 
            
            $quanPrice = $_POST['quantityPrice'][$i]; 
            $order3 = mysqli_query($con, "INSERT INTO `Order_Product` (`OrderID`, `ProductID`, `OrderQuantity`, `QuantityPrice`) VALUES
            ('$orderID', '$productID', '$quantity', '$quanPrice')");
            
            // tìm số lượng sp ms đc đặt mua và update
            $product = mysqli_query($con, "SELECT * FROM Product WHERE ProductID = '$productID'");
            $result = mysqli_fetch_assoc($product);
            $new_quantity =  $result['ProductQuantity']-$quantity; 
            $product_update = mysqli_query($con, "UPDATE Product SET ProductQuantity = '$new_quantity' WHERE ProductID = '$productID'");
            // xóa những sản phẩm đã mua khỏi giỏ hàng
        $cart_update = mysqli_query($con, "DELETE FROM Product_Cart WHERE ProductID = '$productID'");
        }
        
        echo "<script> window.location.href = 'my-order.php?success=Đặt Hàng Thành Công';</script>";
        exit();
    }
    
    
}else{
    echo "<script>window.open('../anon/homepage.php', '_self')</script>";
     exit();
}
?>