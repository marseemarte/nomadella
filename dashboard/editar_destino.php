<?php
include 'conexion.php';
include 'verificar_admin.php';

$msg = '';
$destino = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_destino'])) {
    $id = intval($_POST['id_destino']);
    $nombre = $conn->real_escape_string(trim($_POST['nombre']));

    if ($nombre) {
        $conn->query("UPDATE destinos SET destino = '$nombre' WHERE id_destino = $id");
        header("Location: destinos.php?editado=1");
        exit;
    }
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = $conn->query("SELECT * FROM destinos WHERE id_destino = $id AND estado = 'activo'");
    $destino = $res->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Destino</title>
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
            <li class="breadcrumb-item active" aria-current="page">Editar Destino</li>
        </ol>
    </nav>

    <h2 class="mb-4">Editar Destino</h2>

    <?php if ($destino): ?>
    <form method="post" class="card">
        <input type="hidden" name="id_destino" value="<?= $destino['id_destino'] ?>">
        <div class="mb-3">
            <label class="form-label">Nombre del destino</label>
            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($destino['destino']) ?>" required>
        </div>
        <div class="mt-4">
            <button type="submit" class="btn btn-primary me-2"><i class="bi bi-save"></i> Guardar cambios</button>
            <a href="destinos.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
    <?php else: ?>
        <div class="alert alert-warning">Destino no encontrado.</div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
