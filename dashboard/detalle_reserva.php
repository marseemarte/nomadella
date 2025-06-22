<?php
include 'conexion.php';
include 'verificar_admin.php';

// Detectar si es petición AJAX
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

// Verificar que se recibió un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    if ($isAjax) {
        echo '<div class="alert alert-danger">ID de reserva no válido.</div>';
        exit;
    } else {
        header('Location: dashboard.php');
        exit;
    }
}

$id_orden = intval($_GET['id']);

// Obtener información de la reserva
$res = $conn->query("SELECT o.*, u.nombre, u.apellido FROM ordenes o JOIN usuarios u ON u.id_usuario = o.id_usuario WHERE o.id_orden = $id_orden");
if (!$res || $res->num_rows === 0) {
    if ($isAjax) {
        echo '<div class="alert alert-warning">Reserva no encontrada.</div>';
        exit;
    } else {
        header('Location: dashboard.php');
        exit;
    }
}
$reserva = $res->fetch_assoc();

echo '<h5>Reserva #' . $id_orden . '</h5>';
echo '<div class="row">';
echo '<div class="col-md-6">';
echo '<p><strong>Cliente:</strong> ' . htmlspecialchars($reserva['nombre'] . ' ' . $reserva['apellido']) . '</p>';
echo '<p><strong>Estado:</strong> <span class="badge ' . (strtolower($reserva['estado']) == 'confirmada' ? 'bg-success' : (strtolower($reserva['estado']) == 'pendiente' ? 'bg-warning' : 'bg-danger')) . '">' . ucfirst($reserva['estado']) . '</span></p>';
echo '<p><strong>Fecha de orden:</strong> ' . date('d/m/Y H:i', strtotime($reserva['fecha_orden'])) . '</p>';
echo '<p><strong>Total:</strong> <span class="text-success fw-bold">$' . number_format($reserva['total'], 2) . '</span></p>';
echo '</div>';

// Buscar paquete asociado
$q = $conn->query("SELECT oi.id_producto FROM orden_items oi WHERE oi.id_orden = $id_orden AND oi.tipo_producto = 'paquete_turistico' LIMIT 1");
if ($q && $q->num_rows > 0) {
    $id_paquete = $q->fetch_assoc()['id_producto'];
    
    // Obtener información del paquete
    $pq = $conn->query("SELECT nombre, destino, fecha_inicio, fecha_fin FROM paquetes_turisticos WHERE id_paquete = $id_paquete");
    if ($pq && $pq->num_rows > 0) {
        $paq = $pq->fetch_assoc();
        
        echo '<div class="col-md-6">';
        echo '<p><strong>Paquete:</strong> ' . htmlspecialchars($paq['nombre']) . '</p>';
        echo '<p><strong>Destino:</strong> ' . htmlspecialchars($paq['destino']) . '</p>';
        
        try {
            $fi = new DateTime($paq['fecha_inicio']);
            $ff = new DateTime($paq['fecha_fin']);
            $dias = $fi->diff($ff)->days + 1;
            echo '<p><strong>Fechas del viaje:</strong> ' . $fi->format('d/m/Y') . ' al ' . $ff->format('d/m/Y') . ' (' . $dias . ' días)</p>';
        } catch (Exception $e) {
            echo '<p><strong>Fechas del viaje:</strong> No disponible</p>';
        }
        
        echo '</div>';
        echo '</div>'; // Cerrar row
        
        // Mostrar componentes del paquete
        echo '<hr>';
        echo '<h6>Componentes del Paquete</h6>';
        
        // Función auxiliar para ejecutar consultas
        function obtenerComponentes($conn, $sql) {
            $res = $conn->query($sql);
            return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        }
        
        // Alojamientos
        $alojamientos = obtenerComponentes($conn, "SELECT a.nombre, a.ciudad FROM alojamientos a JOIN paquete_alojamientos pa ON a.id_alojamiento = pa.id_alojamiento WHERE pa.id_paquete = $id_paquete");
        if (!empty($alojamientos)) {
            echo '<div class="mb-3">';
            echo '<strong>🏨 Alojamientos:</strong>';
            echo '<ul class="mb-0">';
            foreach ($alojamientos as $a) {
                echo '<li>' . htmlspecialchars($a['nombre']) . ' - ' . htmlspecialchars($a['ciudad']) . '</li>';
            }
            echo '</ul>';
            echo '</div>';
        }
        
        // Vuelos
        $vuelos = obtenerComponentes($conn, "SELECT v.aerolinea, v.origen, v.destino FROM vuelos v JOIN paquete_vuelos pv ON v.id_vuelo = pv.id_vuelo WHERE pv.id_paquete = $id_paquete");
        if (!empty($vuelos)) {
            echo '<div class="mb-3">';
            echo '<strong>✈️ Vuelos:</strong>';
            echo '<ul class="mb-0">';
            foreach ($vuelos as $v) {
                echo '<li>' . htmlspecialchars($v['aerolinea']) . ' (' . htmlspecialchars($v['origen'] ?? 'N/A') . ' → ' . htmlspecialchars($v['destino'] ?? 'N/A') . ')</li>';
            }
            echo '</ul>';
            echo '</div>';
        }
        
        // Autos
        $autos = obtenerComponentes($conn, "SELECT aa.proveedor, aa.tipo_vehiculo FROM alquiler_autos aa JOIN paquete_autos pa ON aa.id_alquiler = pa.id_alquiler WHERE pa.id_paquete = $id_paquete");
        if (!empty($autos)) {
            echo '<div class="mb-3">';
            echo '<strong>🚗 Alquiler de Autos:</strong>';
            echo '<ul class="mb-0">';
            foreach ($autos as $a) {
                echo '<li>' . htmlspecialchars($a['proveedor']) . ' - ' . htmlspecialchars($a['tipo_vehiculo'] ?? 'Vehículo estándar') . '</li>';
            }
            echo '</ul>';
            echo '</div>';
        }
        
        // Servicios adicionales
        $servicios = obtenerComponentes($conn, "SELECT s.nombre, s.descripcion FROM servicios_adicionales s JOIN paquete_servicios ps ON s.id_servicio = ps.id_servicio WHERE ps.id_paquete = $id_paquete");
        if (!empty($servicios)) {
            echo '<div class="mb-3">';
            echo '<strong>➕ Servicios Adicionales:</strong>';
            echo '<ul class="mb-0">';
            foreach ($servicios as $s) {
                echo '<li>' . htmlspecialchars($s['nombre']);
                if (!empty($s['descripcion'])) {
                    echo ' - ' . htmlspecialchars($s['descripcion']);
                }
                echo '</li>';
            }
            echo '</ul>';
            echo '</div>';
        }
    } else {
        echo '<div class="col-md-6">';
        echo '<p class="text-muted">Información del paquete no disponible</p>';
        echo '</div>';
        echo '</div>'; // Cerrar row
    }
} else {
    echo '<div class="col-md-6">';
    echo '<p class="text-muted">No hay paquete asociado a esta reserva</p>';
    echo '</div>';
    echo '</div>'; // Cerrar row
}

echo '</div>'; // Cerrar container-fluid
?>