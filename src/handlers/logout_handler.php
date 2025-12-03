<?php
// src/handlers/logout_handler.php
session_start();
session_destroy();
header("Location: ../../index.php");
exit();
?>