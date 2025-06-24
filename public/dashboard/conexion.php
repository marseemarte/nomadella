<?php
// Muestra errores de mysqli
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = "localhost";
$user = "root";
$pass = "";
$db   = "nomadella";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Error de conexión PDO: " . $e->getMessage());
}

function registrar_bitacora($pdo, $id_usuario, $accion, $descripcion) {
    try {
        $stmt = $pdo->prepare("INSERT INTO bitacora_sistema (id_usuario, accion, descripcion, fecha_hora) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$id_usuario, $accion, $descripcion]);
    } catch (PDOException $e) {
        echo "Error al registrar en bitácora: " . $e->getMessage();
    }
}
if (session_status() === PHP_SESSION_NONE) session_start();
//ruta para trabajar en local y hostear
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];

    $subcarpeta = '/nomadella';

    define('BASE_URL', $protocol . $host . $subcarpeta . '/');
}
?>
