<?php
// filepath: c:\xampp\htdocs\nomadella\dashboard\reservas_ajax.php
include 'conexion.php';

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$where = $search ? "WHERE u.nombre LIKE '%$search%' OR pt.destino LIKE '%$search%'" : "";

$sql = "SELECT o.id_orden, o.fecha_orden, o.estado, u.nombre, u.apellido, pt.destino, oi.cantidad
    FROM ordenes o
    JOIN usuarios u ON u.id_usuario = o.id_usuario
    JOIN orden_items oi ON oi.id_orden = o.id_orden
    JOIN paquetes_turisticos pt ON pt.id_paquete = oi.id_producto
    $where
    ORDER BY o.id_orden DESC
    LIMIT 20";
$reservas = $conn->query($sql);

if ($reservas && $reservas->num_rows > 0):
    foreach ($reservas as $r): ?>
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading<?= $r['id_orden'] ?>">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $r['id_orden'] ?>">
                    <i class="bi bi-airplane me-2"></i> Reserva #<?= $r['id_orden'] ?> - <?= htmlspecialchars($r['nombre']) ?> <?= htmlspecialchars($r['apellido']) ?> (<?= htmlspecialchars($r['destino']) ?>)
                </button>
            </h2>
            <div id="collapse<?= $r['id_orden'] ?>" class="accordion-collapse collapse" data-bs-parent="#reservasAccordion">
                <div class="accordion-body">
                    <div><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($r['fecha_orden'])) ?></div>
                    <div><strong>Pasajeros:</strong> <?= $r['cantidad'] ?></div>
                    <div><strong>Estado:</strong> <span class="text-danger fw-bold"><?= $r['estado'] ?></span></div>
                    <hr>
                    <div><strong>Tarifa detallada:</strong></div>
                    <table class="table table-sm">
                        <tr>
                            <td>+Transporte</td>
                            <td>---</td>
                        </tr>
                        <tr>
                            <td>+Alojamiento</td>
                            <td>---</td>
                        </tr>
                        <tr>
                            <td>+Autos</td>
                            <td>---</td>
                        </tr>
                        <tr>
                            <td>+Excursiones</td>
                            <td>---</td>
                        </tr>
                        <tr class="table-danger fw-bold">
                            <td>Total</td>
                            <td>---</td>
                        </tr>
                    </table>
                    <div class="mt-3">
                        <a href="editar_reserva.php?id=<?= $r['id_orden'] ?>" class="btn btn-sm btn-info"><i class="bi bi-pencil"></i> Editar</a>
                        <a href="cancelar_reserva.php?id=<?= $r['id_orden'] ?>" class="btn btn-sm btn-danger"><i class="bi bi-x-circle"></i> Cancelar</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach;
else: ?>
    <div class="alert alert-warning">No se encontraron reservas.</div>
<?php endif;
$conn->close();
?>