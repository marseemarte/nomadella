<?php
include 'conexion.php';
include 'verificar_admin.php';

// Obtener datos del paquete a editar
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: paquetes.php");
    exit;
}
$id = intval($_GET['id']);
$q = $conn->query("SELECT * FROM paquetes_turisticos WHERE id_paquete = $id");
$paquete = $q->fetch_assoc();
if (!$paquete) {
    header("Location: paquetes.php");
    exit;
}

// Procesar el formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $descripcion = $conn->real_escape_string($_POST['descripcion']);
    $destino = $conn->real_escape_string($_POST['destino']);
    $precio_base = floatval($_POST['precio_base']);
    $fecha_inicio = $conn->real_escape_string($_POST['fecha_inicio']);
    $fecha_fin = $conn->real_escape_string($_POST['fecha_fin']);

    $conn->query("UPDATE paquetes_turisticos SET 
        nombre='$nombre',
        descripcion='$descripcion',
        destino='$destino',
        precio_base=$precio_base,
        fecha_inicio='$fecha_inicio',
        fecha_fin='$fecha_fin',
        WHERE id_paquete = $id
    ");

    // Procesar componentes seleccionados
    $alojamiento = isset($_POST['alojamiento']) ? intval($_POST['alojamiento']) : null;
    $vuelo = isset($_POST['vuelo']) ? intval($_POST['vuelo']) : null;
    $auto = isset($_POST['auto']) ? intval($_POST['auto']) : null;
    $servicio = isset($_POST['servicio']) ? intval($_POST['servicio']) : null;

    // Alojamientos
    if ($alojamiento) {
        $conn->query("REPLACE INTO paquete_alojamientos (id_paquete, id_alojamiento) VALUES ($id, $alojamiento)");
    } else {
        $conn->query("DELETE FROM paquete_alojamientos WHERE id_paquete = $id");
    }

    // Vuelos
    if ($vuelo) {
        $conn->query("REPLACE INTO paquete_vuelos (id_paquete, id_vuelo) VALUES ($id, $vuelo)");
    } else {
        $conn->query("DELETE FROM paquete_vuelos WHERE id_paquete = $id");
    }

    // Autos
    if ($auto) {
        $conn->query("REPLACE INTO paquete_autos (id_paquete, id_alquiler) VALUES ($id, $auto)");
    } else {
        $conn->query("DELETE FROM paquete_autos WHERE id_paquete = $id");
    }

    // Servicios
    if ($servicio) {
        $conn->query("REPLACE INTO paquete_servicios (id_paquete, id_servicio) VALUES ($id, $servicio)");
    } else {
        $conn->query("DELETE FROM paquete_servicios WHERE id_paquete = $id");
    }

    header("Location: paquetes.php?edit=1");
    exit;
}

// Obtener id_destino según el nombre de destino
$id_destino = null;
$res = $conn->query("SELECT id_destino FROM destinos WHERE destino = '" . $conn->real_escape_string($paquete['destino']) . "' LIMIT 1");
if ($row = $res->fetch_assoc()) {
    $id_destino = intval($row['id_destino']);
}

$alojamientos = $vuelos = $autos = $servicios = [];

$current_alojamiento = null;
$current_vuelo = null;
$current_auto = null;
$current_servicio = null;

if ($id_destino) {
    $res = $conn->query("SELECT id_alojamiento, nombre FROM alojamientos WHERE id_destino = $id_destino");
    while ($row = $res->fetch_assoc()) $alojamientos[] = $row;

    $res = $conn->query("SELECT id_vuelo, aerolinea FROM vuelos WHERE id_destino = $id_destino");
    while ($row = $res->fetch_assoc()) $vuelos[] = $row;

    $res = $conn->query("SELECT id_alquiler, proveedor FROM alquiler_autos WHERE id_destino = $id_destino");
    while ($row = $res->fetch_assoc()) $autos[] = $row;

    $res = $conn->query("SELECT id_servicio, nombre FROM servicios_adicionales WHERE id_destino = $id_destino");
    while ($row = $res->fetch_assoc()) $servicios[] = $row;

    // mostrar los componentes actuales del paquete
    $res = $conn->query("SELECT id_alojamiento FROM paquete_alojamientos WHERE id_paquete = $id LIMIT 1");
    if ($row = $res->fetch_assoc()) {
        $current_alojamiento = $row['id_alojamiento'];
    }

    $res = $conn->query("SELECT id_vuelo FROM paquete_vuelos WHERE id_paquete = $id LIMIT 1");
    if ($row = $res->fetch_assoc()) {
        $current_vuelo = $row['id_vuelo'];
    }

    $res = $conn->query("SELECT id_alquiler FROM paquete_autos WHERE id_paquete = $id LIMIT 1");
    if ($row = $res->fetch_assoc()) {
        $current_auto = $row['id_alquiler'];
    }

    $res = $conn->query("SELECT id_servicio FROM paquete_servicios WHERE id_paquete = $id LIMIT 1");
    if ($row = $res->fetch_assoc()) {
        $current_servicio = $row['id_servicio'];
    }
}


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Paquete Turístico</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/nomadella/css/apartados.css">
    <style>
        .main-content {
            margin-left: 260px;
            padding: 40px 30px 30px 30px;
            min-height: 100vh;
            background: #FFF6F8;
        }

        .card-paquete {
            background: #fff;
            border: 1px solid #6CE0B6;
            border-radius: 18px;
            box-shadow: 0 4px 24px 0 rgba(117, 13, 55, 0.08);
            padding: 32px 28px 24px 28px;
            max-width: 650px;
            margin: 0 auto;
            position: relative;
            overflow: hidden;
        }

        .card-paquete:before {
            content: "";
            position: absolute;
            top: -60px;
            right: -60px;
            width: 160px;
            height: 160px;
            background: radial-gradient(circle, #6CE0B6 0%, #5CC7ED 100%);
            opacity: 0.18;
            z-index: 0;
        }

        .form-label {
            color: #750D37;
            font-weight: 500;
        }

        .btn-success {
            background: #3AB789 !important;
            border: none;
            font-weight: bold;
            color: #fff !important;
            letter-spacing: 1px;
        }

        .btn-secondary {
            background: #5CC7ED !important;
            border: none;
            color: #1A001C !important;
            font-weight: bold;
        }

        .icon-circle {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #6CE0B6 60%, #5CC7ED 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px auto;
            font-size: 2.2rem;
            color: #fff;
            box-shadow: 0 2px 8px #6CE0B633;
        }

        .breadcrumb-item a {
            color: #750D37;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="paquetes.php">Paquetes Turísticos</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editar Paquete</li>
            </ol>
        </nav>
        <div class="card-paquete mt-4">
            <div class="icon-circle mb-3">
                <i class="bi bi-pencil-square"></i>
            </div>
            <h2 class="text-center mb-4" style="color:#750D37;font-weight:700;letter-spacing:1px;">Editar Paquete Turístico</h2>
            <form method="post" autocomplete="off">
                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" required maxlength="100" value="<?= htmlspecialchars($paquete['nombre']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="3" maxlength="255" required><?= htmlspecialchars($paquete['descripcion']) ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Destino</label>
                    <input type="text" name="destino" class="form-control" required maxlength="100" value="<?= htmlspecialchars($paquete['destino']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Precio base</label>
                    <input type="number" name="precio_base" class="form-control" required min="0" step="0.01" value="<?= htmlspecialchars($paquete['precio_base']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Fecha de inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control" required value="<?= htmlspecialchars($paquete['fecha_inicio']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Fecha de fin</label>
                    <input type="date" name="fecha_fin" class="form-control" required value="<?= htmlspecialchars($paquete['fecha_fin']) ?>">
                </div>
                <?php if ($id_destino): ?>
                    <hr>
                    <h5 class="mt-4 mb-3 text-primary">Componentes disponibles para el destino: <?= htmlspecialchars($paquete['destino']) ?></h5>

                    <div class="mb-3">
                        <label class="form-label">Alojamientos</label>
                        <select class="form-select" name="alojamiento">
                            <option value="">Seleccionar alojamiento</option>
                            <?php foreach ($alojamientos as $a): ?>
                                <option value="<?= $a['id_alojamiento'] ?>" <?= ($a['id_alojamiento'] == $current_alojamiento) ? 'selected' : '' ?>><?= htmlspecialchars($a['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Vuelos</label>
                        <select class="form-select" name="vuelo">
                            <option value="">Seleccionar vuelo</option>
                            <?php foreach ($vuelos as $v): ?>
                                <option value="<?= $v['id_vuelo'] ?>" <?= ($v['id_vuelo'] == $current_vuelo) ? 'selected' : '' ?>><?= htmlspecialchars($v['aerolinea']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Autos</label>
                        <select class="form-select" name="auto">
                            <option value="">Seleccionar alquiler de auto</option>
                            <?php foreach ($autos as $au): ?>
                                <option value="<?= $au['id_alquiler'] ?>" <?= ($au['id_alquiler'] == $current_auto) ? 'selected' : '' ?>><?= htmlspecialchars($au['proveedor']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Servicios adicionales</label>
                        <select class="form-select" name="servicio">
                            <option value="">Seleccionar servicio adicional</option>
                            <?php foreach ($servicios as $s): ?>
                                <option value="<?= $s['id_servicio'] ?>" <?= ($s['id_servicio'] == $current_servicio) ? 'selected' : '' ?>><?= htmlspecialchars($s['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="d-flex justify-content-between">
                    <a href="paquetes.php" class="btn btn-secondary px-4">Cancelar</a>
                    <button type="submit" class="btn btn-success px-4"><i class="bi bi-check-circle"></i> Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php

if (isset($_SESSION['id_usuario'])) {
    registrar_bitacora(
        $pdo,
        $_SESSION['id_usuario'],
        'Editar paquete turístico',
        "paquete turístico', '$nombre' editado con éxito"
    );
}

$conn->close(); ?>