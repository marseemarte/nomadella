<?php
include 'conexion.php';
include 'verificar_admin.php';


// Búsqueda de reservas
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$where = $search ? "AND (u.nombre LIKE '%$search%' OR u.apellido LIKE '%$search%')" : "";

// Traemos las ordenes
$sql = "SELECT o.id_orden, o.fecha_orden, o.estado, o.total, u.nombre, u.apellido, oi.id_producto AS id_paquete
        FROM ordenes o
        JOIN usuarios u ON u.id_usuario = o.id_usuario
        JOIN orden_items oi ON oi.id_orden = o.id_orden AND oi.tipo_producto = 'paquete_turistico'
        WHERE 1 $where
        ORDER BY o.id_orden DESC
        LIMIT 20";

$reservas = $conn->query($sql);

if ($reservas && $reservas->num_rows > 0):
    foreach ($reservas as $r):
        $id_orden = $r['id_orden'];
        $id_paquete = $r['id_paquete'];

        // Traemos info general del paquete, incluyendo fechas
        $q = $conn->query("SELECT nombre, destino, precio_base, fecha_inicio, fecha_fin FROM paquetes_turisticos WHERE id_paquete = $id_paquete");
        $p = $q->fetch_assoc();
        if (!$p) {
            // Paquete no encontrado, saltar esta reserva
            continue;
        }
        $paquete_mostrar = "{$p['nombre']} ({$p['destino']})";
        $fecha_inicio = new DateTime($p['fecha_inicio']);
        $fecha_fin = new DateTime($p['fecha_fin']);
        $dias_viaje = $fecha_inicio->diff($fecha_fin)->days + 1;

        // Alojamientos
        $alojamientos = [];
        $total_alojamientos = 0;
        $q = $conn->query("SELECT a.nombre, a.ciudad, a.categoria, a.precio_dia
                           FROM paquete_alojamientos pa
                           JOIN alojamientos a ON pa.id_alojamiento = a.id_alojamiento
                           WHERE pa.id_paquete = $id_paquete");
        while ($a = $q->fetch_assoc()) {
            $total_aloj = $dias_viaje * $a['precio_dia'];
            $total_alojamientos += $total_aloj;
            $alojamientos[] = "{$a['nombre']} ({$a['ciudad']}, {$a['categoria']}⭐)<br>
                <small>Del " . $fecha_inicio->format('d/m/Y') . " al " . $fecha_fin->format('d/m/Y') . " ({$dias_viaje} días)</small>";
        }

        // Autos
        $autos = [];
        $total_autos = 0;
        $q = $conn->query("SELECT aa.proveedor, aa.tipo_vehiculo, aa.precio_por_dia
                           FROM paquete_autos pa
                           JOIN alquiler_autos aa ON pa.id_alquiler = aa.id_alquiler
                           WHERE pa.id_paquete = $id_paquete");
        while ($au = $q->fetch_assoc()) {
            $total_auto = $dias_viaje * $au['precio_por_dia'];
            $total_autos += $total_auto;
            $autos[] = "{$au['proveedor']} - {$au['tipo_vehiculo']}<br>
                <small>Del " . $fecha_inicio->format('d/m/Y') . " al " . $fecha_fin->format('d/m/Y') . " ({$dias_viaje} días)</small>";
        }

        // Vuelos
        $vuelos = [];
        $total_vuelos = 0;
        $q = $conn->query("SELECT v.aerolinea, v.origen, v.destino, v.precio_base
                           FROM paquete_vuelos pv
                           JOIN vuelos v ON pv.id_vuelo = v.id_vuelo
                           WHERE pv.id_paquete = $id_paquete");
        while ($v = $q->fetch_assoc()) {
            $total_vuelos += $v['precio_base'];
            $vuelos[] = "{$v['aerolinea']} ({$v['origen']} → {$v['destino']})";
        }

        // Servicios adicionales
        $servicios = [];
        $total_servicios = 0;
        $q = $conn->query("SELECT s.nombre, s.tipo, s.precio 
                           FROM paquete_servicios ps 
                           JOIN servicios_adicionales s ON ps.id_servicio = s.id_servicio 
                           WHERE ps.id_paquete = $id_paquete");
        while ($s = $q->fetch_assoc()) {
            $total_servicios += $s['precio'];
            $servicios[] = "{$s['nombre']} ({$s['tipo']})";
        }

        // Total general
        $total_reserva = $total_alojamientos + $total_autos + $total_vuelos + $total_servicios;
?>

        <div class="accordion-item">
            <h2 class="accordion-header" id="heading<?= $r['id_orden'] ?>">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $r['id_orden'] ?>">
                    <i class="bi bi-postcard"></i> Reserva #<?= $r['id_orden'] ?> - <?= htmlspecialchars($r['nombre']) ?> <?= htmlspecialchars($r['apellido']) ?> (<?= htmlspecialchars($paquete_mostrar) ?>)
                </button>
            </h2>
            <div id="collapse<?= $r['id_orden'] ?>" class="accordion-collapse collapse" data-bs-parent="#reservasAccordion">
                <div class="accordion-body">
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <div><strong>Fecha de orden:</strong> <?= date('d/m/Y', strtotime($r['fecha_orden'])) ?></div>
                            <div><strong>Fecha de inicio del viaje:</strong> <?= $fecha_inicio->format('d/m/Y') ?></div>
                            <div><strong>Fecha de fin del viaje:</strong> <?= $fecha_fin->format('d/m/Y') ?></div>
                            <div><strong>Días de viaje:</strong> <?= $dias_viaje ?></div>
                            <div><strong>Estado:</strong> <span class="text-danger fw-bold"><?= $r['estado'] ?></span></div>
                            
                        </div>
                        <div>
                            <a href="editar_reserva.php?id=<?= $r['id_orden'] ?>" class="btn btn-sm btn-primary me-2">
                                <i class="bi bi-pencil-square"></i> Editar
                            </a>
                        </div>
                        
                    </div>
                    <hr>
                    <table class="table table-sm">
                        <tr>
                            <td><strong><i class="bi bi-airplane-engines"></i> Vuelos</strong></td>
                            <td><?= $vuelos ? implode("<br>", $vuelos) : "---" ?></td>
                        </tr>
                        <tr>
                            <td><strong><i class="bi bi-buildings"></i> Alojamiento</strong></td>
                            <td><?= $alojamientos ? implode("<br>", $alojamientos) : "---" ?></td>
                        </tr>
                        <tr>
                            <td><strong><i class="bi bi-car-front-fill"></i> Alquiler de Auto</strong></td>
                            <td><?= $autos ? implode("<br>", $autos) : "---" ?></td>
                        </tr>
                        <tr>
                            <td><strong><i class="bi bi-backpack"></i> Servicios Adicionales</strong></td>
                            <td><?= $servicios ? implode("<br>", $servicios) : "---" ?></td>
                        </tr>
                        <tr class="table-danger fw-bold">
                            <td>Total</td>
                            <td>$<?= number_format($total_reserva, 2) ?></td>
                        </tr>
                    </table>

                </div>
            </div>
        </div>

    <?php endforeach;
else: ?>
    <div class="alert alert-warning">No se encontraron reservas.</div>
<?php endif;

$conn->close();
?>