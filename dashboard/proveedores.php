<?php
include 'conexion.php';

// Obtenemos proveedores por tipo
function obtenerProveedores($conn, $tipo)
{
    $stmt = $conn->prepare("SELECT * FROM proveedores WHERE tipo = ?");
    $stmt->bind_param("s", $tipo);
    $stmt->execute();
    return $stmt->get_result();
}

$alojamientos = obtenerProveedores($conn, 'alojamiento');
$vuelos = obtenerProveedores($conn, 'vuelo');
$autos = obtenerProveedores($conn, 'auto');
$servicios = obtenerProveedores($conn, 'servicio');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Proveedores</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #FFF6F8;
            color: #1A001C;
        }

        .main-content {
            margin-left: 260px;
            padding: 40px;
            min-height: 100vh;
        }

        .breadcrumb-item a {
            color: #750D37;
        }

        .nav-link {
            color: #750D37;
            font-weight: bold;
        }

        .card {
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            color: #750D37;
            font-weight: 600;
        }

        .badge-azul {
            background: #5CC7ED;
            color: #fff;
        }
    </style>
</head>

<body>
    <?php include './sidebar.php' ?>
    <div class="main-content">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Proveedores</li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Proveedores</h2>
            <a href="proveedor_form.php" class="btn btn-primary mb-3">
                <i class="bi bi-plus-circle"></i> Nuevo Proveedor
            </a>
        </div>
        <div class="mb-4 position-relative" style="z-index:10;">
            <input type="text" class="form-control w-50" id="buscador" placeholder="Buscar proveedor...">
            <div id="resultados" class="position-absolute bg-white border mt-1 w-50 z-3 rounded" style="display:none"></div>
        </div>

        <ul class="nav nav-tabs mb-4" id="tabsProveedor" role="tablist">
            <li class="nav-item"><a class="nav-link active" id="aloj-tab" data-bs-toggle="tab" href="#alojamiento">Alojamientos</a></li>
            <li class="nav-item"><a class="nav-link" id="vuel-tab" data-bs-toggle="tab" href="#vuelos">Vuelos</a></li>
            <li class="nav-item"><a class="nav-link" id="auto-tab" data-bs-toggle="tab" href="#autos">Alquiler de Autos</a></li>
            <li class="nav-item"><a class="nav-link" id="serv-tab" data-bs-toggle="tab" href="#servicios">Servicios Adicionales</a></li>
        </ul>

        <div class="tab-content">

            <div class="tab-pane fade show active" id="alojamiento">
                <div class="row g-4">
                    <?php while ($a = $alojamientos->fetch_assoc()): ?>
                        <div class="col-md-4">
                            <div class="card p-3">
                                <h5 class="card-title"><?= htmlspecialchars($a['nombre']) ?></h5>
                                <p><?= nl2br($a['descripcion']) ?></p>
                                <p><i class="bi bi-telephone"></i> <?= $a['telefono'] ?> <br> <i class="bi bi-envelope"></i> <?= $a['email'] ?></p>
                                <span class="badge badge-azul"><?= ucfirst($a['tipo']) ?></span>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <div class="tab-pane fade" id="vuelos">
                <div class="row g-4">
                    <?php while ($v = $vuelos->fetch_assoc()): ?>
                        <div class="col-md-4">
                            <div class="card p-3">
                                <h5 class="card-title"><?= htmlspecialchars($v['nombre']) ?></h5>
                                <p><?= nl2br($v['descripcion']) ?></p>
                                <p><i class="bi bi-telephone"></i> <?= $v['telefono'] ?> <br> <i class="bi bi-envelope"></i> <?= $v['email'] ?></p>
                                <span class="badge badge-azul"><?= ucfirst($v['tipo']) ?></span>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <div class="tab-pane fade" id="autos">
                <div class="row g-4">
                    <?php while ($au = $autos->fetch_assoc()): ?>
                        <div class="col-md-4">
                            <div class="card p-3">
                                <h5 class="card-title"><?= htmlspecialchars($au['nombre']) ?></h5>
                                <p><?= nl2br($au['descripcion']) ?></p>
                                <p><i class="bi bi-telephone"></i> <?= $au['telefono'] ?> <br> <i class="bi bi-envelope"></i> <?= $au['email'] ?></p>
                                <span class="badge badge-azul"><?= ucfirst($au['tipo']) ?></span>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <div class="tab-pane fade" id="servicios">
                <div class="row g-4">
                    <?php while ($s = $servicios->fetch_assoc()): ?>
                        <div class="col-md-4">
                            <div class="card p-3">
                                <h5 class="card-title"><?= htmlspecialchars($s['nombre']) ?></h5>
                                <p><?= nl2br($s['descripcion']) ?></p>
                                <p><i class="bi bi-telephone"></i> <?= $s['telefono'] ?> <br> <i class="bi bi-envelope"></i> <?= $s['email'] ?></p>
                                <span class="badge badge-azul"><?= ucfirst($s['tipo']) ?></span>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
$(document).ready(function() {
    let $buscador = $('#buscador');
    let $resultados = $('#resultados');
    let timeout = null;

    $buscador.on('input', function() {
        clearTimeout(timeout);
        let term = $(this).val();
        if (term.length < 2) { $resultados.html('').hide(); return; }
        timeout = setTimeout(function() {
            $.get('proveedor_search.php', {term: term}, function(data) {
                if (data.length) {
                    let list = '<ul class="list-group mb-0">';
                    data.forEach(p => {
                        list += `<li class="list-group-item list-group-item-action" style="cursor:pointer" data-id="${p.id}">${p.label}</li>`;
                    });
                    list += '</ul>';
                    $resultados.html(list).show();
                } else {
                    $resultados.html('<div class="p-2 text-muted">Sin resultados</div>').show();
                }
            }, 'json');
        }, 200);
    });

    // Click en resultado
    $resultados.on('click', '.list-group-item', function() {
        let id = $(this).data('id');
        window.location = 'proveedor_form.php?id=' + id;
    });

    // Ocultar resultados al hacer click fuera
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#buscador, #resultados').length) {
            $resultados.hide();
        }
    });

    // Mostrar resultados si el input recupera el foco y hay resultados
    $buscador.on('focus', function() {
        if ($resultados.html().trim() !== '') $resultados.show();
    });
});
</script>
</body>

</html>

<?php $conn->close(); ?>