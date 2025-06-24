<?php
include './conexion.php';

if (!isset($_POST['id_paquete'])) {
  echo "Error: No se recibió el paquete.";
  exit;
}

$id_paquete = intval($_POST['id_paquete']);

// Buscamos el paquete principal
$stmt = $pdo->prepare("SELECT * FROM paquetes_turisticos WHERE id_paquete = ?");
$stmt->execute([$id_paquete]);
$paquete = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$paquete) {
  echo "No se encontró el paquete.";
  exit;
}

// Alojamiento
$stmt = $pdo->prepare("SELECT a.nombre, a.ciudad, a.categoria FROM alojamientos a
  JOIN paquete_alojamientos pa ON pa.id_alojamiento = a.id_alojamiento WHERE pa.id_paquete = ?");
$stmt->execute([$id_paquete]);
$alojamientos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Auto
$stmt = $pdo->prepare("SELECT aa.proveedor, aa.tipo_vehiculo, aa.precio_por_dia FROM alquiler_autos aa
  JOIN paquete_autos pa ON pa.id_alquiler = aa.id_alquiler WHERE pa.id_paquete = ?");
$stmt->execute([$id_paquete]);
$autos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Vuelos
$stmt = $pdo->prepare("SELECT v.aerolinea, v.origen, v.destino, v.precio_base FROM vuelos v
  JOIN paquete_vuelos pv ON pv.id_vuelo = v.id_vuelo WHERE pv.id_paquete = ?");
$stmt->execute([$id_paquete]);
$vuelos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Servicios adicionales
$stmt = $pdo->prepare("SELECT s.nombre, s.tipo, s.precio FROM servicios_adicionales s
  JOIN paquete_servicios ps ON ps.id_servicio = s.id_servicio WHERE ps.id_paquete = ?");
$stmt->execute([$id_paquete]);
$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mostramos:
echo "<h4 style='color:#750D37;'>".$paquete['nombre']."</h4>";
echo "<p>".$paquete['descripcion']."</p>";

echo "<h6>Alojamiento:</h6>";
if (count($alojamientos)) {
  foreach ($alojamientos as $a) {
    echo "<p>🏨 {$a['nombre']} ({$a['ciudad']}, {$a['categoria']} estrellas)</p>";
  }
} else { echo "<p>--- Sin alojamiento ---</p>"; }

echo "<h6>Alquiler de Auto:</h6>";
if (count($autos)) {
  foreach ($autos as $a) {
    echo "<p>🚗 {$a['proveedor']} - {$a['tipo_vehiculo']} - $".number_format($a['precio_por_dia'], 2)."/día</p>";
  }
} else { echo "<p>--- Sin alquiler de auto ---</p>"; }

echo "<h6>Vuelos:</h6>";
if (count($vuelos)) {
  foreach ($vuelos as $v) {
    echo "<p>✈ {$v['aerolinea']} ({$v['origen']} → {$v['destino']}) - $".number_format($v['precio_base'], 2)."</p>";
  }
} else { echo "<p>--- Sin vuelos ---</p>"; }

echo "<h6>Servicios Adicionales:</h6>";
if (count($servicios)) {
  foreach ($servicios as $s) {
    echo "<p>➕ {$s['nombre']} ({$s['tipo']}) - $".number_format($s['precio'], 2)."</p>";
  }
} else { echo "<p>--- Sin servicios adicionales ---</p>"; }
?>
