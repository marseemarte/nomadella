<?php
include 'conexion.php';
include 'verificar_admin.php';

$error_usuario = '';
$error_paquete = '';
$servicios_adicionales = [];
$alquiler_autos = [];
$id_paquete_sel = $_SESSION['reserva_id_paquete'] ?? null;
$id_usuario = $_SESSION['reserva_id_usuario'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    // Si solo está previsualizando los servicios y autos:
    if ($accion === 'previsualizar') {
        // NO crear la orden ni redirigir
        $_SESSION['reserva_id_paquete'] = intval($_POST['id_paquete']);
    }

    // Si el usuario está confirmando la reserva
    elseif ($accion === 'confirmar') {
        if (isset($_POST['id_usuario'])) {
            $id_usuario = intval($_POST['id_usuario']);
            $_SESSION['reserva_id_usuario'] = $id_usuario;
        }

        if (isset($_POST['id_paquete'])) {
            $id_paquete = intval($_POST['id_paquete']);
            $_SESSION['reserva_id_paquete'] = $id_paquete;
            $id_paquete_sel = $id_paquete;
        }

        // Validaciones
        $res_user = $conn->query("SELECT id_usuario FROM usuarios WHERE id_usuario = $id_usuario");
        if ($res_user->num_rows === 0) {
            $error_usuario = "El usuario seleccionado no existe. Por favor seleccione un usuario válido.";
        }

        if (!isset($id_paquete) || $id_paquete <= 0) {
            $error_paquete = "No se ha seleccionado un paquete válido. Por favor seleccione uno.";
        }

        if ($error_usuario === '' && $error_paquete === '') {
            $fecha_orden = date('Y-m-d H:i:s');

            // Crear orden base
            $conn->query("INSERT INTO ordenes (id_usuario, fecha_orden, estado, total) VALUES ($id_usuario, '$fecha_orden', 'Pendiente', 0)");
            $id_orden = $conn->insert_id;

            // Agregar ítem paquete
            $conn->query("INSERT INTO orden_items (id_orden, tipo_producto, id_producto, cantidad) VALUES ($id_orden, 'paquete_turistico', $id_paquete, 1)");

            $total = 0;
            $res_paquete = $conn->query("SELECT precio_base, fecha_inicio, fecha_fin FROM paquetes_turisticos WHERE id_paquete = $id_paquete");
            $paq = $res_paquete->fetch_assoc();
            $precio_paquete = floatval($paq['precio_base']);
            $fecha_inicio = new DateTime($paq['fecha_inicio']);
            $fecha_fin = new DateTime($paq['fecha_fin']);
            $dias = $fecha_inicio->diff($fecha_fin)->days + 1;

            $total += $precio_paquete;

            // Servicios adicionales
            if (!empty($_POST['servicios_adicionales'])) {
                foreach ($_POST['servicios_adicionales'] as $id_servicio) {
                    $id_servicio = intval($id_servicio);
                    $res_serv = $conn->query("SELECT precio FROM servicios_adicionales WHERE id_servicio = $id_servicio");
                    if ($row = $res_serv->fetch_assoc()) {
                        $total += floatval($row['precio']);
                        $conn->query("INSERT INTO orden_items (id_orden, tipo_producto, id_producto, cantidad) VALUES ($id_orden, 'servicio_adicional', $id_servicio, 1)");
                    }
                }
            }

            // Alquiler auto
            if (!empty($_POST['alquiler_auto'])) {
                $id_auto = intval($_POST['alquiler_auto']);
                $res_auto = $conn->query("SELECT precio_por_dia FROM alquiler_autos WHERE id_alquiler = $id_auto");
                if ($row = $res_auto->fetch_assoc()) {
                    $precio_auto = floatval($row['precio_por_dia']) * $dias;
                    $total += $precio_auto;
                    $conn->query("INSERT INTO orden_items (id_orden, tipo_producto, id_producto, cantidad) VALUES ($id_orden, 'alquiler_auto', $id_auto, $dias)");
                }
            }

            // Actualizar total
            $conn->query("UPDATE ordenes SET total = $total WHERE id_orden = $id_orden");

            // Bitácora
            if (isset($_SESSION['id_usuario'])) {
                registrar_bitacora(
                    $conn,
                    $_SESSION['id_usuario'],
                    'Crear reserva',
                    "Nueva reserva creada por el usuario {$_SESSION['id_usuario']} con paquete ID {$id_paquete} y cliente ID {$id_usuario}"
                );
            }

            // Limpiar sesión
            unset($_SESSION['reserva_id_usuario'], $_SESSION['reserva_id_paquete']);
            header("Location: reservas.php");
            exit;
        }
    }
}

// Siempre cargar paquetes y datos relacionados si hay uno seleccionado
$paquetes = $conn->query("SELECT id_paquete, nombre, destino, fecha_inicio, fecha_fin, precio_base FROM paquetes_turisticos");

if ($id_paquete_sel) {
    $res_serv = $conn->query("SELECT s.id_servicio, s.nombre, s.precio FROM servicios_adicionales s 
    JOIN paquete_servicios ps ON s.id_servicio = ps.id_servicio 
    WHERE ps.id_paquete = $id_paquete_sel");
    while ($row = $res_serv->fetch_assoc()) {
        $servicios_adicionales[] = $row;
    }

    $res_autos = $conn->query("SELECT aa.id_alquiler, aa.proveedor, aa.precio_por_dia FROM alquiler_autos aa 
    JOIN paquete_autos pa ON aa.id_alquiler = pa.id_alquiler 
    WHERE pa.id_paquete = $id_paquete_sel");
    while ($row = $res_autos->fetch_assoc()) {
        $alquiler_autos[] = $row;
    }
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Nueva Reserva</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #FFF6F8;
            color: #1A001C;
        }

        .main-content {
            margin-left: 260px;
            padding: 40px 30px 30px 30px;
            min-height: 100vh;
        }

        .card {
            background: #fff;
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(117, 13, 55, 0.1);
            padding: 20px;
        }

        .btn-primary {
            background-color: #3AB789;
            border-color: #3AB789;
            font-weight: bold;
        }

        .cliente-sugerencias {
            position: absolute;
            z-index: 10;
            background: #fff;
            border: 1px solid #ccc;
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
        }

        .cliente-sugerencia {
            padding: 8px;
            cursor: pointer;
        }

        .cliente-sugerencia:hover {
            background: #f0f0f0;
        }
    </style>
</head>

<body>
    <?php include './sidebar.php' ?>
    <div class="main-content">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="reservas.php">Gestión de Reservas</a></li>
                <li class="breadcrumb-item active" aria-current="page">Nueva Reserva</li>
            </ol>
        </nav>
        <h2 class="mb-4">Nueva Reserva</h2>
        <div class="card">
            <form method="post" autocomplete="off">
                <div class="mb-3 position-relative">
                    <label for="cliente-buscar" class="form-label">Buscar cliente</label>
                    <input type="text" id="cliente-buscar" class="form-control" placeholder="Nombre, email o teléfono" required>
                    <input type="hidden" name="id_usuario" id="id_usuario" required
                        value="<?= $_SESSION['reserva_id_usuario'] ?? '' ?>">
                    <?php if (!empty($error_usuario)): ?>
                        <div style="color: red; font-size: 0.9em; margin-top: 5px;">
                            <?= htmlspecialchars($error_usuario) ?>
                        </div>
                    <?php endif; ?>
                    <div id="cliente-sugerencias" class="cliente-sugerencias" style="display:none"></div>
                    <div id="error-id-usuario" style="color: red; font-size: 0.9em; margin-top: 5px; display: none;">Por favor ingrese un usuario primero</div>
                </div>
                <div class="mb-3">
                    <label for="id_paquete" class="form-label">Paquete turístico</label>
                    <select name="id_paquete" id="id_paquete" class="form-select" required>
                        <option value="">Seleccione un paquete</option>
                        <?php while ($p = $paquetes->fetch_assoc()): ?>
                            <option value="<?= $p['id_paquete'] ?>"
                                data-precio="<?= $p['precio_base'] ?>"
                                data-inicio="<?= $p['fecha_inicio'] ?>"
                                data-fin="<?= $p['fecha_fin'] ?>"
                                <?= ($_SESSION['reserva_id_paquete'] ?? null) == $p['id_paquete'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['nombre'] . ' (' . $p['destino'] . ')') ?>
                            </option>

                        <?php endwhile; ?>
                    </select>
                </div>

                <div id="paquete-detalle" class="mb-3">
                    <!-- Detalles del paquete cargados dinámicamente -->
                </div>
                <div class="mb-3">
                    <label class="form-label">Fecha de orden</label>
                    <input type="text" class="form-control" value="<?= date('d/m/Y H:i') ?>" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Total estimado:</label>
                    <h4 id="total-final">$0.00</h4>
                </div>
                <input type="hidden" name="accion" id="accion" value="previsualizar">
                <button type="submit" class="btn btn-primary" onclick="document.getElementById('accion').value='confirmar'">
                    <i class="bi bi-check-circle"></i> Guardar reserva
                </button>
                <a href="reservas.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(function() {
            let $input = $('#cliente-buscar');
            let $hidden = $('#id_usuario');
            let $sugs = $('#cliente-sugerencias');
            let timeout = null;

            $input.on('input', function() {
                clearTimeout(timeout);
                let q = $(this).val();
                $hidden.val('');
                if (q.length < 2) {
                    $sugs.hide();
                    return;
                }
                timeout = setTimeout(function() {
                    $.getJSON('buscar_clientes.php', {
                        term: q
                    }, function(data) {
                        $sugs.html('');
                        if (data.length) {
                            data.forEach(function(item) {
                                $sugs.append(
                                    `<div class="cliente-sugerencia" data-id="${item.id}" data-nombre="${item.label}">${item.label}</div>`
                                );
                            });
                            $sugs.show();
                        } else {
                            $sugs.hide();
                        }
                    });
                }, 250);
            });

            $sugs.on('click', '.cliente-sugerencia', function() {
                $input.val($(this).data('nombre'));
                $hidden.val($(this).data('id'));
                $sugs.hide();
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('.cliente-sugerencias, #cliente-buscar').length) $sugs.hide();
            });

            $('form').on('submit', function() {
                if (!$hidden.val()) {
                    //alert('Debe seleccionar un cliente de la lista.');
                    $('#error-id-usuario').show();
                    $input.focus();
                    return false;
                } else {
                    $('#error-id-usuario').hide();
                }
            });
        });

        function calcularTotal() {
            let total = 0;

            // Paquete
            let paqueteSel = $('#id_paquete option:selected');
            let precioPaquete = parseFloat(paqueteSel.data('precio')) || 0;
            let fechaInicio = new Date(paqueteSel.data('inicio'));
            let fechaFin = new Date(paqueteSel.data('fin'));

            // Calcular días
            let dias = Math.round((fechaFin - fechaInicio) / (1000 * 60 * 60 * 24)) + 1;

            total += precioPaquete;

            // Servicios adicionales
            $('input[name="servicios_adicionales[]"]:checked').each(function() {
                let precio = parseFloat($(this).data('precio')) || 0;
                total += precio;
            });

            // Alquiler auto
            let autoSel = $('select[name="alquiler_auto"] option:selected');
            if (autoSel.val()) {
                let precioPorDia = parseFloat(autoSel.data('precio')) || 0;
                total += precioPorDia * dias;
            }

            // Mostrar
            $('#total-final').text(`$${total.toFixed(2)}`);

            console.log("Precio por día:", autoSel.data('precio'));
            console.log("Días:", dias);
        }

        $('#id_paquete, input[name="servicios_adicionales[]"], select[name="alquiler_auto"]').on('change', calcularTotal);
        $(document).ready(calcularTotal);
        $('#id_paquete').on('change', function() {
            let id_paquete = $(this).val();
            if (!id_paquete) {
                $('.mb-3').has('input[name="servicios_adicionales[]"], select[name="alquiler_auto"]').remove();
                calcularTotal();
                return;
            }
            $.ajax({
                url: 'get_paquete_detalle.php',
                method: 'POST',
                data: { id_paquete: id_paquete },
                success: function(response) {
                    $('.mb-3').has('input[name="servicios_adicionales[]"], select[name="alquiler_auto"]').remove();

                    let tempDiv = $('<div>').html(response);

                    let serviciosHtml = '';
                    let serviciosHeader = tempDiv.find('h6:contains("Servicios Adicionales")');
                    if (serviciosHeader.length) {
                        serviciosHtml += '<div class="mb-3"><label class="form-label">Servicios adicionales (opcional)</label>';
                        serviciosHeader.nextAll('p').each(function() {
                            let text = $(this).text();
                            let match = text.match(/➕ (.+) \((.+)\) - \$([\d.,]+)/);
                            if (match) {
                                let nombre = match[1];
                                let tipo = match[2];
                                let precio = match[3].replace(',', '');
                                serviciosHtml += `<div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="servicios_adicionales[]" value="" data-precio="${precio}" id="servicio_${nombre.replace(/\s+/g, '_')}">
                                    <label class="form-check-label" for="servicio_${nombre.replace(/\s+/g, '_')}">${nombre}</label>
                                </div>`;
                            }
                        });
                        serviciosHtml += '</div>';
                    }

                    let autosHtml = '';
                    let autosHeader = tempDiv.find('h6:contains("Alquiler de Auto")');
                    if (autosHeader.length) {
                        autosHtml += '<div class="mb-3"><label class="form-label">Alquiler de auto (opcional)</label><select name="alquiler_auto" class="form-select"><option value="">Seleccione un auto</option>';
                        autosHeader.nextAll('p').each(function() {
                            let text = $(this).text();
                            let match = text.match(/🚗 (.+) - (.+) - \$([\d.,]+)\/día/);
                            if (match) {
                                let proveedor = match[1];
                                let tipo_vehiculo = match[2];
                                let precio = match[3].replace(',', '');
                                autosHtml += `<option value="" data-precio="${precio}">${proveedor} - ${tipo_vehiculo}</option>`;
                            }
                        });
                        autosHtml += '</select></div>';
                    }
                    $('#id_paquete').closest('.mb-3').after(serviciosHtml + autosHtml);

                    $('input[name="servicios_adicionales[]"], select[name="alquiler_auto"]').off('change').on('change', calcularTotal);

                    calcularTotal();
                },
                error: function() {
                    alert('Error al cargar los detalles del paquete.');
                }
            });
        });
    </script>
</body>

</html>
<?php $conn->close(); ?>