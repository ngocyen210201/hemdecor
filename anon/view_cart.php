<?php 
session_start ();
include ('../header_footer/header.php');
include('../database/dbcon.php');
$cartID = $_COOKIE['cart'];
$sql = mysqli_query($con, "SELECT * FROM Anon_Cart_Product WHERE CartID = '$cartID'");
$row = mysqli_fetch_assoc($sql);
$num = mysqli_num_rows($sql);

// xóa sp đã hết hàng ra khỏi giỏ hàng
$check_quantity = mysqli_query($con, "SELECT * FROM Product_Cart pc 
                                INNER JOIN Product p ON pc.ProductID = p.ProductID
                                WHERE ProductQuantity = 0");
$check_num = mysqli_num_rows($check_quantity);
if ($check_num == 1) {
    $check_result = mysqli_fetch_assoc($check_quantity);
    $out_of_stock_id = $check_result['ProductID'];
    mysqli_query($con, "DELETE FROM Product_Cart WHERE ProductID = '$out_of_stock_id'");
}
?>
<html>

<head>
    <title>Giỏ Hàng</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"
        integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/user.css?v=<?php echo time(); ?>">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>

<body>
    <div class="main">
        <div class="bar">
            <div class="bname">
                <h2><i class="fas fa-shopping-cart"></i> Giỏ Hàng</h2>
                <button type="button" class="save-change" id = "save">Lưu Thay Đổi</button>
            </div>
            <div class="border_bottom"></div>
            <div class = "warning">
                Vì bạn chưa đăng nhập (đăng ký) tài khoản, nên mọi dữ liệu sẽ được làm mới sau 7 ngày.
            </div>
        </div>
        <?php if ($num == 0) { ?>
        <div class="my-order_box">
            <div class = "message">
                Giỏ hàng rỗng
            </div>
            <div class="cbar">
                <a href="checkout.php"><button type="button" class="checkout1" disabled>Thanh Toán</button></a>
            </div>
        </div>
        <?php }else{ ?>
        <div class="my-order_box">
        <form action="save-change.php" method="post" enctype="multipart/form-data" id="myForm">
            <table width="100%">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="checkAll" value="" /></th>
                        <th>Sản Phẩm</th>
                        <th>Đơn Giá</th>
                        <th>Số Lượng</th>
                        <th>Thành Tiền</th>
                        <th>Xóa</th>
                    </tr>
                </thead>
                <?php
                    $sql1 = mysqli_query($con, "SELECT p.CategoryID, ThumbnailImage, p.*, CartID, Quantity FROM Categories c
                    INNER JOIN Product p ON c.CategoryID = p.CategoryID 
                    INNER JOIN Anon_Cart_Product ac ON p.ProductID = ac.ProductID
                    WHERE CartID = '$cartID' ORDER BY AddedDate DESC");
                    $num1 = mysqli_num_rows($sql1);
                    $i = 0;                
                    while ($row1 = mysqli_fetch_assoc($sql1)){
                        $total = $row1['Price']*$row1['Quantity'];
                        $product_quantity = $row1['ProductQuantity'];
                        $product_id = $row1['ProductID'];
                        //create img path
                        $img =  "../images/product_images/" . $row1['ThumbnailImage'];
                    ?>
                <tbody>
                    <input type = "hidden" id = "product_quantity<?php echo $i?>" value = "<?php echo $product_quantity ?>">
                    <input type = "hidden" id = "product_id<?php echo $i?>" value = "<?php echo $product_id ?>" name = "productID[]">
                    <tr>
                        <th class="check"><input type="checkbox" name="choose_all[]" id="check<?php echo $i?>"
                                value="<?php echo $row1['ProductID']; ?>" /></th>
                        <th class="sp">
                            <a href='view_product.php?cid=<?php echo $row1['CategoryID']; ?>'>
                                <div class="columna">
                                    <img src="<?php echo $img ?>">
                                </div>
                                <div class="columnb">
                                    <?php echo $row1['ProductName'];
                                if($row1['Size'] != ''){
                                    ?> Size <?php echo $row1['Size'];
                                    }
                                ?>
                                </div>

                            </a>
                        </th>
                        <th><a>
                                <?php echo number_format($row1['Price']);?>
                                <input id="price<?php echo $i?>" class="price" value="<?php echo $row1['Price'];?>"
                                    style="display: none;">
                            </a></th>
                        <th class="cnumber">
                            <span class="minus" id="minus<?php echo $i?>">-</span>
                            <input type="text" id="input<?php echo $i?>" class="input" name="quantity[]"
                                value="<?php echo $row1["Quantity"]?>" required 
                                onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 13" 
                                readonly/>
                            <span class="plus" id="plus<?php echo $i?>">+</span>
                        </th>
                        <th>
                            <a id="total<?php echo $i?>" >
                                <?php echo number_format($total);?>
                            </a>
                        </th>
                        <th><a href="view_cart.php?delete=<?php echo $row1['ProductID']; ?>">
                                <i class="fa fa-trash-o"></i>
                            </a>
                        </th>
                    </tr>
                </tbody>
                <?php $i++;} ?>
            </table>
        </div>
        <?php } ?>
    </div>
    <div class="cbar">
        <div class="box-container">
            <div class="box">
                <span>Tổng sản phẩm: <span id="total-p"> 0 </span> </span>
            </div>

            <div class="box">
                <span>Phí Ship(Mặc định): 30,000</span>
            </div>

            <div class="box">
                <span>Tổng Đơn: <span id="total-o"> 0 </span></span>
            </div>
        </div>
        <div class="cbar1">
            <button type="button" class="checkout1" id="checkout1" style="display: block;" disabled>Thanh Toán</button>
            <button type="button" class="checkout2" id="checkout2" style="display: none;">Thanh Toán</button>
        </div>
    </div>
    <?php
        if (isset($_GET['delete'])) {
            $delete = mysqli_query($con, "DELETE FROM Anon_Cart_Product WHERE ProductID = '$_GET[delete]' ");
            if ($delete) {
                echo "<script>window.open('view_cart.php', '_self')</script>";
            }
        }
        ?>
    <script>
    // submit form khi nút "Lưu thay đổi" đc bấm
    $("#save").click(function() {
        document.getElementById("myForm").submit();
    });

    // khi bấm nút "Thanh toán"
    $("#checkout2").click(function (e) { 
        // thay đổi action của form
        document.getElementById("myForm").action = "checkout.php";
        // thay đổi method của form
        document.getElementById("myForm").method = "get";
        // submit form
        document.getElementById("myForm").submit();
        e.preventDefault();
    });


    // plus function
    for (let index = 0; index < <?php echo $num?>; index++) {
        var plusID = "#plus" + index;
        $(plusID).click(function() {
            var quantityID = "#product_quantity" + index
            var p_quantity = $(quantityID).val();
            var num_input = parseInt($(this).prev().val());
            var num_input1 = num_input+1;
            if ((num_input1) <= p_quantity){
                $(this).prev().val(num_input + 1);
            }
            // giá sp
            var priceID = "#price" + index;
            // số lượng
            var inputID = "#input" + index;
            // thành tiền
            var totalID = "#total" + index;
            var price = parseInt($(priceID).val());
            var input = parseInt($(inputID).val());
            var total = (price * input).toLocaleString('en-US');
            $(totalID).text(total);
            var checkID = "#check" + index;
            if ($(checkID).prop('checked') && (num_input1) <= p_quantity) {
                // giá tổng sp
                var o_total = parseInt(($("#total-p").text()).replaceAll(',',''));
                // tính giá sp đc tích vào tổng giá
                var recalculate_total = price + o_total;
                var total_order = recalculate_total + 30000;
                // thay thế các giá trị vừa tính vào các ô tổng sản phẩm và tổng đơn
                $("#total-p").text((recalculate_total).toLocaleString('en-US'));
                $("#total-o").text((total_order).toLocaleString('en-US'));
            }
        });
    }

    // minus function
    for (let index = 0; index < <?php echo $num?>; index++) {
        var minusID = "#minus" + index;
        $(minusID).click(function() {
            var num_input = $(this).next().val();
            if (num_input > 0)
            $(this).next().val(+$(this).next().val() - 1);
            var checkID = "#check" + index;
            var priceID = "#price" + index;
            var inputID = "#input" + index;
            var totalID = "#total" + index;
            var productID = $(checkID).val();
            var price = parseInt($(priceID).val());
            var input = parseInt($(inputID).val());
            // nếu user giảm số sp về 0 thì tự xóa sp đấy khỏi giỏ hàng
            if (input == 0) {
                var url = `view_cart.php?delete=${productID}`;
                console.log(url);
                location.replace(url);
            }
            var total = (price * input).toLocaleString('en-US');
            $(totalID).text(total);
            var checkID = "#check" + index;
            if ($(checkID).prop('checked')) {
                // giá tổng sp
                var o_total = parseInt(($("#total-p").text()).replaceAll(',',''));
                // tính giá sp đc tích vào tổng giá
                var recalculate_total = o_total-price;
                var total_order = recalculate_total + 30000;
                // thay thế các giá trị vừa tính vào các ô tổng sản phẩm và tổng đơn
                $("#total-p").text((recalculate_total).toLocaleString('en-US'));
                $("#total-o").text((total_order).toLocaleString('en-US'));
            }
        });
    }

    // số checkbox đã đc check
    var checkboxNum = 0;
    var checkout1 = document.getElementById("checkout1");
    var checkout2 = document.getElementById("checkout2");
                
    // nếu ô check tất đc click
    $('#checkAll').click(function(event) {
        if (this.checked) {
            // Iterate each checkbox để check các ô
            $(':checkbox').each(function() {
                this.checked = true;
            });
            // số ô đc check = tổng số hàng trong bảng
            checkboxNum = <?php echo $num?>;
            checkout1.style.display = "none";
            checkout2.style.display = "block";
            // tổng giá sản phẩm ban đầu = 0 
            $("#total-p").text(0);
            for (let index = 0; index < <?php echo $num?>; index++) {
                var totalID = "#total" + index;
                
                // lấy value của các ô giá sản phẩm
                // giá sản phẩm
                var total_p = parseInt(($(totalID).text()).replaceAll(',',''));
                // tổng giá sản phẩm
                var o_total = parseInt(($("#total-p").text()).replaceAll(',',''));

                // giá sau khi cộng sản phẩm đc chọn
                var recalculate_total = total_p + o_total;
                // giá sau khi cộng phí ship
                var total_order = recalculate_total + 30000;
                
                // thay thế các giá trị vừa tính vào các ô tổng sản phẩm và tổng đơn
                $("#total-p").text((recalculate_total).toLocaleString('en-US'));
                $("#total-o").text((total_order).toLocaleString('en-US'));
            }
         } //nếu bỏ tích
        else {
            $(':checkbox').each(function() {
                this.checked = false;
            });
            // thay thế 0 vào các ô tổng sản phẩm và tổng đơn
            $("#total-p").text(0);
            $("#total-o").text(0);
            // số ô đc tích = 0
            checkboxNum = 0;
            checkout1.style.display = "block";
            checkout2.style.display = "none";
        }
    });

    // calculate the price of checked product
    price_calculate();
    function price_calculate() {  
        for (let index = 0; index < <?php echo $num?>; index++) {
            var checkID = "#check" + index;
            
            // nếu checkbox thay đổi
            $(checkID).change(function(e) {
                var totalID = "#total" + index;

                // giá sp
                var total_p = parseInt(($(totalID).text()).replaceAll(',',''));
                console.log(total_p);
                // giá tổng sp
                var o_total = parseInt(($("#total-p").text()).replaceAll(',',''));
                console.log(o_total);
                // nếu ô này đc tích
                if ($(this).prop('checked')) {
                    // tính giá sp đc tích vào tổng giá
                    var recalculate_total = total_p + o_total;
                    // số ô đc tích tăng thêm 1 
                    checkboxNum++;
                } //nếu ô này bị bỏ tích
                else {
                // tính lại giá sau khi bỏ tích
                    var recalculate_total = o_total - total_p;
                    // số ô đc tích trừ đi 1
                    checkboxNum--;
                }
                // tính tổng đơn 
                var total_order = recalculate_total + 30000;
                // nếu tổng sp = 0 thì tổng đơn cũng = 0

                if (recalculate_total == 0) {
                    total_order = 0;
                }

                
                // nếu số ô đc tích = số hàng trong bảng
                if (checkboxNum == <?php echo $num?>) {
                    // ô check hết sẽ đc tích
                    $('#checkAll').prop('checked', true);
                } else {
                    // bỏ tích ô check hết
                    $('#checkAll').prop('checked', false);
                }
                console.log(checkboxNum);
                if(checkboxNum > 0) {
                    // ẩn hiện nút thanh toán
                    checkout1.style.display = "none";
                    checkout2.style.display = "block";
                }else if(checkboxNum == 0){
                    checkout1.style.display = "block";
                    checkout2.style.display = "none";
                }
                // thay thế các giá trị vừa tính vào các ô tổng sản phẩm và tổng đơn
                $("#total-p").text((recalculate_total).toLocaleString('en-US'));
                $("#total-o").text((total_order).toLocaleString('en-US'));
                e.preventDefault();
            });
        }
    }
    </script>
</body>

</html>