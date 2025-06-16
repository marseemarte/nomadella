<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /nomadella/public/login.php");
    exit;
}
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1,2])) {
    header("Location: /nomadella/dashboard/dashboard.php");
    exit;
}
?>