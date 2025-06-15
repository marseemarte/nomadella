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
    $stmt = $pdo->prepare("INSERT INTO bitacora (id_usuario, accion, descripcion, fecha_hora) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$id_usuario, $accion, $descripcion]);
}
?>
