<?php
session_start();

// Unset Admin Session
if (isset($_SESSION['a'])) {
    unset($_SESSION['a']);
}

// Redirect to root login or admin login
header("Location: ../login.php");
exit();
?>