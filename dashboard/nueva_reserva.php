<?php
include 'conexion.php';

$msg = '';

// Al enviar el formulario:
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente = intval($_POST['cliente']);
    $paquete = intval($_POST['paquete']);
    $pasajeros = intval($_POST['pasajeros']);
    $fecha = $conn->real_escape_string($_POST['fecha']);

    $paqueteData = $conn->query("SELECT precio_base FROM paquetes_turisticos WHERE id_paquete = $paquete")->fetch_assoc();
    $total = $paqueteData ? $paqueteData['precio_base'] * $pasajeros : 0;

    $conn->query("INSERT INTO ordenes (id_usuario, fecha_orden, total) VALUES ($cliente, '$fecha', $total)");
    $id_orden = $conn->insert_id;
    $conn->query("INSERT INTO orden_items (id_orden, id_producto, cantidad, subtotal) VALUES ($id_orden, $paquete, $pasajeros, $total)");

    $msg = "Reserva creada correctamente.";
}

// Obtener clientes y paquetes:
$clientes = $conn->query("SELECT id_usuario, nombre FROM usuarios");
$paquetes = $conn->query("SELECT id_paquete, destino FROM paquetes_turisticos WHERE activo=1");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Reserva</title>
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
        .btn-primary:hover, .btn-secondary:hover {
            opacity: 0.9;
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
            <li class="breadcrumb-item active" aria-current="page">Nueva Reserva</li>
        </ol>
    </nav>

    <h2 class="mb-4">Nueva Reserva</h2>

    <?php if ($msg): ?>
        <div class="alert alert-success"><?= $msg ?></div>
    <?php endif; ?>

    <div class="card">
        <form method="post">
            <div class="mb-3">
                <label for="cliente" class="form-label">Cliente</label>
                <select class="form-select" id="cliente" name="cliente" required>
                    <option value="">Seleccione un cliente...</option>
                    <?php foreach ($clientes as $c): ?>
                        <option value="<?= $c['id_usuario'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="paquete" class="form-label">Paquete Turístico</label>
                <select class="form-select" id="paquete" name="paquete" required>
                    <option value="">Seleccione un paquete...</option>
                    <?php foreach ($paquetes as $p): ?>
                        <option value="<?= $p['id_paquete'] ?>"><?= htmlspecialchars($p['destino']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="pasajeros" class="form-label">Número de Pasajeros</label>
                <input type="number" class="form-control" id="pasajeros" name="pasajeros" min="1" required>
            </div>

            <div class="mb-3">
                <label for="fecha" class="form-label">Fecha de Viaje</label>
                <input type="date" class="form-control" id="fecha" name="fecha" required>
            </div>

            <button type="submit" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Crear Reserva</button>
            <a href="reservas.php" class="btn btn-secondary">Volver</a>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
