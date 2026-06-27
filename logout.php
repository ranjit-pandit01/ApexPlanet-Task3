<?php
session_start();
session_destroy(); // सारे सेशन डिलीट करें
header("Location: login.php"); // वापस लॉगिन पेज पर भेजें
exit();
?>
