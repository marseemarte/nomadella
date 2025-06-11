<?php
include 'conexion.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nueva_reserva'])) {
    $cliente = intval($_POST['cliente']);
    $paquete = intval($_POST['paquete']);
    $pasajeros = intval($_POST['pasajeros']);
    $fecha = $mysqli->real_escape_string($_POST['fecha']);
    // Calcula el precio total del paquete
    $paqueteData = $mysqli->query("SELECT precio FROM paquetes_turisticos WHERE id_paquete = $paquete")->fetch_assoc();
    $total = $paqueteData ? $paqueteData['precio'] * $pasajeros : 0;
    $mysqli->query("INSERT INTO ordenes (id_usuario, fecha_orden, total) VALUES ($cliente, '$fecha', $total)");
    $id_orden = $mysqli->insert_id;
    $mysqli->query("INSERT INTO orden_items (id_orden, id_producto, cantidad, subtotal) VALUES ($id_orden, $paquete, $pasajeros, $total)");
    $msg = "Reserva creada correctamente.";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Reservas | Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .main-content {
            margin-left: 260px;
            padding: 40px 30px 30px 30px;
            min-height: 100vh;
            background: #FFF6F8;
        }
    </style>
</head>

<body>
    <?php include './sidebar.php' ?>
    <div class="main-content">
        <div id="nueva">
            <h3>Nueva Reserva</h3>
            <form method="post">
                <div class="mb-3">
                    <label for="cliente" class="form-label">Cliente</label>
                    <select class="form-control" id="cliente" name="cliente" required>
                        <option value="">Seleccione...</option>
                        <?php foreach ($clientes as $c): ?>
                            <option value="<?= $c['id_usuario'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="paquete" class="form-label">Paquete Turístico</label>
                    <select class="form-control" id="paquete" name="paquete" required>
                        <option value="">Seleccione...</option>
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
                <button type="submit" name="nueva_reserva" class="btn btn-primary">Guardar Reserva</button>
            </form>
        </div>
    </div>
</body>

</html>