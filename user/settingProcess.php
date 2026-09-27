<?php
session_start();
include "../connection.php";

if (!isset($_SESSION["u"])) {
    echo "Please login first.";
    exit();
}

$email = $_SESSION["u"]["email"];

$fname  = $_POST["f"] ?? "";
$lname  = $_POST["l"] ?? "";
$line1  = $_POST["l1"] ?? "";
$line2  = $_POST["l2"] ?? "";
$city   = $_POST["c"] ?? "";
$pcode  = $_POST["pc"] ?? "";
$mobile = $_POST["m"] ?? "";

if (empty($fname)) {
    echo "Please Enter your First Name.";
} else if (strlen($fname) > 50) {
    echo "First Name must contain fewer than 50 characters.";
} else if (empty($lname)) {
    echo "Please Enter your Last Name.";
} else if (strlen($lname) > 50) {
    echo "Last Name must contain fewer than 50 characters.";
} else if (empty($line1)) {
    echo "Please Enter Address Line 1.";
} else if (empty($city)) {
    echo "Please Enter your City.";
} else if (empty($pcode)) {
    echo "Please Enter Postal Code.";
} else if (empty($mobile)) {
    echo "Please Enter your Mobile Number.";
} else if (strlen($mobile) != 10) {
    echo "Mobile Number must contain 10 characters.";
} else if (!preg_match("/^07[0,1,2,4,5,6,7,8][0-9]{7}$/", $mobile)) {
    echo "Invalid Mobile Number.";
} else {

    
    Database::iud("UPDATE `user` SET 
        `first_name` = '" . $fname . "',
        `last_name` = '" . $lname . "',
        `phone` = '" . $mobile . "',
        `line_1` = '" . $line1 . "',
        `line_2` = '" . $line2 . "',
        `city` = '" . $city . "',
        `postal_code` = '" . $pcode . "'
        WHERE `email` = '" . $email . "'");

    // Update active session data
    $_SESSION["u"]["first_name"] = $fname;
    $_SESSION["u"]["last_name"] = $lname;
    $_SESSION["u"]["phone"] = $mobile;

    echo "success";
}
?>