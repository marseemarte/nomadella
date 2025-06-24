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
    $cupo = intval($_POST['cupo']);
    $fecha_inicio = $conn->real_escape_string($_POST['fecha_inicio']);
    $fecha_fin = $conn->real_escape_string($_POST['fecha_fin']);
    if ($fecha_inicio > $fecha_fin) {
        die("La fecha de inicio no puede ser posterior a la fecha de fin.");
    }
    $destino= $conn->real_escape_string($_POST['id_destino']);

    $activo = isset($_POST['activo']) ? 1 : 0;
    $id_destino = intval($_POST['id_destino']);

    $conn->query("INSERT INTO paquetes_turisticos (nombre, descripcion, id_destino, precio_base, cupo_disponible, destino, fecha_inicio, fecha_fin, activo)
                  VALUES ('$nombre', '$descripcion', $id_destino, $precio_base, $cupo, '$destino', '$fecha_inicio', '$fecha_fin', $activo)");
    $id_paquete = $conn->insert_id;

    // Guardar etiquetas nuevas
    if (!empty($_POST['etiquetas_nuevas'])) {
        $nuevas = array_filter(array_map('trim', explode(',', $_POST['etiquetas_nuevas'])));
        foreach ($nuevas as $nombre_etiqueta) {
            $nombre_etiqueta = $conn->real_escape_string($nombre_etiqueta);
            $conn->query("INSERT IGNORE INTO etiquetas (nombre) VALUES ('$nombre_etiqueta')");
            $res = $conn->query("SELECT * FROM etiquetas WHERE nombre='$nombre_etiqueta'");
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
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Nuevo Paquete Turístico</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/apartados.css">
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
                        <label class="form-label">Cupo Disponible</label>
                        <input type="number" name="cupo" class="form-control" required min="1" step="1" value="1">
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
                <form method="post" action="asociar_componentes_paquete.php?id_paquete=<?= $id_paquete ?>">
                    <h5 class="mb-3">Destino: <span class="text-success"><?= htmlspecialchars($destino) ?></span></h5>
                    <!-- Alojamientos -->
                    <div class="mb-4 bloque-componente bloque-alojamiento">
                        <input type="hidden" name="omitir_alojamiento" id="omitir_alojamiento" value="0">
                        <label class="form-label">Alojamientos en destino</label>
                        <?php if (empty($alojamientos)): ?>
                            <div class="alert alert-warning d-flex align-items-center justify-content-between">
                                <div>
                                    No hay alojamientos para este destino.
                                    <a href="proveedor_form.php?tipo=alojamiento&id_destino=<?= $id_destino ?>&id_paquete=<?= $id_paquete ?>" class="btn btn-sm btn-primary ms-2">
                                        <i class="bi bi-plus-circle"></i> Agregar alojamiento en <?= htmlspecialchars($destino) ?>
                                    </a>
                                </div>
                                <button type="button" class="btn btn-outline-secondary btn-sm ms-3 btn-omitir" data-omitir="alojamiento">
                                    Omitir por ahora
                                </button>
                            </div>
                        <?php else: ?>
                            <select name="alojamientos[]" class="form-select" multiple required>
                                <?php foreach ($alojamientos as $row): ?>
                                    <option value="<?= $row['id_alojamiento'] ?>"><?= htmlspecialchars($row['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Puede seleccionar varios (Ctrl + click)</small>
                        <?php endif; ?>
                    </div>

                    <!-- Vuelos -->
                    <div class="mb-4">
                        <label class="form-label">Vuelos al destino</label>
                        <?php if (empty($vuelos)): ?>
                            <div class="alert alert-warning d-flex align-items-center justify-content-between">
                                <div>
                                    No hay vuelos para este destino.
                                    <a href="proveedor_form.php?tipo=vuelo&id_destino=<?= $id_destino ?>&id_paquete=<?= $id_paquete ?>" class="btn btn-sm btn-primary ms-2">
                                        <i class="bi bi-plus-circle"></i> Agregar vuelo en <?= htmlspecialchars($destino) ?>
                                    </a>
                                </div>
                                <button type="button" class="btn btn-outline-secondary btn-sm ms-3 btn-omitir" data-omitir="vuelo">
                                    Omitir por ahora
                                </button>
                            </div>
                        <?php else: ?>
                            <select name="vuelos[]" class="form-select" multiple required>
                                <?php foreach ($vuelos as $row): ?>
                                    <option value="<?= $row['id_vuelo'] ?>"><?= htmlspecialchars($row['aerolinea']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Puede seleccionar varios (Ctrl + click)</small>
                        <?php endif; ?>
                    </div>

                    <!-- Autos -->
                    <div class="mb-4">
                        <label class="form-label">Alquiler de autos en destino</label>
                        <?php if (empty($autos)): ?>
                            <div class="alert alert-warning d-flex align-items-center justify-content-between">
                                <div>
                                    No hay autos para este destino.
                                    <a href="proveedor_form.php?tipo=auto&id_destino=<?= $id_destino ?>&id_paquete=<?= $id_paquete ?>" class="btn btn-sm btn-primary ms-2">
                                        <i class="bi bi-plus-circle"></i> Agregar auto en <?= htmlspecialchars($destino) ?>
                                    </a>
                                </div>
                                <button type="button" class="btn btn-outline-secondary btn-sm ms-3 btn-omitir" data-omitir="auto">
                                    Omitir por ahora
                                </button>
                            </div>
                        <?php else: ?>
                            <select name="autos[]" class="form-select" multiple required>
                                <?php foreach ($autos as $row): ?>
                                    <option value="<?= $row['id_alquiler'] ?>"><?= htmlspecialchars($row['proveedor']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Puede seleccionar varios (Ctrl + click)</small>
                        <?php endif; ?>
                    </div>

                    <!-- Servicios adicionales -->
                    <div class="mb-4">
                        <label class="form-label">Servicios adicionales en destino</label>
                        <?php if (empty($servicios)): ?>
                            <div class="alert alert-warning d-flex align-items-center justify-content-between">
                                <div>
                                    No hay servicios para este destino.
                                    <a href="proveedor_form.php?tipo=servicio&id_destino=<?= $id_destino ?>&id_paquete=<?= $id_paquete ?>" class="btn btn-sm btn-primary ms-2">
                                        <i class="bi bi-plus-circle"></i> Agregar servicio en <?= htmlspecialchars($destino) ?>
                                    </a>
                                </div>
                                <button type="button" class="btn btn-outline-secondary btn-sm ms-3 btn-omitir" data-omitir="servicio">
                                    Omitir por ahora
                                </button>
                            </div>
                        <?php else: ?>
                            <select name="servicios[]" class="form-select" multiple required>
                                <?php foreach ($servicios as $row): ?>
                                    <option value="<?= $row['id_servicio'] ?>"><?= htmlspecialchars($row['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Puede seleccionar varios (Ctrl + click)</small>
                        <?php endif; ?>
                    </div>
                    <input type="hidden" name="omitir_alojamiento" id="omitir_alojamiento" value="0">
                    <div class="d-flex justify-content-between">
                        <a href="nuevo_paquete.php?id_paquete=<?= $id_paquete ?>" class="btn btn-outline-primary px-4" id="btn-volver-datos">
                            <i class="bi bi-arrow-left"></i> Volver a datos del paquete
                        </a>
                        <a href="paquetes.php" class="btn btn-secondary px-4" id="btn-volver-paquetes">
                            Volver a Paquetes Turísticos
                        </a>
                        <button type="submit" class="btn btn-success px-4" name="asociar" value="1"><i class="bi bi-check-circle"></i> Guardar Paquete</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
    <div class="modal fade" id="modalSalir" tabindex="-1" aria-labelledby="modalSalirLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:2.5rem;"></i>
                    </div>
                    <h5 class="mb-3" id="modalSalirLabel">¿Está seguro?</h5>
                    <p class="mb-4">Se perderá todo el progreso de este formulario.</p>
                    <button type="button" class="btn btn-danger px-4 me-2" id="btnSalirConfirmado">Salir</button>
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalOmitirServicio" tabindex="-1" aria-labelledby="modalOmitirServicioLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size:2.5rem;"></i>
                    </div>
                    <h5 class="mb-3" id="modalOmitirServicioLabel">¿Está seguro?</h5>
                    <p class="mb-4">Podrá editar esta información más tarde.</p>
                    <button type="button" class="btn btn-success px-4 me-2" id="btnConfirmarOmitirServicio">Omitir y continuar</button>
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $('form').on('submit', function(event) {
            console.log("Se va a enviar: ", $('#etiquetas-hidden').val());
            console.log("Form submission triggered.");
            // Add a timeout to check if form submission proceeds
            setTimeout(function() {
                console.log("If you see this message and the page did not reload, form submission might be blocked.");
            }, 3000);
        });

        window.addEventListener('error', function(event) {
            console.error("JavaScript error detected: ", event.message, " at ", event.filename, ":", event.lineno);
        });

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

        // Select2 para destinos
        $('#input-destino').select2({
            placeholder: 'Buscar o agregar destino',
            ajax: {
                url: 'buscar_destinos.php',
                dataType: 'json',
                delay: 250,
                data: params => ({
                    term: params.term
                }),
                processResults: data => ({
                    results: data.map(d => ({
                        id: d.id_destino,
                        text: d.destino
                    }))
                }),
                cache: true
            },
            language: {
                noResults: function(params) {
                    if (params.term && params.term.length > 0) {
                        return `<button type="button" class="btn btn-link p-0" id="agregar-destino-btn">Agregar "${params.term}" como nuevo destino</button>`;
                    }
                    return "No se encontraron resultados";
                }
            },
            escapeMarkup: function(markup) {
                return markup;
            }
        });

        // Agregar destino desde el botón en el dropdown
        $(document).on('click', '#agregar-destino-btn', function(e) {
            let term = $('.select2-search__field').val();
            $.post('agregar_destino.php', {
                destino: term
            }, function(data) {
                let newOption = new Option(data.destino, data.id_destino, true, true);
                $('#input-destino').append(newOption).trigger('change');
                $('.select2-results__options').empty();
            }, 'json');
        });

        // Modal de confirmación para salir
        let salirA = null;
        $('#btn-cancelar').on('click', function(e) {
            e.preventDefault();
            window.history.back();
        });
        $('#btn-volver-datos, #btn-volver-paquetes, .breadcrumb a, .sidebar .nav-link').on('click', function(e) {
            if ($(this).attr('href') && !$(this).hasClass('active')) {
                e.preventDefault();
                salirA = $(this).attr('href');
                let modal = new bootstrap.Modal(document.getElementById('modalSalir'));
                modal.show();
            }
        });
        $('#btnSalirConfirmado').on('click', function() {
            if (salirA) window.location = salirA;
        });

        // Confirmar omitir servicio
        $('#btn-omitir-servicio').on('click', function(e) {
            e.preventDefault();
            let modal = new bootstrap.Modal(document.getElementById('modalOmitirServicio'));
            modal.show();
        });
        // Confirmar omitir cualquier componente
        let tipoAomitir = null;
        let btnAomitir = null;

        $(document).on('click', '.btn-omitir', function(e) {
            e.preventDefault();
            tipoAomitir = $(this).data('omitir');
            btnAomitir = $(this);
            const modal = new bootstrap.Modal(document.getElementById('modalOmitirServicio'));
            modal.show();
        });

        $('#btnConfirmarOmitirServicio').on('click', function() {
            if (tipoAomitir && btnAomitir) {
                // Cambia el botón a verde y desactiva el select
                btnAomitir
                    .removeClass('btn-outline-secondary')
                    .addClass('btn-success')
                    .text('Omitido')
                    .prop('disabled', true);

                // Desactiva el select correspondiente
                btnAomitir.closest('.bloque-componente').find('select').prop('disabled', true);

                // Marca el input hidden
                $('#omitir_' + tipoAomitir).val('1');

                // Cierra el modal
                const modalEl = document.getElementById('modalOmitirServicio');
                const modal = bootstrap.Modal.getInstance(modalEl);
                modal.hide();
            }
        });
    </script>
</body>
<script>
    $(document).ready(function() {
        // Get URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const idNuevo = urlParams.get('id_nuevo');
        const tipo = urlParams.get('tipo');

        if (idNuevo && tipo) {
            // Map tipo to select name attribute
            const tipoMap = {
                'alojamiento': 'alojamientos[]',
                'vuelo': 'vuelos[]',
                'auto': 'autos[]',
                'servicio': 'servicios[]'
            };

            const selectName = tipoMap[tipo];
            if (selectName) {
                // Find the select element
                const selectElem = $(`select[name="${selectName}"]`);
                if (selectElem.length) {
                    // Check if option with idNuevo exists
                    let option = selectElem.find(`option[value="${idNuevo}"]`);
                    if (option.length === 0) {
                        // Option not found, fetch it via AJAX and append
                        $.ajax({
                            url: 'proveedor_search.php',
                            method: 'GET',
                            data: { id: idNuevo },
                            dataType: 'json',
                            success: function(data) {
                                if (data && data.length > 0) {
                                    const prov = data[0];
                                    let optionText = prov.nombre || '';
                                    if(optionText) {
                                        const newOption = new Option(optionText, idNuevo, true, true);
                                        selectElem.append(newOption).trigger('change');
                                    }
                                }
                            }
                        });
                    } else {
                        // Option exists, select it
                        option.prop('selected', true);
                        selectElem.trigger('change');
                    }
                }
            }
        }
    });
</script>
</html>
<?php
if (isset($_SESSION['id_usuario'])) {
    registrar_bitacora(
        $pdo,
        $_SESSION['id_usuario'],
        'Crear paquete',
        "Paquete #$id_paquete creado: $nombre"
    );
}
$conn->close();
?>
