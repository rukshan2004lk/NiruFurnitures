<?php
session_start();
include "connection.php";

$fname    = $_POST["f"] ?? "";
$lname    = $_POST["l"] ?? "";
$email    = $_POST["e"] ?? "";
$mobile    = $_POST["n"] ?? "";
$password = $_POST["p"] ?? "";

if (empty($fname)) {
    echo "Please Enter your First Name.";
} else if (strlen($fname) > 50) {
    echo "First Name must contain LESS THAN 50 Characters.";
} else if (empty($lname)) {
    echo "Please Enter your Last Name.";
} else if (strlen($lname) > 50) {
    echo "Last Name must contain LESS THAN 50 Characters.";
} else if (empty($email)) {
    echo "Please Enter your Email.";
} else if (strlen($email) > 100) {
    echo "Email must contain LESS THAN 100 Characters.";
} else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid Email Address.";
} else if (empty($password)) {
    echo "Please Enter your Password.";
} else if (strlen($password) < 5 || strlen($password) > 20) {
    echo "Password must contain between 5 to 20 characters.";
}else if(empty($mobile)){
    echo("Please Enter your Mobile Number.");
}else if (strlen($mobile)!=10){
    echo("Mobile Number must contain 10 characters.");
}else if(!preg_match("/07[0,1,2,4,5,6,7,8]{1}[0-9]{7}/",$mobile)){
    echo("Invalid Mobile Number.");

    } else {
    // Check if email already exists
    $rs = Database::search("SELECT * FROM `user` WHERE `email`='" . $email . "'");
    $num = $rs->num_rows;

    if ($num > 0) {
        echo "User with the same Email Address already exists.";
    } else {
        $d = new DateTime();
        $tz = new DateTimeZone("Asia/Colombo");
        $d->setTimezone($tz);
        $date = $d->format("Y-m-d H:i:s");

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        Database::iud("INSERT INTO `user` (`first_name`, `last_name`, `email`, `phone`, `password_hash`, `created_at`, `status`) 
                       VALUES ('" . $fname . "', '" . $lname . "', '" . $email . "', '".$mobile."', '" . $hashed_password . "', '" . $date . "', 'active')");

        echo "success";
    }
}
?>