<?php
session_start();
require '../conexion.php';
require __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dompdf\Dompdf;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    exit('No autenticado');
}

$id_usuario = $_SESSION['usuario_id'];
$nombreCompleto = $_POST['nombreCompleto'] ?? '';
$dniCuit = $_POST['dniCuit'] ?? '';
$medio_pago = $_POST['medioPago'] ?? '';
$direccionFacturacion = $_POST['direccionFacturacion'] ?? '';
$email_cliente = $_POST['email'] ?? null;

$res = $conexion->query("SELECT id_carrito FROM carritos WHERE id_usuario=$id_usuario AND estado='activo' ORDER BY id_carrito DESC LIMIT 1");
if (!$row = $res->fetch_assoc()) exit('No hay carrito activo');
$id_carrito = $row['id_carrito'];

// Obtener items
$items = [];
$total = 0;
$q = $conexion->query("SELECT * FROM carrito_items WHERE id_carrito=$id_carrito");
while($item = $q->fetch_assoc()) {
    $items[] = $item;
    $total += $item['subtotal'];
}

// Simular pago: solo se guarda la info relevante
$datos_facturacion = "Nombre: $nombreCompleto\nDNI/CUIT: $dniCuit\nDirección: $direccionFacturacion";

// Crear orden
$conexion->query("INSERT INTO ordenes (id_usuario, total, estado, medio_pago, datos_facturacion) VALUES ($id_usuario, $total, 'Confirmada', '$medio_pago', '$datos_facturacion')");
$id_orden = $conexion->insert_id;

// Pasar items a la orden
foreach($items as $item) {
    $conexion->query("INSERT INTO orden_items (id_orden, tipo_producto, id_producto, cantidad, precio_unitario, subtotal) VALUES ($id_orden, '{$item['tipo_producto']}', {$item['id_producto']}, {$item['cantidad']}, {$item['precio_unitario']}, {$item['subtotal']})");
    if ($item['tipo_producto'] === 'paquete_turistico') {
        $conexion->query("UPDATE paquetes_turisticos SET cupo_disponible = cupo_disponible - {$item['cantidad']} WHERE id_paquete = {$item['id_producto']}");
    }
}

// Vaciar carrito
$conexion->query("DELETE FROM carrito_items WHERE id_carrito=$id_carrito");
$conexion->query("UPDATE carritos SET estado='cerrado' WHERE id_carrito=$id_carrito");

// --- GENERAR PDF DEL TICKET ---
$ticketHtml = '
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; background: #fff; margin: 0; padding: 0; }
        .ticket { width: 220px; padding: 10px; border: 1px dashed #b84e6f; margin: auto; }
        .titulo { color: #b84e6f; font-size: 18px; text-align: center; font-weight: bold; }
        .subtitulo { text-align: center; font-size: 13px; }
        .detalle { margin-top: 10px; }
        .total { font-size: 13px; font-weight: bold; text-align: right; margin-top: 10px; }
        .gracias { color: #741d41; text-align: center; margin-top: 12px; font-size: 11px; }
        ul { padding-left: 15px; margin: 0; }
        li { margin-bottom: 2px; }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="titulo">Nomadella</div>
        <div class="subtitulo">Ticket de compra</div>
        <div class="subtitulo">'.date('d/m/Y H:i').'</div>
        <div class="detalle">
            <b>Detalle:</b>
            <ul>';
foreach($items as $item) {
    $nombre = "Paquete";
    if ($item['tipo_producto'] === 'paquete_turistico') {
        $p = $conexion->query("SELECT nombre FROM paquetes_turisticos WHERE id_paquete={$item['id_producto']}")->fetch_assoc();
        $nombre = $p ? $p['nombre'] : 'Paquete';
    }
    $ticketHtml .= "<li>$nombre x{$item['cantidad']} - $ {$item['subtotal']} USD</li>";
}
$ticketHtml .= '
            </ul>
        </div>
        <div class="total">TOTAL: $'.number_format($total,2).' USD</div>
        <div class="gracias">¡Gracias por confiar en Nomadella!<br>www.nomadella.com</div>
    </div>
</body>
</html>';

$dompdf = new Dompdf();
$dompdf->loadHtml($ticketHtml);
$dompdf->setPaper([0,0,226.77,425.19], 'portrait');
$dompdf->render();
$pdfdoc = $dompdf->output();

// --- ENVIAR MAIL REAL DESDE GMAIL ---
if ($email_cliente) {
    $asunto = "¡Gracias por tu compra en Nomadella!";
    $mensajeHtml = '
        <div style="font-family:Arial,sans-serif;background:#faf6f8;padding:24px;">
            <h2 style="color:#b84e6f;">¡Gracias por tu compra en Nomadella!</h2>
            <p>Este es el resumen de tu compra:<br>
            <b>Resumen:</b></p>
            <ul style="padding-left:18px;">';
    foreach($items as $item) {
        $nombre = "Paquete";
        if ($item['tipo_producto'] === 'paquete_turistico') {
            $p = $conexion->query("SELECT nombre FROM paquetes_turisticos WHERE id_paquete={$item['id_producto']}")->fetch_assoc();
            $nombre = $p ? $p['nombre'] : 'Paquete';
        }
        $mensajeHtml .= "<li><b>$nombre</b> x {$item['cantidad']} - Subtotal: $ {$item['subtotal']} USD</li>";
    }
    $mensajeHtml .= '</ul>
            <p style="font-size:1.1em;"><b>Total:</b> $' . $total . ' USD</p>
            <p style="color:#741d41;">¡Esperamos que disfrutes tu experiencia!</p>
        </div>';

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'nomadellaturismo@gmail.com'; // TU CORREO
        $mail->Password = 'ofsn sehc mvdz ucsa'; // ← Reemplaza esto con la contraseña generada en Gmail
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('nomadellaturismo@gmail.com', 'Nomadella Turismo');
        $mail->addAddress($email_cliente);

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $asunto;
        $mail->Body    = $mensajeHtml;

        $mail->send();
    } catch (Exception $e) {
        error_log("Error al enviar correo: {$mail->ErrorInfo}");
    }
}

$response = [
    'success' => true,
    'mensaje' => '<div style="font-family:Arial,sans-serif;background:#faf6f8;padding:24px;max-width:500px;margin:40px auto;border-radius:8px;">
        <h2 style="color:#b84e6f;">¡Gracias por tu compra!</h2>
        <p>Te enviamos un correo con el resumen de tu compra.<br>
        Si lo deseas, puedes descargar tu ticket aquí:</p>
        <a href="order/descargar_ticket.php?id_orden='.$id_orden.'" style="display:inline-block;padding:10px 18px;background:#b84e6f;color:#fff;text-decoration:none;border-radius:5px;font-weight:bold;margin-top:10px;">Descargar ticket PDF</a>
        <p style="color:#741d41;margin-top:18px;">¡Esperamos que disfrutes tu experiencia!</p>
        <a href="index.php" style="display:inline-block;margin-top:20px;padding:10px 18px;background:#b84e6f;color:#fff;text-decoration:none;border-radius:5px;font-weight:bold;">Volver al inicio</a>
    </div>'
];
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>
