<?php
include 'conexion.php';
include 'verificar_admin.php';

function mostrarDato($valor) {
    return ($valor === null || $valor === '') ? '<span class="text-muted fst-italic">No disponible</span>' : htmlspecialchars($valor);
}

if (!isset($_GET['id_orden']) || !is_numeric($_GET['id_orden'])) {
    exit('<div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill"></i> ID de reserva inválido.</div>');
}

$id_orden = intval($_GET['id_orden']);

// Datos de la orden
$res = $conn->query("SELECT o.*, u.nombre, u.apellido 
                    FROM ordenes o 
                    JOIN usuarios u ON u.id_usuario = o.id_usuario 
                    WHERE o.id_orden = $id_orden");

if (!$res || $res->num_rows === 0) {
    exit('<div class="alert alert-warning"><i class="bi bi-info-circle"></i> No se encontró la reserva.</div>');
}

$orden = $res->fetch_assoc();

// Traemos los items
$items = $conn->query("SELECT * FROM orden_items WHERE id_orden = $id_orden");

// Paquete turístico
$paquete = null;
foreach ($items as $item) {
    if ($item['tipo_producto'] === 'paquete_turistico') {
        $id_paquete = intval($item['id_producto']);
        $q = $conn->query("SELECT * FROM paquetes_turisticos WHERE id_paquete = $id_paquete");
        $paquete = $q->fetch_assoc();
        break;
    }
}
?>

<div class="mb-4">
    <h5 class="mb-3 text-primary"><i class="bi bi-card-list"></i> Datos de la Reserva</h5>
    <ul class="list-group shadow-sm">
        <li class="list-group-item"><i class="bi bi-hash"></i> <strong>ID Reserva:</strong> <?= $orden['id_orden'] ?></li>
        <li class="list-group-item"><i class="bi bi-person"></i> <strong>Cliente:</strong> <?= mostrarDato($orden['nombre'] . ' ' . $orden['apellido']) ?></li>
        <li class="list-group-item"><i class="bi bi-calendar-event"></i> <strong>Fecha:</strong> <?= $orden['fecha_orden'] ? date('d/m/Y', strtotime($orden['fecha_orden'])) : '<span class="text-muted fst-italic">No disponible</span>' ?></li>
        <li class="list-group-item"><i class="bi bi-flag"></i> <strong>Estado:</strong> <?= mostrarDato($orden['estado']) ?></li>
        <li class="list-group-item"><i class="bi bi-cash-coin"></i> <strong>Total:</strong> $<?= number_format($orden['total'], 2) ?></li>
        <li class="list-group-item"><i class="bi bi-credit-card"></i> <strong>Medio de Pago:</strong> <?= mostrarDato($orden['medio_pago']) ?></li>
        <li class="list-group-item"><i class="bi bi-receipt"></i> <strong>Datos de Facturación:</strong> <?= $orden['datos_facturacion'] ? nl2br(htmlspecialchars($orden['datos_facturacion'])) : '<span class="text-muted fst-italic">No disponible</span>' ?></li>
    </ul>
</div>

<?php if ($paquete): ?>
<div class="mb-4">
    <h5 class="mb-3 text-success"><i class="bi bi-box-seam"></i> Paquete Turístico</h5>
    <ul class="list-group shadow-sm">
        <li class="list-group-item"><i class="bi bi-box"></i> <strong>Nombre:</strong> <?= mostrarDato($paquete['nombre']) ?></li>
        <li class="list-group-item"><i class="bi bi-geo-alt"></i> <strong>Destino:</strong> <?= mostrarDato($paquete['destino']) ?></li>
        <li class="list-group-item"><i class="bi bi-calendar2-week"></i> <strong>Fecha de Inicio:</strong> <?= $paquete['fecha_inicio'] ? date('d/m/Y', strtotime($paquete['fecha_inicio'])) : '<span class="text-muted fst-italic">No disponible</span>' ?></li>
        <li class="list-group-item"><i class="bi bi-calendar2-week-fill"></i> <strong>Fecha de Fin:</strong> <?= $paquete['fecha_fin'] ? date('d/m/Y', strtotime($paquete['fecha_fin'])) : '<span class="text-muted fst-italic">No disponible</span>' ?></li>
        <li class="list-group-item"><i class="bi bi-currency-dollar"></i> <strong>Precio Base:</strong> $<?= number_format($paquete['precio_base'], 2) ?></li>
        <li class="list-group-item"><i class="bi bi-tags"></i> <strong>Tipo de Paquete:</strong> <?= mostrarDato($paquete['tipo_paquete']) ?></li>
    </ul>
</div>
<?php endif; ?>
