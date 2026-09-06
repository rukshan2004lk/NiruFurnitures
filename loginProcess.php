<?php 
session_start();
require_once "connection.php";

$email      = trim($_POST["e"] ?? $_POST["email"] ?? "");
$password   = trim($_POST["p"] ?? $_POST["password"] ?? "");
$rememberme = $_POST["r"] ?? $_POST["rememberme"] ?? "false";

if (empty($email)) {
    echo "Please Enter your Email.";
} else if (strlen($email) > 100) {
    echo "Email must contain Less Than 100 Characters.";
} else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid Email Address.";
} else if (empty($password)) {
    echo "Please Enter your Password.";
} else if (strlen($password) < 5 || strlen($password) > 20) {
    echo "Password must contain between 5 to 20 characters.";
} else {
    $rs = Database::search("SELECT * FROM `user` WHERE `email`='" . addslashes($email) . "'");
    
    if ($rs && $rs->num_rows > 0) {
        $user = $rs->fetch_assoc();

        if (isset($user["status_id"]) && (int)$user["status_id"] !== 1) {
            echo "Your account has been deactivated. Please contact support.";
            exit();
        }

        $hash = $user["password_hash"] ?? $user["password"] ?? "";

        if (password_verify($password, $hash) || $password === $hash) {
            $_SESSION["u"] = $user;
            if ((int)($user["role_id"] ?? 0) === 1) {
                $_SESSION["a"] = $user;
            }

            if ($rememberme === "true" || $rememberme === true || $rememberme === "1") {
                setcookie("email", $email, time() + (86400 * 30), "/");
                setcookie("password", $password, time() + (86400 * 30), "/");
            } else {
                setcookie("email", "", time() - 3600, "/");
                setcookie("password", "", time() - 3600, "/");
            }

            echo "success";
        } else {
            echo "Invalid Password.";
        }
    } else {
        echo "User with this Email does not exist.";
    }
}
?>