<?php
include 'conexion.php';
include 'verificar_admin.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$etiqueta = isset($_GET['etiqueta']) ? intval($_GET['etiqueta']) : 0;
$destino = isset($_GET['destino']) ? intval($_GET['destino']) : 0;
$estado = isset($_GET['estado']) ? $_GET['estado'] : '';

$sql = "SELECT p.*, d.destino 
        FROM paquetes_turisticos p 
        LEFT JOIN destinos d ON p.id_destino = d.id_destino
        WHERE 1";
$params = [];

if ($q !== '') {
    $sql .= " AND (
        p.nombre LIKE ? OR
        SOUNDEX(p.nombre) = SOUNDEX(?) OR
        d.destino LIKE ? OR
        SOUNDEX(d.destino) = SOUNDEX(?)
    )";
    $params[] = "%$q%";
    $params[] = $q;
    $params[] = "%$q%";
    $params[] = $q;
}

if ($etiqueta) {
    $sql .= " AND EXISTS (SELECT 1 FROM paquete_etiquetas pe WHERE pe.id_paquete = p.id_paquete AND pe.id_etiqueta = ?)";
    $params[] = $etiqueta;
}

if ($destino) {
    $sql .= " AND p.id_destino = ?";
    $params[] = $destino;
}

if ($estado !== '') {
    $sql .= " AND p.activo = ?";
    $params[] = $estado;
}

$sql .= " ORDER BY p.fecha_inicio DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$paquetes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Traer etiquetas y componentes para cada paquete
foreach ($paquetes as &$paquete) {
    $id_paquete = $paquete['id_paquete'];

    // Etiquetas
    $stmt2 = $pdo->prepare("SELECT e.nombre FROM etiquetas e JOIN paquete_etiquetas pe ON e.id_etiqueta = pe.id_etiqueta WHERE pe.id_paquete = ?");
    $stmt2->execute([$id_paquete]);
    $paquete['etiquetas'] = $stmt2->fetchAll(PDO::FETCH_COLUMN);

    // Alojamientos
    $stmt2 = $pdo->prepare("SELECT a.nombre, a.direccion, a.ciudad, a.categoria FROM alojamientos a JOIN paquete_alojamientos pa ON a.id_alojamiento = pa.id_alojamiento WHERE pa.id_paquete = ?");
    $stmt2->execute([$id_paquete]);
    $paquete['alojamientos'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    // Autos
    $stmt2 = $pdo->prepare("SELECT aa.proveedor, aa.tipo_vehiculo, aa.precio_por_dia FROM alquiler_autos aa JOIN paquete_autos pa ON aa.id_alquiler = pa.id_alquiler WHERE pa.id_paquete = ?");
    $stmt2->execute([$id_paquete]);
    $paquete['autos'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    // Vuelos
    $stmt2 = $pdo->prepare("SELECT v.aerolinea, v.origen, v.destino, v.precio_base FROM vuelos v JOIN paquete_vuelos pv ON v.id_vuelo = pv.id_vuelo WHERE pv.id_paquete = ?");
    $stmt2->execute([$id_paquete]);
    $paquete['vuelos'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    // Servicios adicionales
    $stmt2 = $pdo->prepare("SELECT s.nombre, s.descripcion, s.precio FROM servicios_adicionales s JOIN paquete_servicios ps ON s.id_servicio = ps.id_servicio WHERE ps.id_paquete = ?");
    $stmt2->execute([$id_paquete]);
    $paquete['servicios'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
}
unset($paquete);

header('Content-Type: application/json');
echo json_encode($paquetes);