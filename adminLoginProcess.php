<?php 
session_start();
require_once "connection.php";

$email    = trim($_POST["e"] ?? "");
$password = trim($_POST["p"] ?? "");

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
    $query = "SELECT u.*, r.role_name 
              FROM `user` u 
              LEFT JOIN `user_role` r ON u.role_id = r.role_id 
              WHERE u.email = '" . addslashes($email) . "'";
              
    $rs = Database::search($query);

    if ($rs->num_rows > 0) {
        $user = $rs->fetch_assoc();

        if (isset($user["status_id"]) && $user["status_id"] != 1) {
            echo "Your account has been deactivated. Please contact support.";
            exit();
        }

        $role_id = (int)($user["role_id"] ?? 0);
        $role_name = strtolower($user["role_name"] ?? "");

        if ($role_id !== 1 && $role_name !== "admin") {
            echo "You don't have access. Please contact support.";
            exit();
        }

        $hashed_pwd = $user["password_hash"] ?? $user["password"] ?? "";

        if (password_verify($password, $hashed_pwd) || $password === $hashed_pwd) {
            $_SESSION["a"] = $user;
            $_SESSION["u"] = $user;
            echo "success";
        } else {
            echo "Invalid Password.";
        }
    } else {
        echo "User with this Email does not exist.";
    }
}
?>