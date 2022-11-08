<?php 
session_start ();
include('../database/dbcon.php');
include ('../header_footer/header.php');
$cartID = $_COOKIE['cart'];
?>
<!DOCTYPE html>
<html>

<head>
    <title>Thanh Toán</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/checkout.css?v=<?php echo time(); ?>">
</head>

<body>

    <h2>Thanh toán</h2>
    <div class="row">
        <div class="col-75">
            <div class="container">
                <form action="place-order.php" method="post">
                    <div class="row">
                        <div class="col-50">
                            <h8>Thông tin giao hàng</h8>
                            <label for="fname"><i class="fa fa-user"></i> Họ và tên <span
                                    style="color:red; display:inline;">*</span></label>
                            <input type="text" id="fname" name="fullname" placeholder="VD: Nguyễn Văn A" required>
                            <label for="phone"><i class="fa fa-phone"></i> Số điện thoại <span
                                    style="color:red; display:inline;">*</span></label>
                            <input type="tel" id="phone" name="phone" placeholder="VD: 0365222686" required>
                            <label for="adr"><i class="fa fa-address-card-o"></i> Địa chỉ <span
                                    style="color:red; display:inline;">*</span></label>
                            <input type="text" id="adr" name="address"
                                placeholder="Số nhà, đường , phường/xã, quận/huyện, tỉnh/thành phố" required><br>
                            <script>
                            function ShowHideDiv() {
                                var bank = document.getElementById("bank");
                                var bankBox = document.getElementById("bankBox");
                                bankBox.style.display = bank.checked ? "block" : "none";
                            }
                            </script>
                            <label for="payment"><i class="fa fa-money"></i> Hình Thức Thanh Toán <span
                                    style="color:red; display:inline;">*</span></label>
                            <div class="payment">
                                <div class="cod-col">
                                    <label for="cod"><input type="radio" name="payment" required value="cod" id="cod"
                                            onclick="ShowHideDiv()">
                                        COD</label>
                                </div>
                                <div class="bank-col"><label for="bank"><input type="radio" name="payment" value="bank"
                                            id="bank" onclick="ShowHideDiv()"> Banking</label>

                                    <div class="bankBox" id="bankBox" style="display: none">
                                        <span>Chuyển khoản tổng đơn hàng (bao gồm
                                            tiền sản phẩm + phí ship) qua tài khoản:
                                            <br>Chủ tài khoản: TRẦN THỊ THU HƯƠNG
                                            <br>STK: 19025793988668
                                            <br>Ngân hàng Techcombank
                                            (chi nhánh Hà Thành)
                                            <br>Nội dung chuyển khoản: Họ và Tên + SĐT
                                            + Mã Đơn Hàng</span>
                                    </div>
                                </div>
                            </div>

                            <label for="note"><i class="fa fa-pencil"></i> Ghi chú
                                <input type="text" id="note" name="note"
                                    placeholder="VD: Ship hàng vào giờ hành chính"><br>
                            </label>
                            <?php if (isset($_GET['error'])) { ?>
                            <p class="error1"><?php echo $_GET['error']; ?></p>
                            <?php } ?>
                        </div>

                        <div class="col-25">
                            <div class="container2">
                                <?php
                                // đếm số sp đc chọn
                                $count = count($_GET['choose_all']);
                                ?>
                                
                                <h8>Giỏ hàng <span class="price" style="color:black"><i class="fa fa-shopping-cart"></i>
                                        <b><?php echo $count ?></b></span></h8>
                                <?php 
                                $total = 0;
                                for ($i=0; $i < $count; $i++) { 
                                // find product ID
                                $productID = $_GET['choose_all'][$i];
                                $sql1 = mysqli_query($con, "SELECT p.CategoryID, p.ProductID, ProductName, Size, Quantity, Price FROM product p
                                INNER JOIN Anon_Cart_Product ac ON p.ProductID = ac.ProductID
                                WHERE ac.CartID = '$cartID' AND p.ProductID = '$productID'");
                                $row1 = mysqli_fetch_array($sql1);
                                $quantity = $_GET['quantity'][$i];
                                $quantityPrice = $row1['Price']*$quantity;
                                $total += $quantityPrice;
                                ?>

                                <!-- thông tin để truyền sang file place-order -->
                                <input type = "hidden" value = "<?php echo $row1['ProductID'] ?>" name = "productID[]">
                                <input type = "hidden" value = "<?php echo $quantity ?>" name = "quantity[]">
                                <input type = "hidden" value = "<?php echo $quantityPrice ?>" name = "quantityPrice[]">
                                <p><a href='view_product.php?cid=<?php echo $row1['CategoryID']; ?>'>
                                        <?php echo $row1['ProductName']; 
                                if($row1['Size'] != ''){
                                    ?> Size <?php echo $row1['Size'];
                                }
                                ?> * <?php echo $quantity ?></a> <span
                                            class="price"><?php echo number_format($quantityPrice) ?>đ</span></p>
                                <?php } 
                                $total += 30000;
                                ?>
                                <p>Phí Ship <span class="price">30,000đ</span></p>
                                <hr>
                                <p>Tổng thanh toán <span class="price"
                                        style="color:#707070"><b><?php echo number_format($total) ?>đ</b></span>
                                </p>
                                <input type = "hidden" value = "<?php echo $total ?>" name = "total">
                            </div>
                        </div>

                    </div>

                    <input type="submit" value="Thanh toán" class="btn">
                </form>
                <a href="view_cart.php"><input type="submit" value="Hủy" class="btn"></a>

                </form>
            </div>
        </div>

    </div>

</body>

</html>
