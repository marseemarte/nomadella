<?php
include 'conexion.php';
include 'verificar_admin.php';

$msg = '';

// Al enviar el formulario:
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_orden = intval($_POST['id_orden']);
    $estado = $conn->real_escape_string($_POST['estado']);
    $medio_pago = $conn->real_escape_string($_POST['medio_pago']);
    $datos_facturacion = $conn->real_escape_string($_POST['datos_facturacion']);

    // Actualizar orden
    $conn->query("UPDATE ordenes SET estado='$estado', medio_pago='$medio_pago', datos_facturacion='$datos_facturacion' WHERE id_orden=$id_orden");

    // Eliminar servicios adicionales y autos anteriores
    $conn->query("DELETE FROM orden_items WHERE id_orden=$id_orden AND tipo_producto IN ('servicio_adicional', 'alquiler_auto')");

    // Insertar servicios adicionales seleccionados
    if (!empty($_POST['servicios_adicionales'])) {
        foreach ($_POST['servicios_adicionales'] as $id_servicio) {
            $id_servicio = intval($id_servicio);
            $conn->query("INSERT INTO orden_items (id_orden, tipo_producto, id_producto, cantidad) VALUES ($id_orden, 'servicio_adicional', $id_servicio, 1)");
        }
    }

    // Insertar alquiler de auto seleccionado
    if (!empty($_POST['alquiler_auto'])) {
        $id_auto = intval($_POST['alquiler_auto']);
        $conn->query("INSERT INTO orden_items (id_orden, tipo_producto, id_producto, cantidad) VALUES ($id_orden, 'alquiler_auto', $id_auto, 1)");
    }

    $msg = "Reserva actualizada correctamente.";

    // Obtener id_usuario de la reserva
    $res = $conn->query("SELECT id_usuario FROM ordenes WHERE id_orden = $id_orden");
    $row = $res->fetch_assoc();
    $id_usuario = $row['id_usuario'] ?? 0;

    // Crear notificación
    $mensaje_notif = "El estado de su reserva #$id_orden ha cambiado a '$estado'. Por favor verifique los cambios en Mis Reservas.";
    $mensaje_notif_sql = $conn->real_escape_string($mensaje_notif);
    $conn->query("INSERT INTO notificaciones (id_usuario, mensaje, tipo, leido, fecha) VALUES ($id_usuario, '$mensaje_notif_sql', 'estado', 0, NOW())");

    // Registrar en bitácora
    if (isset($_SESSION['id_usuario'])) {
        registrar_bitacora(
            $pdo,
            $_SESSION['id_usuario'],
            'Editar reserva',
            "Reserva #$id_orden actualizada. Estado: $estado"
        );
    }
}


$reserva = null;
$servicios_seleccionados = [];
$auto_seleccionado = null;
$servicios_disponibles = [];
$autos_disponibles = [];
$id_paquete = null;

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = $conn->query("SELECT * FROM ordenes WHERE id_orden = $id");
    $reserva = $res->fetch_assoc();

    if ($reserva) {
        $id_orden = $reserva['id_orden'];

        // Obtener paquete turístico de la orden
        $res_paquete = $conn->query("SELECT id_producto FROM orden_items WHERE id_orden = $id_orden AND tipo_producto = 'paquete_turistico' LIMIT 1");
        if ($row = $res_paquete->fetch_assoc()) {
            $id_paquete = $row['id_producto'];

            // Obtener servicios adicionales disponibles para el paquete
            $res_serv = $conn->query("SELECT s.id_servicio, s.nombre FROM servicios_adicionales s JOIN paquete_servicios ps ON s.id_servicio = ps.id_servicio WHERE ps.id_paquete = $id_paquete");
            while ($row = $res_serv->fetch_assoc()) {
                $servicios_disponibles[] = $row;
            }

            // Obtener autos disponibles para el paquete
            $res_autos = $conn->query("SELECT aa.id_alquiler, aa.proveedor FROM alquiler_autos aa JOIN paquete_autos pa ON aa.id_alquiler = pa.id_alquiler WHERE pa.id_paquete = $id_paquete");
            while ($row = $res_autos->fetch_assoc()) {
                $autos_disponibles[] = $row;
            }

            // Obtener servicios adicionales seleccionados en la orden
            $res_serv_sel = $conn->query("SELECT id_producto FROM orden_items WHERE id_orden = $id_orden AND tipo_producto = 'servicio_adicional'");
            while ($row = $res_serv_sel->fetch_assoc()) {
                $servicios_seleccionados[] = $row['id_producto'];
            }

            // Obtener auto seleccionado en la orden (solo uno)
            $res_auto_sel = $conn->query("SELECT id_producto FROM orden_items WHERE id_orden = $id_orden AND tipo_producto = 'alquiler_auto' LIMIT 1");
            if ($row = $res_auto_sel->fetch_assoc()) {
                $auto_seleccionado = $row['id_producto'];
            }
        }
    }
}
// Registrar en bitácora
if (isset($_SESSION['id_usuario'])) {
    registrar_bitacora(
        $pdo,
        $_SESSION['id_usuario'],
        'Editar reserva',
        "Reserva #$id_orden actualizada. Estado: $estado, Fecha: $fecha"
    );
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Reserva</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #FFF6F8;
            color: #1A001C;
        }

        .main-content {
            margin-left: 260px;
            padding: 40px 30px 30px 30px;
            min-height: 100vh;
        }

        .breadcrumb-item a {
            text-decoration: none;
            color: #750D37;
        }

        .form-label {
            font-weight: 500;
            color: #1A001C;
        }

        .btn-primary {
            background-color: #3AB789;
            border-color: #3AB789;
            font-weight: bold;
        }

        .btn-secondary {
            background-color: #5CC7ED;
            border-color: #5CC7ED;
            color: #1A001C;
            font-weight: bold;
        }

        .btn-primary:hover,
        .btn-secondary:hover {
            opacity: 0.9;
        }

        .card {
            background: #fff;
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(117, 13, 55, 0.1);
            padding: 20px;
        }
    </style>
</head>

<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="reservas.php">Reservas</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editar Reserva</li>
            </ol>
        </nav>

        <h2 class="mb-4">Editar Reserva</h2>

        <?php if ($msg): ?>
            <div class="alert alert-success"><?= $msg ?></div>
        <?php endif; ?>

        <?php if ($reserva): ?>
            <div class="card">
                <form method="post" id="form-editar-reserva">
                    <input type="hidden" name="id_orden" value="<?= $reserva['id_orden'] ?>">

                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-select" id="estado" name="estado" required>
                            <option value="Pendiente" <?= $reserva['estado'] == 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
                            <option value="Confirmada" <?= $reserva['estado'] == 'Confirmada' ? 'selected' : '' ?>>Confirmada</option>
                            <option value="Cancelada" <?= $reserva['estado'] == 'Cancelada' ? 'selected' : '' ?>>Cancelada</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="medio_pago" class="form-label">Medio de pago</label>
                        <input type="text" class="form-control" name="medio_pago" id="medio_pago" value="<?= htmlspecialchars($reserva['medio_pago'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label for="datos_facturacion" class="form-label">Datos de facturación</label>
                        <textarea class="form-control" name="datos_facturacion" id="datos_facturacion" rows="3"><?= htmlspecialchars($reserva['datos_facturacion'] ?? '') ?></textarea>
                    </div>

                    <?php if (!empty($servicios_disponibles)): ?>
                        <div class="mb-3">
                            <label class="form-label">Servicios adicionales (opcional)</label>
                            <?php foreach ($servicios_disponibles as $serv): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="servicios_adicionales[]" value="<?= $serv['id_servicio'] ?>" id="servicio_<?= $serv['id_servicio'] ?>" <?= in_array($serv['id_servicio'], $servicios_seleccionados) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="servicio_<?= $serv['id_servicio'] ?>"><?= htmlspecialchars($serv['nombre']) ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($autos_disponibles)): ?>
                        <div class="mb-3">
                            <label class="form-label">Alquiler de auto (opcional)</label>
                            <select name="alquiler_auto" class="form-select">
                                <option value="">No alquilar auto</option>
                                <?php foreach ($autos_disponibles as $auto): ?>
                                    <option value="<?= $auto['id_alquiler'] ?>" <?= ($auto_seleccionado == $auto['id_alquiler']) ? 'selected' : '' ?>><?= htmlspecialchars($auto['proveedor']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Guardar Cambios</button>
                    <a href="reservas.php" class="btn btn-secondary">Volver</a>
                </form>
            </div>

        <?php else: ?>
            <div class="alert alert-warning">No se encontró la reserva solicitada.</div>
        <?php endif; ?>

    </div>
    <!-- Modal -->
    <div class="modal fade" id="modalConfirmacion" tabindex="-1" aria-labelledby="modalConfirmacionLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size:2.5rem;"></i>
                    </div>
                    <h5 class="mb-3" id="modalConfirmacionLabel">¿Está seguro?</h5>
                    <p class="mb-4">Se enviará una notificación al cliente informando los cambios.</p>
                    <button type="button" class="btn btn-success px-4 me-2" id="btnConfirmar">Confirmar</button>
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('form-editar-reserva');
            const modalEl = document.getElementById('modalConfirmacion');
            let modal = new bootstrap.Modal(modalEl);

            let submitIntent = false;

            form.addEventListener('submit', function(e) {
                if (!submitIntent) {
                    e.preventDefault();
                    modal.show();
                } else {
                    submitIntent = false; // reset para futuros envíos
                }
            });

            document.getElementById('btnConfirmar').addEventListener('click', function() {
                submitIntent = true;
                modal.hide();
                form.submit();
            });
        });
    </script>
</body>

</html>

<?php
// require_once 'enviar_mail.php';
// enviarCorreo($email, $nombre_cliente, $asunto, nl2br($mensaje));
?>