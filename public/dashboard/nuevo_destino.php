<?php
include 'conexion.php';
include 'verificar_admin.php';

$msg = '';
if (isset($_POST['submit'])) {
    $destino = $_POST['destino'];
    
    $smt = $conn->prepare("INSERT INTO destinos (destino, estado) VALUES (?, 'activo')");
    $smt->bind_param("s", $destino);
    $smt->execute();
    $smt->close();
    header("Location: destinos.php?creado=1");
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Destino</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #FFF6F8; color: #1A001C; }
        .main-content { margin-left: 260px; padding: 40px 30px 30px 30px; min-height: 100vh; }
        .breadcrumb-item a { text-decoration: none; color: #750D37; }
        .btn-primary { background-color: #3AB789; border-color: #3AB789; font-weight: bold; }
        .btn-secondary { background-color: #6c757d; border-color: #6c757d; font-weight: bold; }
        .card { border-radius: 12px; box-shadow: 0 0 10px rgba(0,0,0,0.05); padding: 25px; }
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

    <h2 class="mb-4">Nuevo Destino</h2>

    <form method="post" class="card">
        <div class="mb-3">
            <label class="form-label">Nombre del destino</label>
            <input type="text" name="destino" class="form-control" required>
        </div>
        <i> <i class="bi bi-alert"></i>Recuerda agregar proveedores luego para cada destino, en <a href="proveedor_form.php">Alta de Proveedor</a></i>
        <div class="mt-4">
            <button type="submit" name="submit" class="btn btn-primary me-2"><i class="bi bi-save"></i> Guardar Nuevo Destino</button>
            <a href="destinos.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.js"></script>
</body>
</html>
