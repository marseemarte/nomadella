<?php
// filepath: c:\xampp\htdocs\nomadella\dashboard\nuevo_destino.php
include 'conexion.php';
include 'verificar_admin.php';

$msg = '';
$id_destino = null;
$destino = '';

// Paso 1: Alta de destino
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['asociar'])) {
    $destino = trim($conn->real_escape_string($_POST['destino']));
    if ($destino) {
        // Verificar si ya existe (case-insensitive)
        $res = $conn->query("SELECT COUNT(*) as total FROM destinos WHERE LOWER(TRIM(destino)) = LOWER(TRIM('$destino'))");
        $row = $res->fetch_assoc();
        if ($row['total'] > 0) {
            $msg = "Este destino ya está ingresado.";
        } else {
            $conn->query("INSERT INTO destinos (destino) VALUES ('$destino')");
            $id_destino = $conn->insert_id;
            header("Location: nuevo_destino.php?id_destino=$id_destino&asociar=1");
            exit;
        }
    } else {
        $msg = "Debe ingresar un destino válido.";
    }
}

// Paso 2: Asociación de proveedores
$id_destino = isset($_GET['id_destino']) ? intval($_GET['id_destino']) : $id_destino;
if ($id_destino) {
    $res = $conn->query("SELECT * FROM destinos WHERE id_destino = $id_destino");
    $row = $res->fetch_assoc();
    $destino = $row ? $row['destino'] : '';
}

// Traer proveedores por id_destino
$alojamientos = [];
$vuelos = [];
$autos = [];
$servicios = [];

if ($id_destino) {
    // Alojamientos
    $res = $conn->query("SELECT id_alojamiento, nombre FROM alojamientos WHERE id_destino = $id_destino");
    while ($row = $res->fetch_assoc()) $alojamientos[] = $row;

    // Vuelos
    $res = $conn->query("SELECT id_vuelo, aerolinea FROM vuelos WHERE id_destino = $id_destino");
    while ($row = $res->fetch_assoc()) $vuelos[] = $row;

    // Autos
    $res = $conn->query("SELECT id_alquiler, proveedor FROM alquiler_autos WHERE id_destino = $id_destino");
    while ($row = $res->fetch_assoc()) $autos[] = $row;

    // Servicios adicionales
    $res = $conn->query("SELECT id_servicio, nombre FROM servicios_adicionales WHERE id_destino = $id_destino");
    while ($row = $res->fetch_assoc()) $servicios[] = $row;
}

// Guardar proveedores asociados
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['asociar'])) {
    // Alojamientos
    if (!empty($_POST['alojamientos'])) {
        foreach ($_POST['alojamientos'] as $nombre) {
            $nombre = $conn->real_escape_string($nombre);
            $conn->query("INSERT INTO alojamientos (nombre, id_destino) VALUES ('$nombre', $id_destino)");
        }
    }
    // Vuelos
    if (!empty($_POST['vuelos'])) {
        foreach ($_POST['vuelos'] as $aerolinea) {
            $aerolinea = $conn->real_escape_string($aerolinea);
            $conn->query("INSERT INTO vuelos (aerolinea, id_destino) VALUES ('$aerolinea', $id_destino)");
        }
    }
    // Autos
    if (!empty($_POST['autos'])) {
        foreach ($_POST['autos'] as $proveedor) {
            $proveedor = $conn->real_escape_string($proveedor);
            $conn->query("INSERT INTO alquiler_autos (proveedor, id_destino) VALUES ('$proveedor', $id_destino)");
        }
    }
    // Servicios adicionales
    if (!empty($_POST['servicios'])) {
        foreach ($_POST['servicios'] as $nombre) {
            $nombre = $conn->real_escape_string($nombre);
            $conn->query("INSERT INTO servicios_adicionales (nombre, id_destino) VALUES ('$nombre', $id_destino)");
        }
    }
    $msg = "Destino y proveedores asociados correctamente.";
    header("Location: destinos.php?ok=1");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Nuevo Destino</title>
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

        .card-destino {
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
    </style>
</head>

<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="destinos.php">Destinos</a></li>
                <li class="breadcrumb-item active" aria-current="page">Nuevo Destino</li>
            </ol>
        </nav>
        <div class="card-destino mt-4">
            <div class="icon-circle mb-3">
                <i class="bi bi-geo-alt"></i>
            </div>
            <h2 class="text-center mb-4" style="color:#750D37;font-weight:700;letter-spacing:1px;">Alta de Destino</h2>
            <?php if ($msg): ?>
                <div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
            <?php endif; ?>
            <?php if (!$id_destino): ?>
                <!-- Paso 1: Alta de destino -->
                <form method="post" autocomplete="off">
                    <div class="mb-3">
                        <label class="form-label">Nombre del destino</label>
                        <input type="text" name="destino" class="form-control" required maxlength="100">
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="destino.php" class="btn btn-secondary px-4">Cancelar</a>
                        <button type="submit" class="btn btn-success px-4"><i class="bi bi-check-circle"></i> Siguiente</button>
                    </div>
                </form>
            <?php else: ?>
                <!-- Paso 2: Asociación de proveedores -->
                <?php include 'componentes_destino.php'; ?>
            <?php endif; ?>
        </div>
    </div>
    <!-- Modal Alta Proveedor -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/js_componentes_desino.js"></script>
</body>

</html>
<?php
$conn->close();
?>