<?php
session_start();
include '../conexion.php';
include '../header.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit;
}

$id_usuario = $_SESSION['usuario_id'];
$id_orden = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Traer reserva
$sql = "SELECT o.*, p.id_paquete, p.nombre AS paquete, p.fecha_inicio, p.fecha_fin
        FROM ordenes o
        JOIN orden_items oi ON o.id_orden = oi.id_orden
        JOIN paquetes_turisticos p ON oi.id_producto = p.id_paquete
        WHERE o.id_orden = ? AND o.id_usuario = ? AND oi.tipo_producto = 'paquete_turistico'";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $id_orden, $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Reserva no encontrada o acceso denegado.</div></div>";
    include '../footer.php';
    exit;
}

$reserva = $res->fetch_assoc();
$fecha_inicio_actual = new DateTime($reserva['fecha_inicio']);
$fecha_fin_actual = new DateTime($reserva['fecha_fin']);
$hoy = new DateTime();
$permite_cambios = $fecha_inicio_actual > $hoy;

// Procesar cambios de fecha
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_cambios'])) {
    $nueva_inicio = $_POST['fecha_inicio'];
    $nueva_fin = $_POST['fecha_fin'];

    if ($permite_cambios && strtotime($nueva_inicio) < strtotime($nueva_fin)) {
        $update = $conexion->prepare("UPDATE paquetes_turisticos SET fecha_inicio = ?, fecha_fin = ? WHERE id_paquete = ?");
        $update->bind_param("ssi", $nueva_inicio, $nueva_fin, $reserva['id_paquete']);
        $update->execute();

        // Redirigir a reservas.php
        header("Location: reservas.php");
        exit;
    } else {
        $mensaje_error = "Las fechas no son válidas o el viaje ya comenzó.";
    }
}

// Cancelar reserva
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancelar'])) {
    if ($permite_cambios && in_array(strtolower($reserva['estado']), ['pendiente', 'confirmada'])) {
        $cancelar = $conexion->prepare("UPDATE ordenes SET estado = 'Cancelada' WHERE id_orden = ?");
        $cancelar->bind_param("i", $id_orden);
        $cancelar->execute();

        echo "<script>alert('Reserva cancelada con éxito.'); window.location.href='reservas.php';</script>";
        header("Location: reservas.php");
        exit;
    } else {
        $mensaje_error = "No se puede cancelar esta reserva.";
    }
}
?>

<div class="container my-5">
    <h2 class="text-center mb-4">Editar Fechas de la Reserva</h2>
    <div class="card p-4 shadow-sm">
        <h4 class="text-primary"><?= htmlspecialchars($reserva['paquete']) ?></h4>
        <p><strong>Estado actual:</strong> <?= ucfirst($reserva['estado']) ?></p>

        <?php if (isset($mensaje_error)): ?>
            <div class="alert alert-warning"><?= $mensaje_error ?></div>
        <?php endif; ?>

        <?php if ($permite_cambios): ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="fecha_inicio" class="form-label">Nueva fecha de inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control" required value="<?= $fecha_inicio_actual->format('Y-m-d') ?>">
                </div>
                <div class="mb-3">
                    <label for="fecha_fin" class="form-label">Nueva fecha de fin</label>
                    <input type="date" name="fecha_fin" class="form-control" required value="<?= $fecha_fin_actual->format('Y-m-d') ?>">
                </div>
                <button type="submit" name="guardar_cambios" class="btn btn-primary">Guardar cambios</button>
                <button type="submit" name="cancelar" class="btn btn-danger ms-2" onclick="return confirm('¿Seguro que querés cancelar esta reserva?');">Cancelar reserva</button>
            </form>
        <?php else: ?>
            <div class="alert alert-info">La reserva no se puede editar ni cancelar porque el viaje ya comenzó.</div>
        <?php endif; ?>

        <a href="perfil/reservas.php" class="btn btn-secondary mt-3">Volver</a>
    </div>
</div>

<?php include '../footer.php'; ?>
