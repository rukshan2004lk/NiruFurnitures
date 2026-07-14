<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
$cols = dbFetchAll("SHOW COLUMNS FROM users");
print_r($cols);
