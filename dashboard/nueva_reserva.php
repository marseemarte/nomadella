<?php
include 'conexion.php';
include 'verificar_admin.php';

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = intval($_POST['id_usuario']);
    $id_paquete = intval($_POST['id_paquete']);
    $fecha_orden = date('Y-m-d H:i:s');

    // Crear la orden
    $conn->query("INSERT INTO ordenes (id_usuario, fecha_orden, estado, total) VALUES ($id_usuario, '$fecha_orden', 'Pendiente', 0)");
    $id_orden = $conn->insert_id;

    // Paquete turístico (obligatorio)
    $conn->query("INSERT INTO orden_items (id_orden, tipo_producto, id_producto, cantidad) VALUES ($id_orden, 'paquete_turistico', $id_paquete, 1)");

    // Servicios adicionales (opcionales)
    if (!empty($_POST['servicios_adicionales'])) {
        foreach ($_POST['servicios_adicionales'] as $id_servicio) {
            $id_servicio = intval($id_servicio);
            $conn->query("INSERT INTO orden_items (id_orden, tipo_producto, id_producto, cantidad) VALUES ($id_orden, 'servicio_adicional', $id_servicio, 1)");
        }
    }

    // Alquiler de auto (opcional, solo uno)
    if (!empty($_POST['alquiler_auto'])) {
        $id_auto = intval($_POST['alquiler_auto']);
        $conn->query("INSERT INTO orden_items (id_orden, tipo_producto, id_producto, cantidad) VALUES ($id_orden, 'alquiler_auto', $id_auto, 1)");
    }

    // Bitácora
    if (isset($_SESSION['id_usuario'])) {
        registrar_bitacora(
            $pdo,
            $_SESSION['id_usuario'],
            'Crear reserva',
            "Nueva reserva creada por el usuario {$_SESSION['id_usuario']} con paquete ID {$id_paquete} y cliente ID {$id_usuario}"
        );
    }

    // Redirigir a reservas
    header("Location: reservas.php");
    exit;
}

// Traer paquetes turísticos
$paquetes = $conn->query("SELECT id_paquete, nombre, destino FROM paquetes_turisticos");

// Traer servicios adicionales y autos para el paquete seleccionado (si hay)
$servicios_adicionales = [];
$alquiler_autos = [];
$id_paquete_sel = null;
if (isset($_POST['id_paquete'])) {
    $id_paquete_sel = intval($_POST['id_paquete']);
    // Servicios adicionales
    $res_serv = $conn->query("SELECT s.id_servicio, s.nombre FROM servicios_adicionales s JOIN paquete_servicios ps ON s.id_servicio = ps.id_servicio WHERE ps.id_paquete = $id_paquete_sel");
    while ($row = $res_serv->fetch_assoc()) {
        $servicios_adicionales[] = $row;
    }
    // Autos
    $res_autos = $conn->query("SELECT aa.id_alquiler, aa.proveedor FROM alquiler_autos aa JOIN paquete_autos pa ON aa.id_alquiler = pa.id_alquiler WHERE pa.id_paquete = $id_paquete_sel");
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #FFF6F8; color: #1A001C; }
        .main-content { margin-left: 260px; padding: 40px 30px 30px 30px; min-height: 100vh; }
        .card { background: #fff; border: none; border-radius: 10px; box-shadow: 0 0 10px rgba(117, 13, 55, 0.1); padding: 20px; }
        .btn-primary { background-color: #3AB789; border-color: #3AB789; font-weight: bold; }
        .cliente-sugerencias { position: absolute; z-index: 10; background: #fff; border: 1px solid #ccc; width: 100%; max-height: 200px; overflow-y: auto; }
        .cliente-sugerencia { padding: 8px; cursor: pointer; }
        .cliente-sugerencia:hover { background: #f0f0f0; }
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
                    <input type="hidden" name="id_usuario" id="id_usuario" required>
                    <div id="cliente-sugerencias" class="cliente-sugerencias" style="display:none"></div>
                </div>
                <div class="mb-3">
                    <label for="id_paquete" class="form-label">Paquete turístico</label>
                    <select name="id_paquete" id="id_paquete" class="form-select" required onchange="this.form.submit()">
                        <option value="">Seleccione un paquete</option>
                        <?php while($p = $paquetes->fetch_assoc()): ?>
                            <option value="<?= $p['id_paquete'] ?>" <?= ($id_paquete_sel == $p['id_paquete']) ? 'selected' : '' ?>><?= htmlspecialchars($p['nombre'] . ' (' . $p['destino'] . ')') ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <?php if (!empty($servicios_adicionales)): ?>
                <div class="mb-3">
                    <label class="form-label">Servicios adicionales (opcional)</label>
                    <?php foreach ($servicios_adicionales as $serv): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="servicios_adicionales[]" value="<?= $serv['id_servicio'] ?>" id="servicio_<?= $serv['id_servicio'] ?>">
                            <label class="form-check-label" for="servicio_<?= $serv['id_servicio'] ?>"><?= htmlspecialchars($serv['nombre']) ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($alquiler_autos)): ?>
                <div class="mb-3">
                    <label class="form-label">Alquiler de auto (opcional)</label>
                    <select name="alquiler_auto" class="form-select">
                        <option value="">No alquilar auto</option>
                        <?php foreach ($alquiler_autos as $auto): ?>
                            <option value="<?= $auto['id_alquiler'] ?>"><?= htmlspecialchars($auto['proveedor']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label">Fecha de orden</label>
                    <input type="text" class="form-control" value="<?= date('d/m/Y H:i') ?>" disabled>
                </div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Guardar reserva</button>
                <a href="reservas.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(function(){
        let $input = $('#cliente-buscar');
        let $hidden = $('#id_usuario');
        let $sugs = $('#cliente-sugerencias');
        let timeout = null;

        $input.on('input', function(){
            clearTimeout(timeout);
            let q = $(this).val();
            $hidden.val('');
            if(q.length < 2) { $sugs.hide(); return; }
            timeout = setTimeout(function(){
                $.getJSON('buscar_clientes.php', {term: q}, function(data){
                    $sugs.html('');
                    if(data.length) {
                        data.forEach(function(item){
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

        $sugs.on('click', '.cliente-sugerencia', function(){
            $input.val($(this).data('nombre'));
            $hidden.val($(this).data('id'));
            $sugs.hide();
        });

        $(document).on('click', function(e){
            if(!$(e.target).closest('.cliente-sugerencias, #cliente-buscar').length) $sugs.hide();
        });

        $('form').on('submit', function(){
            if(!$hidden.val()) {
                alert('Debe seleccionar un cliente de la lista.');
                $input.focus();
                return false;
            }
        });
    });
    </script>
</body>
</html>
<?php $conn->close(); ?>
