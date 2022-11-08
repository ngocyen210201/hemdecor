<?php
    session_start ();
    include('../database/dbcon.php');
    // nếu cookie chưa đc tạo
    if (!isset($_COOKIE["cart"])) {
        // tạo random 1 id cho cart của người dùng vô danh
        $cartID = "anon" . rand(00000, 99999);
        $sql = mysqli_query($con, "SELECT * FROM Anon_Cart WHERE CartID = '$cartID'");
        // kiểm tra xem nếu random cartID thì có bị trùng vs id đã xuất hiện trong db ko
        $num = mysqli_num_rows($sql);
        // nếu có thì sẽ random 1 kết quả mới
        while ($num == 1) {
            $cartID = "anon" . rand(00000, 99999);
            $sql1 = mysqli_query($con, "SELECT * FROM Anon_Cart WHERE CartID = '$cartID'");
            $num1 = mysqli_num_rows($sql1);
            // sau khi random id mới, nếu ko trùng thì break khỏi vòng lặp
            if ($num1 ==0) {
                break;
            }
        }
        // tạo cookie và lưu vào array giá trị id đã tạo
        // set thời hạn là 8 ngày vì cần giữ giá trị để xóa sau 7 ngày
        setcookie("cart", $cartID, time() + (86400 * 8), "/");
           
        // add id vào db
        mysqli_query($con, "INSERT INTO Anon_Cart(`CartID`) VALUES('$cartID')");
        $row = mysqli_fetch_assoc($sql);
        // tách dãy số random ra khỏi cartID để tạo 1 id tạm cho anon(sẽ đc sử dụng sau)
        $ex = explode('anon', $cartID);
        $id = 0 .$ex[1];
        // lưu id vào session
        $_SESSION['anonID'] = "$id";
    } // nếu cookie đã đc tạo
    else{
        // lấy cookie từ kho lưu trữ
        $cartID = $_COOKIE["cart"];
        // setcookie("cart", $cartID, time()-(60*60*24*7));
        $sql = mysqli_query($con, "SELECT * FROM Anon_Cart WHERE CartID = '$cartID'");
        $num = mysqli_num_rows($sql);
        $row = mysqli_fetch_assoc($sql);
        // tách dãy số random ra khỏi cartID để tạo 1 id tạm cho anon(sẽ đc sử dụng sau)
        $ex = explode('anon', $cartID);
        $id = 0 .$ex[1];
        // lưu id vào session
        $_SESSION['anonID'] = "$id";

        if ($num == 1) {
            // lấy ra thời gian cartID đc lưu vào db
            $time = $row['CreatedDate'];
            // tính thời gian cookie sẽ hết hạn 
            $date=strtotime("$time +1 week");
            $duration = date("Y-m-d h:i:sa", $date);

            // set timezone sang VN
            date_default_timezone_set('Asia/Ho_Chi_Minh');
            // tìm thời gian hiện tại của hệ thống
            $today = date("Y-m-d h:i:sa");

            // nếu tgian hiện tại = tgian hết hạn, xóa các dữ liệu liên quan đến cookie khỏi db
            if ($today >= $duration) {
                mysqli_query($con, "DELETE FROM Anon_Cart WHERE CartID = '$cartID'");

                // kiểm tra xem ng sử dụng cookie này đã đặt hàng chưa
                $sql2 = mysqli_query($con, "SELECT * FROM 'ORDER' WHERE AccountID = '$id'");
                $check_order = mysqli_num_rows($sql2);
                if ($check_order == 1) {
                    mysqli_query($con, "DELETE FROM 'Order' WHERE AccountID = '$id'");
                }
            }
        } else{
            $sql1 = mysqli_query($con, "INSERT INTO Anon_Cart(`CartID`) VALUES('$cartID')");
            // lấy ra thời gian cartID đc lưu vào db
            $time = $row['CreatedDate'];
            // tính thời gian cookie sẽ hết hạn 
            $date=strtotime("$time +1 week");
            $duration = date("Y-m-d h:i:sa", $date);

            // set timezone sang VN
            date_default_timezone_set('Asia/Ho_Chi_Minh');
            // tìm thời gian hiện tại của hệ thống
            $today = date("Y-m-d h:i:sa");

            // nếu tgian hiện tại = tgian hết hạn, xóa các dữ liệu liên quan đến cookie khỏi db
            if ($today >= $duration) {
                mysqli_query($con, "DELETE FROM Anon_Cart WHERE CartID = '$cartID'");

                // kiểm tra xem ng sử dụng cookie này đã đặt hàng chưa
                $sql2 = mysqli_query($con, "SELECT * FROM 'ORDER' WHERE AccountID = '$id'");
                $check_order = mysqli_num_rows($sql2);
                if ($check_order == 1) {
                    mysqli_query($con, "DELETE FROM 'Order' WHERE AccountID = '$id'");
                }
            }
        }
        
    }
?>