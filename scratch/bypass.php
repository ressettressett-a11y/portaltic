<?php
session_start();
$_SESSION['admin'] = 'admin';
header('Location: ../admin.php');
exit;
