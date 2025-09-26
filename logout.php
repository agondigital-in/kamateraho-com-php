<?php
session_start();
session_destroy();
header("Location: kamate raho/index.php");
exit;
?>