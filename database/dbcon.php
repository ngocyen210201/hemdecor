<?php
$sname= "localhost";
$uname= "root";
$password = "Ngocyen*2102";
$db_name = "hemdecor";
// $port = "3360";

$con = mysqli_connect($sname, $uname, $password, $db_name) or die ("cannot connect");
// $con = mysqli_connect($sname, $uname, $password, $db_name, $port) or die ("cannot connect");
?>