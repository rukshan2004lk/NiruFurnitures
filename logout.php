<?php
session_start();

if (isset($_SESSION["u"])) {
    $_SESSION["u"] = null;
}

if (isset($_SESSION["a"])) {
    $_SESSION["a"] = null;
}

session_destroy();

header("Location: login.php");
exit();
?>
