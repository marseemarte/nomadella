<?php
include 'conexion.php';
include 'verificar_admin.php';

// Obtenemos proveedores por tipo
function obtenerProveedores($conn, $tipo)
{
    $stmt = $conn->prepare("SELECT * FROM proveedores WHERE tipo = ? AND estado = 'activo' ORDER BY nombre ASC");
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            <div>
                <a href="historial_proveedores.php" class="btn btn-secondary mb-3">
                    <i class="bi bi-book"> Proveedores Viejos</i>
                </a>
                <a href="proveedor_form.php" class="btn btn-primary mb-3">
                    <i class="bi bi-plus-circle"></i> Nuevo Proveedor
                </a>    
            </div>
            
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
                    <!--ALOJAMIENTOS-->
                    <?php while ($a = $alojamientos->fetch_assoc()): ?>
                        <div class="col-md-4">
                            <div class="card p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title mb-0 flex-grow-1 me-2" style="word-break:break-word;"><?= htmlspecialchars($a['nombre']) ?></h5>
                                    <div class="d-flex gap-1">
                                        <a href="editar_proveedor.php?id=<?= $a['id_proveedor'] ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger" title="Eliminar"
                                            onclick="confirmarEliminacion(<?= $a['id_proveedor'] ?>)">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </div>
                                </div>
                                <p><?= nl2br($a['descripcion']) ?></p>
                                <p>
                                    <i class="bi bi-telephone"></i> <?= $a['telefono'] ?> <br>
                                    <i class="bi bi-envelope"></i> <?= $a['email'] ?> <br>
                                    <i class="bi bi-house"></i> <?= $a['direccion'] ?>
                                </p>
                                <span class="badge badge-azul"><?= ucfirst($a['tipo']) ?></span>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <!--VUELOS-->
            <div class="tab-pane fade" id="vuelos">
                <div class="row g-4">
                    <?php while ($v = $vuelos->fetch_assoc()): ?>
                        <div class="col-md-4">
                            <div class="card p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title mb-0 flex-grow-1 me-2" style="word-break:break-word;"><?= htmlspecialchars($v['nombre']) ?></h5>
                                    <div class="d-flex gap-1">
                                        <a href="editar_proveedor.php?id=<?= $v['id_proveedor'] ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger" title="Eliminar"
                                            onclick="confirmarEliminacion(<?= $v['id_proveedor'] ?>)">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </div>
                                </div>
                                <p><?= nl2br($v['descripcion']) ?></p>
                                <p>
                                    <i class="bi bi-telephone"></i> <?= $v['telefono'] ?> <br> 
                                    <i class="bi bi-envelope"></i> <?= $v['email'] ?> <br>
                                    <i class="bi bi-house"></i> <?= $v['direccion'] ?>
                                </p>
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
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title mb-0 flex-grow-1 me-2" style="word-break:break-word;"><?= htmlspecialchars($au['nombre']) ?></h5>
                                    <div class="d-flex gap-1">
                                        <a href="editar_proveedor.php?id=<?= $au['id_proveedor'] ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger" title="Eliminar"
                                            onclick="confirmarEliminacion(<?= $au['id_proveedor'] ?>)">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </div>
                                </div>
                                <p><?= nl2br($au['descripcion']) ?></p>
                                <p>
                                    <i class="bi bi-telephone"></i> <?= $au['telefono'] ?> <br> 
                                    <i class="bi bi-envelope"></i> <?= $au['email'] ?> <br>
                                    <i class="bi bi-house"></i> <?= $au['direccion'] ?>
                                </p>
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
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title mb-0 flex-grow-1 me-2" style="word-break:break-word;"><?= htmlspecialchars($s['nombre']) ?></h5>
                                    <div class="d-flex gap-1">
                                        <a href="editar_proveedor.php?id=<?= $s['id_proveedor'] ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger" title="Eliminar"
                                            onclick="confirmarEliminacion(<?= $s['id_proveedor'] ?>)">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </div>
                                </div>
                                <p><?= nl2br($s['descripcion']) ?></p>
                                <p>
                                    <i class="bi bi-telephone"></i> <?= $s['telefono'] ?> <br> 
                                    <i class="bi bi-envelope"></i> <?= $s['email'] ?> <br>
                                    <i class="bi bi-house"></i> <?= $s['direccion'] ?>
                                </p>
                                <span class="badge badge-azul"><?= ucfirst($s['tipo']) ?></span>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

        </div>

    </div>

    <!-- Modal de confirmación -->
    <div class="modal fade" id="modalConfirmarEliminar" tabindex="-1" aria-labelledby="modalConfirmarEliminarLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:2.5rem;"></i>
                    </div>
                    <h5 class="mb-3" id="modalConfirmarEliminarLabel">¿Estás seguro de que quieres desactivar?</h5>
                    <p class="mb-4">Esta información seguirá disponible en el registro.</p>
                    <button type="button" class="btn btn-danger px-4 me-2" id="btnEliminarConfirmado"><i class="bi bi-x-circle"></i> Desactivar</button>
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de éxito -->
    <div class="modal fade" id="modalEliminadoOk" tabindex="-1" aria-labelledby="modalEliminadoOkLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <i class="bi bi-check-circle-fill text-success" style="font-size:2.5rem;"></i>
                    </div>
                    <h5 class="mb-3" id="modalEliminadoOkLabel">¡Se ha desactivado correctamente!</h5>
                    <button type="button" class="btn btn-success px-4" data-bs-dismiss="modal" onclick="location.reload()">Aceptar</button>
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
                if (term.length < 2) {
                    $resultados.html('').hide();
                    return;
                }
                timeout = setTimeout(function() {
                    $.get('proveedor_search.php', {
                        term: term
                    }, function(data) {
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

            // Mostrar modal de éxito si se acaba de cargar un proveedor
            <?php if (isset($_GET['ok']) && $_GET['ok'] == 1): ?>
                setTimeout(function() {
                    $('#modalEliminadoOkLabel').text('¡Se ha cargado correctamente!');
                    var modalOk = new bootstrap.Modal(document.getElementById('modalEliminadoOk'));
                    modalOk.show();

                    // Quitamos el parámetro ok de la URL inmediatamente
                    if (window.history.replaceState) {
                        const url = new URL(window.location);
                        url.searchParams.delete('ok');
                        window.history.replaceState({}, document.title, url.pathname + url.search);
                    }
                }, 300);
            <?php endif; ?>

            // Mostrar modal de éxito si se acaba de editar un proveedor
            <?php if (isset($_GET['edit']) && $_GET['edit'] == 1): ?>
                setTimeout(function() {
                    $('#modalEliminadoOkLabel').text('¡Se ha editado correctamente!');
                    var modalOk = new bootstrap.Modal(document.getElementById('modalEliminadoOk'));
                    modalOk.show();

                    // Quitamos el parámetro edit de la URL inmediatamente
                    if (window.history.replaceState) {
                        const url = new URL(window.location);
                        url.searchParams.delete('edit');
                        window.history.replaceState({}, document.title, url.pathname + url.search);
                    }
                }, 300);
            <?php endif; ?>
        });

        let proveedorAEliminar = null;

        function confirmarEliminacion(id) {
            proveedorAEliminar = id;
            let modal = new bootstrap.Modal(document.getElementById('modalConfirmarEliminar'));
            modal.show();
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('btnEliminarConfirmado').onclick = function() {
                if (proveedorAEliminar) {
                    // AJAX para eliminar sin recargar
                    $.get('eliminar_proveedor.php', {
                        id: proveedorAEliminar
                    }, function() {
                        let modalConfirm = bootstrap.Modal.getInstance(document.getElementById('modalConfirmarEliminar'));
                        modalConfirm.hide();
                        let modalOk = new bootstrap.Modal(document.getElementById('modalEliminadoOk'));
                        modalOk.show();
                    });
                }
            };
        });
    </script>
</body>

</html>

<?php $conn->close(); ?>