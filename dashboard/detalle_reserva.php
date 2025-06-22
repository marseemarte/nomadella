<?php
include 'conexion.php';
include 'verificar_admin.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo '<div class="alert alert-danger">ID de reserva no válido.</div>';
    exit;
}

$id_orden = intval($_GET['id']);

$res = $conn->query("SELECT o.*, u.nombre, u.apellido FROM ordenes o JOIN usuarios u ON u.id_usuario = o.id_usuario WHERE o.id_orden = $id_orden");
if (!$res || $res->num_rows === 0) {
    echo '<div class="alert alert-warning">Reserva no encontrada.</div>';
    exit;
}
$reserva = $res->fetch_assoc();

// Buscar paquete asociado
$q = $conn->query("SELECT oi.id_producto FROM orden_items oi WHERE oi.id_orden = $id_orden AND oi.tipo_producto = 'paquete_turistico' LIMIT 1");
$id_paquete = $q && $q->num_rows > 0 ? $q->fetch_assoc()['id_producto'] : null;

if (!$id_paquete) {
    echo '<div class="alert alert-warning">No se encontró paquete vinculado.</div>';
    exit;
}

$pq = $conn->query("SELECT nombre, destino, fecha_inicio, fecha_fin FROM paquetes_turisticos WHERE id_paquete = $id_paquete");
$paq = $pq->fetch_assoc();
$fi = new DateTime($paq['fecha_inicio']);
$ff = new DateTime($paq['fecha_fin']);
$dias = $fi->diff($ff)->days + 1;

function traer($conn, $sql) {
    $res = $conn->query($sql);
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

$alojamientos = traer($conn, "SELECT a.nombre, a.ciudad, a.precio_dia FROM alojamientos a JOIN paquete_alojamientos pa ON a.id_alojamiento = pa.id_alojamiento WHERE pa.id_paquete = $id_paquete");
$autos = traer($conn, "SELECT aa.proveedor, aa.tipo_vehiculo, aa.precio_por_dia FROM alquiler_autos aa JOIN paquete_autos pa ON aa.id_alquiler = pa.id_alquiler WHERE pa.id_paquete = $id_paquete");
$vuelos = traer($conn, "SELECT v.aerolinea, v.origen, v.destino, v.precio_base FROM vuelos v JOIN paquete_vuelos pv ON v.id_vuelo = pv.id_vuelo WHERE pv.id_paquete = $id_paquete");
$servicios = traer($conn, "SELECT s.nombre, s.tipo, s.precio FROM servicios_adicionales s JOIN paquete_servicios ps ON s.id_servicio = ps.id_servicio WHERE ps.id_paquete = $id_paquete");

$total = 0;
$total += array_sum(array_map(fn($a) => $a['precio_dia'] * $dias, $alojamientos));
$total += array_sum(array_map(fn($a) => $a['precio_por_dia'] * $dias, $autos));
$total += array_sum(array_column($vuelos, 'precio_base'));
$total += array_sum(array_column($servicios, 'precio'));
?>

<h5>Cliente: <?= htmlspecialchars($reserva['nombre'] . ' ' . $reserva['apellido']) ?></h5>
<p><strong>Estado:</strong> <?= ucfirst($reserva['estado']) ?><br>
<strong>Fecha de orden:</strong> <?= date('d/m/Y', strtotime($reserva['fecha_orden'])) ?><br>
<strong>Destino:</strong> <?= htmlspecialchars($paq['destino']) ?><br>
<strong>Fechas del viaje:</strong> <?= $fi->format('d/m/Y') ?> al <?= $ff->format('d/m/Y') ?> (<?= $dias ?> días)</p>
<hr>
<h6>Vuelos</h6>
<ul>
    <?php foreach ($vuelos as $v): ?>
        <li><?= htmlspecialchars($v['aerolinea']) ?> (<?= htmlspecialchars($v['origen']) ?> &rarr; <?= htmlspecialchars($v['destino']) ?>) - $<?= number_format($v['precio_base'], 2) ?></li>
    <?php endforeach; ?>
</ul>
<h6>Alojamientos</h6>
<ul>
    <?php foreach ($alojamientos as $a): ?>
        <li><?= htmlspecialchars($a['nombre']) ?> (<?= $a['ciudad'] ?>) - $<?= number_format($a['precio_dia'] * $dias, 2) ?> (<?= $dias ?> días)</li>
    <?php endforeach; ?>
</ul>
<h6>Alquiler de Autos</h6>
<ul>
    <?php foreach ($autos as $a): ?>
        <li><?= htmlspecialchars($a['proveedor']) ?> - <?= $a['tipo_vehiculo'] ?> - $<?= number_format($a['precio_por_dia'] * $dias, 2) ?> (<?= $dias ?> días)</li>
    <?php endforeach; ?>
</ul>
<h6>Servicios Adicionales</h6>
<ul>
    <?php foreach ($servicios as $s): ?>
        <li><?= htmlspecialchars($s['nombre']) ?> (<?= $s['tipo'] ?>) - $<?= number_format($s['precio'], 2) ?></li>
    <?php endforeach; ?>
</ul>
<hr>
<p><strong>Total estimado:</strong> <span class="text-success fw-bold">$<?= number_format($total, 2) ?></span></p>
