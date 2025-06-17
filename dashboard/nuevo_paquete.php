<?php
include 'conexion.php';
include 'verificar_admin.php';

// Obtener destinos disponibles
$destinos_disponibles = [];
$res = $conn->query("SELECT id_destino, destino FROM destinos ORDER BY destino");
while ($row = $res->fetch_assoc()) {
    $destinos_disponibles[] = $row;
}

// Paso 1: Alta de paquete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['asociar'])) {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $descripcion = $conn->real_escape_string($_POST['descripcion']);
    $precio_base = floatval($_POST['precio_base']);
    $fecha_inicio = $conn->real_escape_string($_POST['fecha_inicio']);
    $fecha_fin = $conn->real_escape_string($_POST['fecha_fin']);
    $activo = isset($_POST['activo']) ? 1 : 0;
    $id_destino = intval($_POST['id_destino']);

    $conn->query("INSERT INTO paquetes_turisticos (nombre, descripcion, id_destino, precio_base, fecha_inicio, fecha_fin, activo)
                  VALUES ('$nombre', '$descripcion', $id_destino, $precio_base, '$fecha_inicio', '$fecha_fin', $activo)");
    $id_paquete = $conn->insert_id;

    // Guardar etiquetas nuevas
    if (!empty($_POST['etiquetas_nuevas'])) {
        $nuevas = array_filter(array_map('trim', explode(',', $_POST['etiquetas_nuevas'])));
        foreach ($nuevas as $nombre_etiqueta) {
            $nombre_etiqueta = $conn->real_escape_string($nombre_etiqueta);
            $conn->query("INSERT IGNORE INTO etiquetas (nombre) VALUES ('$nombre_etiqueta')");
            $res = $conn->query("SELECT id_etiqueta FROM etiquetas WHERE nombre='$nombre_etiqueta'");
            $row = $res->fetch_assoc();
            $id_etiqueta = $row['id_etiqueta'];
            $conn->query("INSERT IGNORE INTO paquete_etiquetas (id_paquete, id_etiqueta) VALUES ($id_paquete, $id_etiqueta)");
        }
    }
    // Guardar etiquetas existentes
    if (!empty($_POST['etiquetas_existentes'])) {
        foreach ($_POST['etiquetas_existentes'] as $id_etiqueta) {
            $id_etiqueta = intval($id_etiqueta);
            $conn->query("INSERT IGNORE INTO paquete_etiquetas (id_paquete, id_etiqueta) VALUES ($id_paquete, $id_etiqueta)");
        }
    }

    header("Location: nuevo_paquete.php?id_paquete=$id_paquete&asociar=1");
    exit;
}

// Paso 2: Asociación de componentes
$id_paquete = isset($_GET['id_paquete']) ? intval($_GET['id_paquete']) : null;
$paquete = null;
$id_destino = null;
$destino = '';
if ($id_paquete) {
    $res = $conn->query("SELECT p.*, d.destino FROM paquetes_turisticos p LEFT JOIN destinos d ON p.id_destino = d.id_destino WHERE p.id_paquete = $id_paquete");
    $paquete = $res->fetch_assoc();
    if ($paquete) {
        $id_destino = $paquete['id_destino'];
        $destino = $paquete['destino'];
    }
}

// Traer etiquetas existentes
$etiquetas_existentes = [];
$res = $conn->query("SELECT id_etiqueta, nombre FROM etiquetas ORDER BY nombre");
while ($row = $res->fetch_assoc()) {
    $etiquetas_existentes[] = $row;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Paquete Turístico</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/nomadella/css/apartados.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
                <li class="breadcrumb-item"><a href="paquetes.php">Paquetes Turísticos</a></li>
                <li class="breadcrumb-item active" aria-current="page">Nuevo Paquete</li>
            </ol>
        </nav>
        <div class="card-paquete mt-4">
            <div class="icon-circle mb-3">
                <i class="bi bi-box2-heart"></i>
            </div>
            <h2 class="text-center mb-4" style="color:#750D37;font-weight:700;letter-spacing:1px;">Alta de Paquete Turístico</h2>
            <?php if (!$id_paquete): ?>
                <form method="post" autocomplete="off">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" required maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3" maxlength="255" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Destino</label>
                        <select name="id_destino" class="form-select" required>
                            <option value="">Seleccione destino</option>
                            <?php foreach ($destinos_disponibles as $dest): ?>
                                <option value="<?= $dest['id_destino'] ?>"
                                    <?= (isset($id_destino) && $id_destino == $dest['id_destino']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($dest['destino']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Precio base</label>
                        <input type="number" name="precio_base" class="form-control" required min="0" step="0.01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha de inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha de fin</label>
                        <input type="date" name="fecha_fin" class="form-control" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="activo" class="form-check-input" id="activo" checked>
                        <label class="form-check-label" for="activo">Activo</label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Agregar nueva etiqueta</label>
                        <input type="text" id="input-etiqueta" class="form-control" placeholder="Escribe y presiona Enter o coma para agregar">
                        <div id="etiquetas-container" class="mt-2"></div>
                        <input type="hidden" name="etiquetas_nuevas" id="etiquetas-hidden">
                        <small class="text-muted">Ejemplo: familiar, aventura, lujo, playa...</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Etiquetas existentes</label>
                        <div class="row">
                            <?php
                            $col = ceil(count($etiquetas_existentes) / 3);
                            foreach (array_chunk($etiquetas_existentes, $col) as $col_etiquetas):
                            ?>
                                <div class="col-md-4">
                                    <?php foreach ($col_etiquetas as $et): ?>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="etiquetas_existentes[]" value="<?= $et['id_etiqueta'] ?>" id="etiqueta<?= $et['id_etiqueta'] ?>">
                                            <label class="form-check-label" for="etiqueta<?= $et['id_etiqueta'] ?>">
                                                <?= htmlspecialchars($et['nombre']) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary px-4" id="btn-cancelar">Cancelar</button>
                        <a href="paquetes.php" class="btn btn-outline-primary px-4" id="btn-volver-paquetes">
                            <i class="bi bi-arrow-left"></i> Volver a Paquetes Turísticos
                        </a>
                        <button type="submit" class="btn btn-success px-4"><i class="bi bi-check-circle"></i> Siguiente</button>
                    </div>
                </form>
            <?php else: ?>
                <!-- Paso 2: Asociación de componentes -->
                <?php include 'componentes_destino.php'; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php include 'modal_proveedor.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="js/js_componentes_desino.js"></script>
    <script>
        // Etiquetas nuevas
        let etiquetas = [];
        function renderEtiquetas() {
            $('#etiquetas-container').html(
                etiquetas.map((et, i) =>
                    `<span class="badge bg-info text-dark me-1 mb-1" style="font-size:1rem;">
                    ${et}
                    <a href="#" onclick="eliminarEtiqueta(${i});return false;" style="color:#750D37;text-decoration:none;font-weight:bold;">&times;</a>
                </span>`
                ).join('')
            );
            $('#etiquetas-hidden').val(etiquetas.join(','));
        }
        function eliminarEtiqueta(idx) {
            etiquetas.splice(idx, 1);
            renderEtiquetas();
        }
        $('#input-etiqueta').on('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ',' || e.keyCode === 188) {
                e.preventDefault();
                let val = $(this).val().trim().replace(/,$/, '');
                if (val && !etiquetas.includes(val)) {
                    etiquetas.push(val);
                    renderEtiquetas();
                }
                $(this).val('');
            }
        });
        // Si hay etiquetas precargadas (por ejemplo, al volver atrás), puedes agregarlas aquí
        // etiquetas = [...];
        // renderEtiquetas();
    </script>
</body>
</html>
<?php
$conn->close();
?>