<?php
include 'conexion.php';
include 'verificar_admin.php';

// Obtener datos del proveedor a editar
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: proveedores.php");
    exit;
}
$id = intval($_GET['id']);
$q = $conn->query("SELECT * FROM proveedores WHERE id_proveedor = $id");
$proveedor = $q->fetch_assoc();
if (!$proveedor) {
    header("Location: proveedores.php");
    exit;
}

// Procesar el formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $tipo = $conn->real_escape_string($_POST['tipo']);
    $contacto = $conn->real_escape_string($_POST['contacto']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $email = $conn->real_escape_string($_POST['email']);
    $direccion = $conn->real_escape_string($_POST['direccion']);
    $descripcion = $conn->real_escape_string($_POST['descripcion']);

    $conn->query("UPDATE proveedores SET 
        nombre='$nombre',
        tipo='$tipo',
        contacto='$contacto',
        telefono='$telefono',
        email='$email',
        direccion='$direccion',
        descripcion='$descripcion'
        WHERE id_proveedor = $id
    ");

    header("Location: proveedores.php?edit=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Proveedor</title>
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
        .card-proveedor {
            background: #fff;
            border: 1px solid #6CE0B6;
            border-radius: 18px;
            box-shadow: 0 4px 24px 0 rgba(117, 13, 55, 0.08);
            padding: 32px 28px 24px 28px;
            max-width: 600px;
            margin: 0 auto;
            position: relative;
            overflow: hidden;
        }
        .card-proveedor:before {
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
                <li class="breadcrumb-item"><a href="proveedores.php">Proveedores</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editar Proveedor</li>
            </ol>
        </nav>
        <div class="card-proveedor mt-4">
            <div class="icon-circle mb-3">
                <i class="bi bi-pencil-square"></i>
            </div>
            <h2 class="text-center mb-4" style="color:#750D37;font-weight:700;letter-spacing:1px;">Editar Proveedor</h2>
            <form method="post" autocomplete="off">
                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" required maxlength="100" value="<?= htmlspecialchars($proveedor['nombre']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Tipo</label>
                    <select name="tipo" class="form-select" required>
                        <option value="">Seleccione tipo</option>
                        <option value="alojamiento" <?= $proveedor['tipo']=='alojamiento'?'selected':'' ?>>Alojamiento</option>
                        <option value="vuelo" <?= $proveedor['tipo']=='vuelo'?'selected':'' ?>>Vuelo</option>
                        <option value="auto" <?= $proveedor['tipo']=='auto'?'selected':'' ?>>Auto</option>
                        <option value="servicio" <?= $proveedor['tipo']=='servicio'?'selected':'' ?>>Servicio</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Contacto (Nombre del encargado, empresa, etc)</label>
                    <input type="text" name="contacto" class="form-control" maxlength="100" value="<?= htmlspecialchars($proveedor['contacto']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" maxlength="30" value="<?= htmlspecialchars($proveedor['telefono']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" maxlength="100" value="<?= htmlspecialchars($proveedor['email']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="direccion" class="form-control" maxlength="150" value="<?= htmlspecialchars($proveedor['direccion']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="3" maxlength="255"><?= htmlspecialchars($proveedor['descripcion']) ?></textarea>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="proveedores.php" class="btn btn-secondary px-4">Cancelar</a>
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
        'Editar proveedor',
        "Proveedor editado: {$proveedor['nombre']} (ID: $id)",
    );
}

$conn->close();
?>