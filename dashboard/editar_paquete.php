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
    $activo = isset($_POST['activo']) ? 1 : 0;

    $conn->query("UPDATE paquetes_turisticos SET 
        nombre='$nombre',
        descripcion='$descripcion',
        destino='$destino',
        precio_base=$precio_base,
        fecha_inicio='$fecha_inicio',
        fecha_fin='$fecha_fin',
        activo=$activo
        WHERE id_paquete = $id
    ");

    header("Location: paquetes.php?edit=1");
    exit;
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
                <div class="mb-3 form-check">
                    <input type="checkbox" name="activo" class="form-check-input" id="activo" <?= $paquete['activo'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="activo">Activo</label>
                </div>
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