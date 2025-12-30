<?php
$host = "localhost";
$user = "farhanwa_farhanwahid"; // cPanel theke paoya user [cite: 2]
$pass = "01813102490F@"; // cPanel theke deya password [cite: 2]
$dbname = "farhanwa_user_sohayok"; // cPanel theke paoya db name [cite: 2]

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>