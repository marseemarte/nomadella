<?php
include 'conexion.php';
include 'verificar_admin.php';

$msg = '';

// Al enviar el formulario:
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_orden = intval($_POST['id_orden']);
    $fecha = $conn->real_escape_string($_POST['fecha']);
    $estado = $conn->real_escape_string($_POST['estado']);

    $conn->query("UPDATE ordenes SET fecha_orden='$fecha', estado='$estado' WHERE id_orden=$id_orden");
    $msg = "Reserva actualizada correctamente.";

    // Registrar en bitácora
    if (isset($_SESSION['id_usuario'])) {
        registrar_bitacora(
            $pdo,
            $_SESSION['id_usuario'],
            'Editar reserva',
            "Reserva #$id_orden actualizada. Estado: $estado, Fecha: $fecha"
        );
    }

    // ENVIAR CORREO AL CLIENTE
//     $res = $conn->query("
//         SELECT u.email, u.nombre
//         FROM ordenes o
//         JOIN usuarios u ON o.id_usuario = u.id_usuario
//         WHERE o.id_orden = $id_orden
//     ");
//     $cliente = $res->fetch_assoc();
//     $email = $cliente['email'];
//     $nombre_cliente = $cliente['nombre'];

//     $asunto = "Actualización de su reserva";

//     $mensaje = "Hola $nombre_cliente,\n\n";
//     $mensaje .= "Se han realizado cambios en su reserva #$id_orden:\n";
//     $mensaje .= "- Nueva fecha: $fecha\n";
//     $mensaje .= "- Nuevo estado: $estado\n\n";
//     $mensaje .= "Por favor verifique los cambios. Muchas gracias por confiar en Nomadella.";
// ;


// Obtener el id_usuario de la reserva
$res = $conn->query("SELECT id_usuario FROM ordenes WHERE id_orden = $id_orden");
$row = $res->fetch_assoc();
$id_usuario = $row['id_usuario'] ?? 0;

// Crear la notificación
$mensaje_notif = "El estado de su reserva #$id_orden ha cambiado a '$estado'. Por favor verifique los cambios en Mis Reservas.";
$mensaje_notif_sql = $conn->real_escape_string($mensaje_notif);
$conn->query("INSERT INTO notificaciones (id_usuario, mensaje, tipo, leido, fecha) VALUES ($id_usuario, '$mensaje_notif_sql', '".strtolower($estado)."', 0, NOW())");
}

// Obtener datos de la reserva:
$reserva = null;
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = $conn->query("SELECT * FROM ordenes WHERE id_orden = $id");
    $reserva = $res->fetch_assoc();
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
        body { background-color: #FFF6F8; color: #1A001C; }
        .main-content { margin-left: 260px; padding: 40px 30px 30px 30px; min-height: 100vh; }
        .breadcrumb-item a { text-decoration: none; color: #750D37; }
        .form-label { font-weight: 500; color: #1A001C; }
        .btn-primary { background-color: #3AB789; border-color: #3AB789; font-weight: bold; }
        .btn-secondary { background-color: #5CC7ED; border-color: #5CC7ED; color: #1A001C; font-weight: bold; }
        .btn-primary:hover, .btn-secondary:hover { opacity: 0.9; }
        .card { background: #fff; border: none; border-radius: 10px; box-shadow: 0 0 10px rgba(117,13,55,0.1); padding: 20px; }
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
                <label for="fecha" class="form-label">Fecha de Viaje</label>
                <input type="date" class="form-control" id="fecha" name="fecha" value="<?= date('Y-m-d', strtotime($reserva['fecha_orden'])) ?>" required>
            </div>

            <div class="mb-3">
                <label for="estado" class="form-label">Estado</label>
                <select class="form-select" id="estado" name="estado" required>
                    <option value="Pendiente" <?= $reserva['estado']=='Pendiente' ? 'selected' : '' ?>>Pendiente</option>
                    <option value="Confirmada" <?= $reserva['estado']=='Confirmada' ? 'selected' : '' ?>>Confirmada</option>
                    <option value="Cancelada" <?= $reserva['estado']=='Cancelada' ? 'selected' : '' ?>>Cancelada</option>
                </select>
            </div>

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
