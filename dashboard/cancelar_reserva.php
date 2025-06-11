<?php
include 'conexion.php';

$msg = '';

// Procesar la cancelación:
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn->query("UPDATE ordenes SET estado='Cancelada' WHERE id_orden=$id");
    $msg = "Reserva cancelada correctamente.";
}

// Buscar la reserva (solo para mostrar):
$reserva = null;
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = $conn->query("SELECT o.id_orden, o.fecha_orden, u.nombre AS cliente, o.estado
        FROM ordenes o 
        JOIN usuarios u ON u.id_usuario = o.id_usuario 
        WHERE o.id_orden = $id");
    $reserva = $res->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cancelar Reserva</title>
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
        .btn-danger {
            background-color: #750D37;
            border-color: #750D37;
            font-weight: bold;
        }
        .card {
            background: #fff;
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(117,13,55,0.1);
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
            <li class="breadcrumb-item active" aria-current="page">Cancelar Reserva</li>
        </ol>
    </nav>

    <h2 class="mb-4">Cancelar Reserva</h2>

    <?php if ($msg): ?>
        <div class="alert alert-success"><?= $msg ?></div>
        <a href="reservas.php" class="btn btn-secondary">Volver a reservas</a>
    <?php elseif ($reserva): ?>
        <div class="card">
            <p><strong>Reserva #:</strong> <?= $reserva['id_orden'] ?></p>
            <p><strong>Cliente:</strong> <?= htmlspecialchars($reserva['cliente']) ?></p>
            <p><strong>Fecha de Viaje:</strong> <?= date('d/m/Y', strtotime($reserva['fecha_orden'])) ?></p>
            <p><strong>Estado actual:</strong> <?= $reserva['estado'] ?></p>

            <div class="mt-4">
                <a href="?id=<?= $reserva['id_orden'] ?>&confirm=1" class="btn btn-danger">
                    <i class="bi bi-x-circle"></i> Confirmar Cancelación
                </a>
                <a href="reservas.php" class="btn btn-secondary">Volver</a>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">Reserva no encontrada.</div>
        <a href="reservas.php" class="btn btn-secondary">Volver</a>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
