<?php
session_start();

// Unset Admin Session
if (isset($_SESSION['a'])) {
    unset($_SESSION['a']);
}

header("Location: ../login.php");
exit();
?>
