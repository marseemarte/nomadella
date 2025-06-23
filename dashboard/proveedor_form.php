<?php
include 'conexion.php';
include 'verificar_admin.php';

// Obtener destinos disponibles
$destinos_disponibles = [];
$res = $conn->query("SELECT id_destino, destino FROM destinos ORDER BY destino");
while ($row = $res->fetch_assoc()) {
    $destinos_disponibles[] = $row;
}

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $tipo = $conn->real_escape_string($_POST['tipo']);
    $contacto = $conn->real_escape_string($_POST['contacto']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $email = $conn->real_escape_string($_POST['email']);
    $direccion = $conn->real_escape_string($_POST['direccion']);
    $descripcion = $conn->real_escape_string($_POST['descripcion']);
    $ciudad = isset($_POST['ciudad']) ? $conn->real_escape_string($_POST['ciudad']) : '';
    $id_destino = intval($_POST['id_destino']);

    $conn->query("INSERT INTO proveedores (nombre, tipo, contacto, telefono, email, direccion, descripcion, id_destino) 
                  VALUES ('$nombre', '$tipo', '$contacto', '$telefono', '$email', '$direccion', '$descripcion', $id_destino)");

    $id_nuevo = $conn->insert_id;

    // Insert into specific tables based on tipo
    switch ($tipo) {
        case 'alojamiento':
            $precio_por_dia = isset($_POST['precio_por_dia_alojamiento']) ? floatval($_POST['precio_por_dia_alojamiento']) : 0;
            $categoria = isset($_POST['categoria_alojamiento']) ? $conn->real_escape_string($_POST['categoria_alojamiento']) : '';
            $conn->query("INSERT INTO alojamientos (id_proveedor, id_destino, nombre, precio_por_dia, categoria) VALUES ($id_nuevo, $id_destino, '$nombre', $precio_por_dia, '$categoria')");
            break;
        case 'vuelo':
            $id_vuelo = isset($_POST['id_vuelo']) ? $conn->real_escape_string($_POST['id_vuelo']) : '';
            $codigo_vuelo = isset($_POST['codigo_vuelo']) ? $conn->real_escape_string($_POST['codigo_vuelo']) : '';
            $aerolinea = isset($_POST['aerolinea_vuelo']) ? $conn->real_escape_string($_POST['aerolinea_vuelo']) : '';
            $origen = isset($_POST['origen_vuelo']) ? $conn->real_escape_string($_POST['origen_vuelo']) : '';
            $destino_vuelo = isset($_POST['destino_vuelo']) ? $conn->real_escape_string($_POST['destino_vuelo']) : '';
            $id_destino_vuelo = isset($_POST['id_destino_vuelo']) ? intval($_POST['id_destino_vuelo']) : 0;
            $fecha_salida = isset($_POST['fecha_salida']) ? $conn->real_escape_string($_POST['fecha_salida']) : '';
            $fecha_llegada = isset($_POST['fecha_llegada']) ? $conn->real_escape_string($_POST['fecha_llegada']) : '';
            $precio_base = isset($_POST['precio_base']) ? floatval($_POST['precio_base']) : 0;
            $conn->query("INSERT INTO vuelos (id_proveedor, id_destino, id_vuelo, codigo_vuelo, aerolinea, origen, destino, id_destino, fecha_salida, fecha_llegada, precio_base) VALUES ($id_nuevo, $id_destino, '$id_vuelo', '$codigo_vuelo', '$aerolinea', '$origen', '$destino_vuelo', $id_destino_vuelo, '$fecha_salida', '$fecha_llegada', $precio_base)");
            break;
        case 'auto':
            $id_alquiler = isset($_POST['id_alquiler']) ? $conn->real_escape_string($_POST['id_alquiler']) : '';
            $proveedor_auto = isset($_POST['proveedor_auto']) ? $conn->real_escape_string($_POST['proveedor_auto']) : '';
            $tipo_vehiculo = isset($_POST['tipo_vehiculo']) ? $conn->real_escape_string($_POST['tipo_vehiculo']) : '';
            $ubicacion_retiro = isset($_POST['ubicacion_retiro']) ? $conn->real_escape_string($_POST['ubicacion_retiro']) : '';
            $id_destino_auto = isset($_POST['id_destino_auto']) ? intval($_POST['id_destino_auto']) : 0;
            $ubicacion_entrega = isset($_POST['ubicacion_entrega']) ? $conn->real_escape_string($_POST['ubicacion_entrega']) : '';
            $precio_por_dia_auto = isset($_POST['precio_por_dia_auto']) ? floatval($_POST['precio_por_dia_auto']) : 0;
            $condiciones = isset($_POST['condiciones']) ? $conn->real_escape_string($_POST['condiciones']) : '';
            $id_proveedor_auto = isset($_POST['id_proveedor_auto']) ? intval($_POST['id_proveedor_auto']) : 0;
            $conn->query("INSERT INTO alquiler_autos (id_proveedor, id_destino, id_alquiler, proveedor, tipo_vehiculo, ubicacion_retiro, id_destino, ubicacion_entrega, precio_por_dia, condiciones, id_proveedor) VALUES ($id_nuevo, $id_destino, '$id_alquiler', '$proveedor_auto', '$tipo_vehiculo', '$ubicacion_retiro', $id_destino_auto, '$ubicacion_entrega', $precio_por_dia_auto, '$condiciones', $id_proveedor_auto)");
            break;
        case 'servicio':
            $id_servicio = isset($_POST['id_servicio']) ? $conn->real_escape_string($_POST['id_servicio']) : '';
            $nombre_servicio = isset($_POST['nombre_servicio']) ? $conn->real_escape_string($_POST['nombre_servicio']) : '';
            $id_destino_servicio = isset($_POST['id_destino_servicio']) ? intval($_POST['id_destino_servicio']) : 0;
            $ciudad_servicio = isset($_POST['ciudad_servicio']) ? $conn->real_escape_string($_POST['ciudad_servicio']) : '';
            $descripcion_servicio = isset($_POST['descripcion_servicio']) ? $conn->real_escape_string($_POST['descripcion_servicio']) : '';
            $tipo_servicio = isset($_POST['tipo_servicio']) ? $conn->real_escape_string($_POST['tipo_servicio']) : '';
            $precio_servicio = isset($_POST['precio_servicio']) ? floatval($_POST['precio_servicio']) : 0;
            $id_proveedor_servicio = isset($_POST['id_proveedor_servicio']) ? intval($_POST['id_proveedor_servicio']) : 0;
            $conn->query("INSERT INTO servicios_adicionales (id_proveedor, id_destino, id_servicio, nombre, id_destino, ciudad, descripcion, tipo, precio, id_proveedor) VALUES ($id_nuevo, $id_destino, '$id_servicio', '$nombre_servicio', $id_destino_servicio, '$ciudad_servicio', '$descripcion_servicio', '$tipo_servicio', $precio_servicio, $id_proveedor_servicio)");
            break;
    }

    // Redirección condicional
    if (!empty($_GET['id_paquete'])) {
        $id_paquete = intval($_GET['id_paquete']);
        header("Location: nuevo_paquete.php?id_paquete=$id_paquete&asociar=1&tipo=$tipo&id_nuevo=$id_nuevo");
    } else {
        header("Location: proveedores.php?ok=1");
    }
    exit;
}

$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';
$id_destino = isset($_GET['id_destino']) ? intval($_GET['id_destino']) : '';
$id_paquete = isset($_GET['id_paquete']) ? intval($_GET['id_paquete']) : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Proveedor</title>
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
                <li class="breadcrumb-item active" aria-current="page">Nuevo Proveedor</li>
            </ol>
        </nav>
        <div class="card-proveedor mt-4">
            <div class="icon-circle mb-3">
                <i class="bi bi-person-badge"></i>
            </div>
            <h2 class="text-center mb-4" style="color:#750D37;font-weight:700;letter-spacing:1px;">Alta de Proveedor</h2>
            <form method="post" autocomplete="off">
                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" required maxlength="100">
                </div>
                <div class="mb-3">
                    <label class="form-label">Tipo</label>
                    <select name="tipo" id="tipo-select" class="form-select" required>
                        <option value="">Seleccione tipo</option>
                        <option value="alojamiento" <?= $tipo=='alojamiento'?'selected':'' ?>>Alojamiento</option>
                        <option value="vuelo" <?= $tipo=='vuelo'?'selected':'' ?>>Vuelo</option>
                        <option value="auto" <?= $tipo=='auto'?'selected':'' ?>>Auto</option>
                        <option value="servicio" <?= $tipo=='servicio'?'selected':'' ?>>Servicio</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Contacto (Nombre del encargado, empresa, etc)</label>
                    <input type="text" name="contacto" class="form-control" maxlength="100">
                </div>

                <!-- Additional fields for alojamiento -->
                <div id="fields-alojamiento" style="display:none;">
                    <div class="mb-3">
                        <label class="form-label">Precio por día</label>
                        <input type="number" step="0.01" name="precio_por_dia_alojamiento" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Categoría</label>
                        <input type="text" name="categoria_alojamiento" class="form-control" maxlength="100">
                    </div>
                </div>

                <!-- Additional fields for vuelo -->
                <div id="fields-vuelo" style="display:none;">
                    <div class="mb-3">
                        <label class="form-label">ID Vuelo</label>
                        <input type="text" name="id_vuelo" class="form-control" maxlength="50">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Código Vuelo</label>
                        <input type="text" name="codigo_vuelo" class="form-control" maxlength="50">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Aerolínea</label>
                        <input type="text" name="aerolinea_vuelo" class="form-control" maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Origen</label>
                        <input type="text" name="origen_vuelo" class="form-control" maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Destino</label>
                        <input type="text" name="destino_vuelo" class="form-control" maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ID Destino</label>
                        <input type="number" name="id_destino_vuelo" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha Salida</label>
                        <input type="datetime-local" name="fecha_salida" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha Llegada</label>
                        <input type="datetime-local" name="fecha_llegada" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Precio Base</label>
                        <input type="number" step="0.01" name="precio_base" class="form-control">
                    </div>
                </div>

                <!-- Additional fields for auto -->
                <div id="fields-auto" style="display:none;">
                    <div class="mb-3">
                        <label class="form-label">ID Alquiler</label>
                        <input type="text" name="id_alquiler" class="form-control" maxlength="50">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Proveedor</label>
                        <input type="text" name="proveedor_auto" class="form-control" maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipo Vehículo</label>
                        <input type="text" name="tipo_vehiculo" class="form-control" maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ubicación Retiro</label>
                        <input type="text" name="ubicacion_retiro" class="form-control" maxlength="150">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ID Destino</label>
                        <input type="number" name="id_destino_auto" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ubicación Entrega</label>
                        <input type="text" name="ubicacion_entrega" class="form-control" maxlength="150">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Precio por día</label>
                        <input type="number" step="0.01" name="precio_por_dia_auto" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Condiciones</label>
                        <textarea name="condiciones" class="form-control" rows="3" maxlength="255"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ID Proveedor</label>
                        <input type="number" name="id_proveedor_auto" class="form-control">
                    </div>
                </div>

                <!-- Additional fields for servicio -->
                <div id="fields-servicio" style="display:none;">
                    <div class="mb-3">
                        <label class="form-label">ID Servicio</label>
                        <input type="text" name="id_servicio" class="form-control" maxlength="50">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre_servicio" class="form-control" maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ID Destino</label>
                        <input type="number" name="id_destino_servicio" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ciudad</label>
                        <input type="text" name="ciudad_servicio" class="form-control" maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion_servicio" class="form-control" rows="3" maxlength="255"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipo</label>
                        <input type="text" name="tipo_servicio" class="form-control" maxlength="50">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Precio</label>
                        <input type="number" step="0.01" name="precio_servicio" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ID Proveedor</label>
                        <input type="number" name="id_proveedor_servicio" class="form-control">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" maxlength="30">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" maxlength="100">
                </div>
                <div class="mb-3">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="direccion" class="form-control" maxlength="150">
                </div>
                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="3" maxlength="255"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ciudad ()</label>
                    <input type="text" name="direccion" class="form-control" maxlength="150">
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
                <div class="d-flex justify-content-between">
                    <a href="<?= !empty($id_paquete) ? "nuevo_paquete.php?id_paquete=$id_paquete&asociar=1" : "proveedores.php" ?>" class="btn btn-secondary px-4">Cancelar</a>
                    <button type="submit" class="btn btn-success px-4"><i class="bi bi-check-circle"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $('#input-destino').select2({
        placeholder: 'Buscar o agregar destino',
        ajax: {
            url: 'buscar_destinos.php',
            dataType: 'json',
            delay: 250,
            data: params => ({ term: params.term }),
            processResults: data => ({
                results: data.map(d => ({ id: d.id_destino, text: d.destino }))
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
        escapeMarkup: function (markup) { return markup; }
    });

    // Agregar destino desde el botón en el dropdown
    $(document).on('click', '#agregar-destino-btn', function(e) {
        let term = $('.select2-search__field').val();
        $.post('agregar_destino.php', { destino: term }, function(data) {
            let newOption = new Option(data.destino, data.id_destino, true, true);
            $('#input-destino').append(newOption).trigger('change');
            $('.select2-results__options').empty();
        }, 'json');
    });

    // Precargar destino si viene por GET
    <?php if ($id_destino): ?>
    $.get('buscar_destinos.php', {term: ''}, function(data) {
        let found = data.find(d => d.id_destino == <?= $id_destino ?>);
        if (found) {
            let newOption = new Option(found.destino, found.id_destino, true, true);
            $('#input-destino').append(newOption).trigger('change');
        }
    }, 'json');
    <?php endif; ?>
    </script>
    <script>
    $(document).ready(function() {
        $('#tipo-select').change(function() {
            var tipo = $(this).val();
            // Hide all dynamic fields containers
            $('#fields-alojamiento, #fields-vuelo, #fields-auto, #fields-servicio').hide();
            if (tipo) {
                $('#fields-' + tipo).show();
            }
        });
        // Trigger change on page load to show fields if tipo is preselected
        $('#tipo-select').trigger('change');
    });
    </script>
</body>
</html>
<?php 

if (isset($_SESSION['id_usuario'])) {
    registrar_bitacora(
        $pdo,
        $_SESSION['id_usuario'],
        'Crear proveedor',
        "Proveedor creado: $nombre (Tipo: $tipo, ID Destino: $id_destino)" . 
        (!empty($id_paquete) ? " asociado al paquete ID: $id_paquete" : "")
    );
}

$conn->close(); 
?>
