<?php
include './sidebar.php';
include './conexion.php'; // archivo de conexión PDO a tu base 'nomadella'
//include './verificar_admin.php'; // archivo para verificar si el usuario es admin
// Obtener paquetes
$paquetes = $pdo->query("SELECT * FROM paquetes_turisticos")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Paquetes Turísticos - Nomadella</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/nomadella/css/apartados.css">
</head>

<body>
    <div class="main-content">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Paquetes Turísticos</li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Paquetes Turísticos</h2>
            <div>
                <a href="nuevo_paquete.php" class="btn btn-primary mb-3">
                    <i class="bi bi-plus-circle"></i> Nuevo Paquete
                </a>
            </div>

        </div>

        <div class="row mb-3">
            <div class="col-md-4 mb-2">
                <input type="text" id="buscador-paquete" class="form-control" placeholder="Buscar por título o destino...">
            </div>
            <div class="col-md-2 mb-2">
                <select id="filtro-etiqueta" class="form-select">
                    <option value="">Todas las etiquetas</option>
                    <?php
                    $etiquetas = $pdo->query("SELECT id_etiqueta, nombre FROM etiquetas ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($etiquetas as $et) {
                        echo '<option value="' . $et['id_etiqueta'] . '">' . htmlspecialchars($et['nombre']) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <select id="filtro-destino" class="form-select">
                    <option value="">Todos los destinos</option>
                    <?php
                    $destinos = $pdo->query("SELECT id_destino, destino FROM destinos ORDER BY destino")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($destinos as $d) {
                        echo '<option value="' . $d['id_destino'] . '">' . htmlspecialchars($d['destino']) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <select id="filtro-estado" class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                </select>
            </div>
        </div>

        <div id="paquetes-lista">
            <?php foreach ($paquetes as $paquete): ?>
                <?php
                $id_paquete = $paquete['id_paquete'];

                // ALOJAMIENTOS
                $stmt = $pdo->prepare("SELECT a.nombre, a.direccion, a.ciudad, a.categoria FROM alojamientos a JOIN paquete_alojamientos pa ON a.id_alojamiento = pa.id_alojamiento WHERE pa.id_paquete = ?");
                $stmt->execute([$id_paquete]);
                $alojamientos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // AUTOS
                $stmt = $pdo->prepare("SELECT aa.proveedor, aa.tipo_vehiculo, aa.precio_por_dia FROM alquiler_autos aa JOIN paquete_autos pa ON aa.id_alquiler = pa.id_alquiler WHERE pa.id_paquete = ?");
                $stmt->execute([$id_paquete]);
                $autos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // VUELOS
                $stmt = $pdo->prepare("SELECT v.aerolinea, v.origen, v.destino, v.precio_base FROM vuelos v JOIN paquete_vuelos pv ON v.id_vuelo = pv.id_vuelo WHERE pv.id_paquete = ?");
                $stmt->execute([$id_paquete]);
                $vuelos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // SERVICIOS ADICIONALES
                $stmt = $pdo->prepare("SELECT s.nombre, s.descripcion, s.precio FROM servicios_adicionales s JOIN paquete_servicios ps ON s.id_servicio = ps.id_servicio WHERE ps.id_paquete = ?");
                $stmt->execute([$id_paquete]);
                $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // ETIQUETAS
                $stmt = $pdo->prepare("SELECT e.nombre FROM etiquetas e JOIN paquete_etiquetas pe ON e.id_etiqueta = pe.id_etiqueta WHERE pe.id_paquete = ?");
                $stmt->execute([$id_paquete]);
                $etiquetas = $stmt->fetchAll(PDO::FETCH_COLUMN);
                ?>

                <div class="card-paquete">
                    <div class="row">
                        <div class="col-md-6">
                            <h4 style="color:#750D37"><?= htmlspecialchars($paquete['nombre']) ?></h4>
                            <p><?= htmlspecialchars($paquete['descripcion']) ?></p>

                            <h6><i class="bi bi-houses"></i> Alojamiento:</h6>
                            <?php foreach ($alojamientos as $a): ?>
                                <p> <?= htmlspecialchars($a['nombre']) ?> - <?= htmlspecialchars($a['ciudad']) ?> (<?= htmlspecialchars($a['categoria']) ?>)</p>
                            <?php endforeach; ?>

                            <h6><i class="bi bi-car-front"></i> Alquiler de Auto:</h6>
                            <?php foreach ($autos as $a): ?>
                                <p> <?= htmlspecialchars($a['proveedor']) ?> - <?= htmlspecialchars($a['tipo_vehiculo']) ?> - $<?= number_format($a['precio_por_dia'], 2) ?>/día</p>
                            <?php endforeach; ?>

                            <h6><i class="bi bi-airplane"></i> Vuelos:</h6>
                            <?php foreach ($vuelos as $v): ?>
                                <p> <?= htmlspecialchars($v['aerolinea']) ?> (<?= htmlspecialchars($v['origen']) ?> → <?= htmlspecialchars($v['destino']) ?>) - $<?= number_format($v['precio_base'], 2) ?></p>
                            <?php endforeach; ?>

                            <h6><i class="bi bi-plus-circle"></i> Servicios Adicionales:</h6>
                            <?php foreach ($servicios as $s): ?>
                                <p> <?= htmlspecialchars($s['nombre']) ?> - <?= htmlspecialchars($s['descripcion']) ?> - $<?= number_format($s['precio'], 2) ?></p>
                            <?php endforeach; ?>

                        </div>
                        <div class="col-md-6 d-flex flex-column justify-content-between">
                            <div>
                                <h6>Etiquetas:</h6>
                                <div class="d-flex flex-wrap">
                                    <?php foreach ($etiquetas as $et): ?>
                                        <span class="etiqueta"><?= htmlspecialchars($et) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div>
                                <div>
                                    <p>Precio Base: <b>$<?= number_format($paquete['precio_base'], 2) ?></b></p>
                                    <p>Destino: <?= htmlspecialchars($paquete['destino']) ?></p>
                                    <p>Fecha: <?= date("d/m/Y", strtotime($paquete['fecha_inicio'])) ?> - <?= date("d/m/Y", strtotime($paquete['fecha_fin'])) ?></p>
                                </div>
                                <div>
                                    <p>Estado:
                                        <?php if ($paquete['activo'] == '1'): ?>
                                            <span class="text-success fw-bold">Activo</span>
                                        <?php else: ?>
                                            <span class="text-danger fw-bold">Inactivo</span>
                                        <?php endif; ?>
                                    </p>
                                </div>

                                <a href="editar_paquete.php?id=<?= $id_paquete ?>" class="btn btn-editar"><i class="bi bi-pencil-square"></i> EDITAR</a>
                                <?php if ($paquete['activo']): ?>
                                    <a href="#" id="btn-toggle-<?= $paquete['id_paquete'] ?>" class="btn btn-danger ms-2"
                                        onclick="confirmarEliminacion(<?= $paquete['id_paquete'] ?>); return false;">
                                        <i class="bi bi-x-circle"></i> Desactivar
                                    </a>
                                <?php else: ?>
                                    <a href="#" id="btn-toggle-<?= $paquete['id_paquete'] ?>" class="btn btn-success ms-2"
                                        onclick="confirmarReactivacion(<?= $paquete['id_paquete'] ?>); return false;">
                                        <i class="bi bi-check-circle"></i> Reactivar
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>

    <!-- Modal de confirmación de eliminación -->
    <div class="modal fade" id="modalConfirmarEliminar" tabindex="-1" aria-labelledby="modalConfirmarEliminarLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:2.5rem;"></i>
                    </div>
                    <h5 class="mb-3" id="modalConfirmarEliminarLabel">¿Estás seguro de que quieres desactivar?</h5>
                    <p class="mb-4">Este paquete pasara a estar inactivo.</p>
                    <button type="button" class="btn btn-danger px-4 me-2" id="btnEliminarConfirmado"><i class="bi bi-x-circle"></i> Desactivar</button>
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal de confirmación de reactivación -->
    <div class="modal fade" id="modalConfirmarReactivar" tabindex="-1" aria-labelledby="modalConfirmarReactivarLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <i class="bi bi-check-circle-fill text-success" style="font-size:2.5rem;"></i>
                    </div>
                    <h5 class="mb-3" id="modalConfirmarReactivarLabel">¿Estás seguro de que quieres reactivar?</h5>
                    <p class="mb-4">Este paquete volverá a estar activo.</p>
                    <button type="button" class="btn btn-success px-4 me-2" id="btnReactivarConfirmado"><i class="bi bi-check-circle"></i> Reactivar</button>
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal de éxito ABM -->
    <div class="modal fade" id="modalAbmOk" tabindex="-1" aria-labelledby="modalAbmOkLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <i class="bi bi-check-circle-fill text-success" style="font-size:2.5rem;"></i>
                    </div>
                    <h5 class="mb-3" id="modalAbmOkLabel">¡Operación realizada correctamente!</h5>
                    <button type="button" class="btn btn-success px-4" data-bs-dismiss="modal" onclick="location.reload()">Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let paqueteAEliminar = null;
        let paqueteAReactivar = null;

        function confirmarEliminacion(id) {
            paqueteAEliminar = id;
            let modal = new bootstrap.Modal(document.getElementById('modalConfirmarEliminar'));
            modal.show();
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('btnEliminarConfirmado').onclick = function() {
                if (paqueteAEliminar) {
                    $.get('eliminar_paquete.php', {
                        id: paqueteAEliminar
                    }, function(response) {
                        if (response.trim() === 'OK') {
                            bootstrap.Modal.getInstance(document.getElementById('modalConfirmarEliminar')).hide();
                            new bootstrap.Modal(document.getElementById('modalAbmOk')).show();
                            $('#modalAbmOkLabel').text('¡Se ha desactivado correctamente!');

                            // Cambiar botón a "Reactivar"
                            $('#btn-toggle-' + paqueteAEliminar)
                                .removeClass('btn-danger')
                                .addClass('btn-success')
                                .html('<i class="bi bi-check-circle"></i> Reactivar')
                                .attr('onclick', `confirmarReactivacion(${paqueteAEliminar}); return false;`);
                        } else {
                            alert('Error al desactivar');
                        }
                    });
                }
            };
            // Confirmar reactivación
            document.getElementById('btnReactivarConfirmado').onclick = function() {
                if (paqueteAReactivar) {
                    $.get('reactivar_paquete.php', {
                        id: paqueteAReactivar
                    }, function(response) {
                        if (response.trim() === 'OK') {
                            bootstrap.Modal.getInstance(document.getElementById('modalConfirmarReactivar')).hide();
                            new bootstrap.Modal(document.getElementById('modalAbmOk')).show();
                            $('#modalAbmOkLabel').text('¡Se ha reactivado correctamente!');

                            // Cambiar dinámicamente el botón a "Desactivar"
                            $('#btn-toggle-' + paqueteAReactivar)
                                .removeClass('btn-success')
                                .addClass('btn-danger')
                                .html('<i class="bi bi-x-circle"></i> Desactivar')
                                .attr('onclick', `confirmarEliminacion(${paqueteAReactivar}); return false;`);
                        } else {
                            alert("Error al reactivar.");
                        }
                    });
                }
            };



            // Modal de éxito para alta y edición
            <?php if (isset($_GET['ok']) && $_GET['ok'] == 1): ?>
                setTimeout(function() {
                    $('#modalAbmOkLabel').text('¡Se ha cargado correctamente!');
                    var modalOk = new bootstrap.Modal(document.getElementById('modalAbmOk'));
                    modalOk.show();
                    if (window.history.replaceState) {
                        const url = new URL(window.location);
                        url.searchParams.delete('ok');
                        window.history.replaceState({}, document.title, url.pathname + url.search);
                    }
                }, 300);
            <?php endif; ?>
            <?php if (isset($_GET['edit']) && $_GET['edit'] == 1): ?>
                setTimeout(function() {
                    $('#modalAbmOkLabel').text('¡Se ha editado correctamente!');
                    var modalOk = new bootstrap.Modal(document.getElementById('modalAbmOk'));
                    modalOk.show();
                    if (window.history.replaceState) {
                        const url = new URL(window.location);
                        url.searchParams.delete('edit');
                        window.history.replaceState({}, document.title, url.pathname + url.search);
                    }
                }, 300);
            <?php endif; ?>
        });

        function renderPaquetes(paquetes) {
            let html = '';
            if (paquetes.length === 0) {
                html = '<div class="alert alert-warning">No se encontraron paquetes.</div>';
            }
            paquetes.forEach(function(paquete) {
                html += `
            <div class="card-paquete mb-3">
                <div class="row">
                    <div class="col-md-6">
                        <h4 style="color:#750D37">${paquete.nombre}</h4>
                        <p>${paquete.descripcion}</p>
                        <h6><i class="bi bi-houses"></i> Alojamiento:</h6>
                        ${(paquete.alojamientos||[]).map(a => `<p>${a.nombre} - ${a.ciudad} (${a.categoria})</p>`).join('')}
                        <h6><i class="bi bi-car-front"></i> Alquiler de Auto:</h6>
                        ${(paquete.autos||[]).map(a => `<p>${a.proveedor} - ${a.tipo_vehiculo} - $${parseFloat(a.precio_por_dia).toLocaleString('es-AR', {minimumFractionDigits:2})}/día</p>`).join('')}
                        <h6><i class="bi bi-airplane"></i> Vuelos:</h6>
                        ${(paquete.vuelos||[]).map(v => `<p>${v.aerolinea} (${v.origen} → ${v.destino}) - $${parseFloat(v.precio_base).toLocaleString('es-AR', {minimumFractionDigits:2})}</p>`).join('')}
                        <h6><i class="bi bi-plus-circle"></i> Servicios Adicionales:</h6>
                        ${(paquete.servicios||[]).map(s => `<p>${s.nombre} - ${s.descripcion} - $${parseFloat(s.precio).toLocaleString('es-AR', {minimumFractionDigits:2})}</p>`).join('')}
                    </div>
                    <div class="col-md-6 d-flex flex-column justify-content-between">
                        <div>
                            <h6>Etiquetas:</h6>
                            <div class="d-flex flex-wrap">
                                ${(paquete.etiquetas || []).map(et => `<span class="etiqueta">${et}</span>`).join(' ')}
                            </div>
                        </div>
                        <div>
                            <p>Precio: <b>$${parseFloat(paquete.precio_base).toLocaleString('es-AR', {minimumFractionDigits:2})}</b></p>
                            <p>Destino: ${paquete.destino}</p>
                            <p>Fecha: ${new Date(paquete.fecha_inicio).toLocaleDateString()} - ${new Date(paquete.fecha_fin).toLocaleDateString()}</p>
                            <p>Estado: ${paquete.activo == 1 ? '<span class="text-success fw-bold">Activo</span>' : '<span class="text-danger fw-bold">Inactivo</span>'}</p>
                            <a href="editar_paquete.php?id=${paquete.id_paquete}" class="btn btn-editar"><i class="bi bi-pencil-square"></i> EDITAR</a>
                        <a href="#"
                            id="btn-toggle-${paquete.id_paquete}"
                            class="btn ${paquete.activo == 1 ? 'btn-danger' : 'btn-success'} ms-2"
                            onclick="${paquete.activo == 1 ? `confirmarEliminacion(${paquete.id_paquete})` : `confirmarReactivacion(${paquete.id_paquete})`}; return false;">
                            <i class="bi ${paquete.activo == 1 ? 'bi-x-circle' : 'bi-check-circle'}"></i>
                            ${paquete.activo == 1 ? 'Desactivar' : 'Reactivar'}
                        </a>
                        </div>
                    </div>
                </div>
            </div>
            `;
            });
            $('#paquetes-lista').html(html);
        }

        function buscarPaquetes() {
            let q = $('#buscador-paquete').val();
            let etiqueta = $('#filtro-etiqueta').val();
            let destino = $('#filtro-destino').val();
            let estado = $('#filtro-estado').val();
            $.get('buscar_paquetes.php', {
                q: q,
                etiqueta: etiqueta,
                destino: destino,
                estado: estado
            }, function(data) {
                renderPaquetes(data);
            }, 'json');
        }

        $('#buscador-paquete, #filtro-etiqueta, #filtro-destino, #filtro-estado').on('input change', function() {
            buscarPaquetes();
        });

        // Carga inicial
        $(document).ready(function() {
            buscarPaquetes();
        });

        function confirmarEliminacion(id) {
            paqueteAEliminar = id;
            let modal = new bootstrap.Modal(document.getElementById('modalConfirmarEliminar'));
            modal.show();
        }

        function confirmarReactivacion(id) {
            paqueteAReactivar = id;
            let modal = new bootstrap.Modal(document.getElementById('modalConfirmarReactivar'));
            modal.show();
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Confirmar eliminación
            document.getElementById('btnEliminarConfirmado').onclick = function() {
                if (paqueteAEliminar) {
                    // AJAX para desactivar sin recargar
                    $.get('eliminar_paquete.php', {
                        id: paqueteAEliminar
                    }, function() {
                        let modalConfirm = bootstrap.Modal.getInstance(document.getElementById('modalConfirmarEliminar'));
                        modalConfirm.hide();
                        let modalOk = new bootstrap.Modal(document.getElementById('modalAbmOk'));
                        $('#modalAbmOkLabel').text('¡Se ha desactivado correctamente!');
                        modalOk.show();
                        // Recargar lista o página para reflejar cambios
                        setTimeout(() => location.reload(), 1500);
                    });
                }
            };
        });
    </script>
</body>

</html>