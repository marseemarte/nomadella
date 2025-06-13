<?php

session_start();
require '../conexion.php';
require __DIR__ . '/../../vendor/autoload.php';
use Dompdf\Dompdf;

// Seguridad: solo usuarios autenticados
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    exit('No autenticado');
}

$id_orden = intval($_GET['id_orden'] ?? 0);
$id_usuario = $_SESSION['usuario_id'];

// Verifica que la orden pertenezca al usuario
$res = $conexion->query("SELECT * FROM ordenes WHERE id_orden=$id_orden AND id_usuario=$id_usuario");
if (!$orden = $res->fetch_assoc()) exit('Orden no encontrada');

// Obtén los items de la orden
$items = [];
$total = 0;
$q = $conexion->query("SELECT * FROM orden_items WHERE id_orden=$id_orden");
while($item = $q->fetch_assoc()) {
    $nombre = "Paquete";
    if ($item['tipo_producto'] === 'paquete_turistico') {
        $p = $conexion->query("SELECT nombre FROM paquetes_turisticos WHERE id_paquete={$item['id_producto']}")->fetch_assoc();
        $nombre = $p ? $p['nombre'] : 'Paquete';
    }
    $item['nombre'] = $nombre;
    $items[] = $item;
    $total += $item['subtotal'];
}

// --- Generar HTML del ticket ---
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
        <div class="subtitulo">'.date('d/m/Y H:i', strtotime($orden['fecha'] ?? 'now')).'</div>
        <div class="detalle">
            <b>Detalle:</b>
            <ul>';
foreach($items as $item) {
    $ticketHtml .= "<li>{$item['nombre']} x{$item['cantidad']} - $ {$item['subtotal']} USD</li>";
}
$ticketHtml .= '
            </ul>
        </div>
        <div class="total">TOTAL: $'.number_format($total,2).' USD</div>
        <div class="gracias">¡Gracias por confiar en Nomadella!<br>www.nomadella.com</div>
    </div>
</body>
</html>';

// --- Generar y enviar el PDF ---
$dompdf = new Dompdf();
$dompdf->loadHtml($ticketHtml);
$dompdf->setPaper([0,0,226.77,425.19], 'portrait');
$dompdf->render();

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="ticket_nomadella.pdf"');
echo $dompdf->output();
exit;