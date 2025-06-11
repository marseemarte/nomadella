<?php
include 'conexion.php'; // Asegúrate de incluir tu archivo de conexión a la base de datos

// --- NUEVA RESERVA ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nueva_reserva'])) {
    $cliente = intval($_POST['cliente']);
    $paquete = intval($_POST['paquete']);
    $pasajeros = intval($_POST['pasajeros']);
    $fecha = $conn->real_escape_string($_POST['fecha']);
    // Calcula el precio total del paquete
    $paqueteData = $conn->query("SELECT precio_base FROM paquetes_turisticos WHERE id_paquete = $paquete")->fetch_assoc();
    $total = $paqueteData ? $paqueteData['precio_base'] * $pasajeros : 0;
    $conn->query("INSERT INTO ordenes (id_usuario, fecha_orden, total) VALUES ($cliente, '$fecha', $total)");
    $id_orden = $conn->insert_id;
    $conn->query("INSERT INTO orden_items (id_orden, id_producto, cantidad, subtotal) VALUES ($id_orden, $paquete, $pasajeros, $total)");
    $msg = "Reserva creada correctamente.";
}

// --- EDITAR RESERVA ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_reserva'])) {
    $id_orden = intval($_POST['id_orden']);
    $fecha = $conn->real_escape_string($_POST['fecha']);
    $estado = $conn->real_escape_string($_POST['estado']);
    $conn->query("UPDATE ordenes SET fecha_orden='$fecha', estado='$estado' WHERE id_orden=$id_orden");
    $msg = "Reserva actualizada.";
}

// --- CANCELAR RESERVA ---
if (isset($_GET['cancelar_id'])) {
    $id_orden = intval($_GET['cancelar_id']);
    $conn->query("UPDATE ordenes SET estado='Cancelada' WHERE id_orden=$id_orden");
    $msg = "Reserva cancelada.";
}

// --- DATOS PARA SELECTS ---
$clientes = $conn->query("SELECT id_usuario, nombre FROM usuarios");
$paquetes = $conn->query("SELECT id_paquete, destino FROM paquetes_turisticos WHERE activo=1");

// Parámetros de paginación y búsqueda para reservas
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$offset = ($page - 1) * $limit;

// Filtro de búsqueda
$where = $search ? "WHERE u.nombre LIKE '%$search%' OR pt.destino LIKE '%$search%'" : "";

// Ordenamiento por ID
$order = isset($_GET['order']) && strtolower($_GET['order']) === 'asc' ? 'ASC' : 'DESC';
$orderIcon = $order === 'ASC' ? 'bi-caret-up-fill' : 'bi-caret-down-fill';

// Total de reservas
$totalQuery = $conn->query("SELECT COUNT(*) as total
    FROM ordenes o
    JOIN usuarios u ON u.id_usuario = o.id_usuario
    JOIN orden_items oi ON oi.id_orden = o.id_orden
    JOIN paquetes_turisticos pt ON pt.id_paquete = oi.id_producto
    
    $where
");
$totalRows = $totalQuery->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// Consulta de reservas paginada con orden dinámico
$reservas = $conn->query("
    SELECT o.id_orden, o.fecha_orden, o.estado, u.nombre AS cliente, pt.destino, oi.cantidad
    FROM ordenes o
    JOIN usuarios u ON u.id_usuario = o.id_usuario
    JOIN orden_items oi ON oi.id_orden = o.id_orden
    JOIN paquetes_turisticos pt ON pt.id_paquete = oi.id_producto
    $where
    ORDER BY o.id_orden $order
    LIMIT $limit OFFSET $offset
");

$historial = []; // <-- Agrega esta línea antes del HTML

if (isset($_GET['historial_cliente'])) {
    $cid = intval($_GET['historial_cliente']);
    $historial = $conn->query("
        SELECT o.fecha_orden, o.estado, pt.destino, oi.cantidad
        FROM ordenes o
        JOIN orden_items oi ON oi.id_orden = o.id_orden
        JOIN paquetes_turisticos pt ON pt.id_paquete = oi.id_producto
        WHERE o.id_usuario = $cid
        ORDER BY o.fecha_orden DESC
    ");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reservas | Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #FFF6F8; color: #1A001C; margin: 0; padding: 0; }
        .main-content { margin-left: 260px; padding: 40px 30px 30px 30px; min-height: 100vh; background: #FFF6F8; }
        .tab-content { margin-top: 20px; }
        body {
            background: #FFF6F8;
            color: #1A001C;
        }
        .table-responsive { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #0001; }
        .dataTables_wrapper .dataTables_paginate .paginate_button { padding: 0.2em 0.8em; }
        .content {
            margin-left: 260px;
            padding: 30px 20px 20px 20px;
        }
        .table thead.table-dark th {
            background: #750D37 !important;
            color: #FFF6F8 !important;
        }
        .btn-primary {
            background: #6CE0B6 !important;
            border: none;
            color: #1A001C !important;
            font-weight: bold;
        }
        .form-select, .form-control {
            border-radius: 6px;
        }
        .pagination .page-link {
            color: #750D37;
        }
        .pagination .page-item.active .page-link {
            background: #6CE0B6;
            color: #1A001C;
            border: none;
        }
        .pagination .page-link:focus {
            box-shadow: none;
        }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="main-content">
    <h1 class="mb-4">Gestión de Reservas</h1>
    <?php if (isset($msg)): ?>
        <div class="alert alert-success"><?= $msg ?></div>
    <?php endif; ?>
    <ul class="nav nav-tabs" id="reservasTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="gestion-tab" data-bs-toggle="tab" data-bs-target="#gestion" type="button" role="tab">Gestión de Reservas</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="nueva-tab" data-bs-toggle="tab" data-bs-target="#nueva" type="button" role="tab">Nueva Reserva</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="editar-tab" data-bs-toggle="tab" data-bs-target="#editar" type="button" role="tab">Editar Reserva</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="cancelar-tab" data-bs-toggle="tab" data-bs-target="#cancelar" type="button" role="tab">Cancelar Reserva</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="historial-tab" data-bs-toggle="tab" data-bs-target="#historial" type="button" role="tab">Historial</button>
        </li>
    </ul>
    <div class="tab-content">
        <!-- Gestión de Reservas -->
        <div class="tab-pane fade show active" id="gestion" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <form class="d-flex" method="get" action="">
                    <input type="text" id="busqueda-reserva" name="search" class="form-control me-2" placeholder="Buscar reserva..." value="<?= htmlspecialchars($search) ?>">
                    <button class="btn btn-primary" type="submit">Buscar</button>
                </form>
                <form method="get" class="d-flex align-items-center">
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    <label class="me-2">Mostrar</label>
                    <select name="limit" class="form-select me-2" style="width: auto;" onchange="this.form.submit()">
                        <option <?= $limit==10?'selected':'' ?>>10</option>
                        <option <?= $limit==25?'selected':'' ?>>25</option>
                        <option <?= $limit==50?'selected':'' ?>>50</option>
                        <option <?= $limit==100?'selected':'' ?>>100</option>
                    </select>
                    <span>registros</span>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>
                                <a href="?<?= http_build_query(array_merge($_GET, ['order' => ($order === 'ASC' ? 'desc' : 'asc'), 'page' => 1])) ?>#gestion" style="color:inherit;text-decoration:none;">
                                    #
                                    <i class="bi <?= $orderIcon ?>"></i>
                                </a>
                            </th>
                            <th>Cliente</th>
                            <th>Paquete</th>
                            <th>Pasajeros</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-reservas">
                        <?php if ($reservas && $reservas->num_rows > 0): ?>
                            <?php foreach ($reservas as $i => $r): ?>
                                <tr>
                                    <td><?= $offset + $i + 1 ?></td>
                                    <td><?= htmlspecialchars($r['cliente']) ?></td>
                                    <td><?= htmlspecialchars($r['destino']) ?></td>
                                    <td><?= $r['cantidad'] ?></td>
                                    <td><?= date('d/m/Y', strtotime($r['fecha_orden'])) ?></td>
                                    <td><?= $r['estado'] ?></td>
                                    <td>
                                        <a href="?tab=editar&editar_id=<?= $r['id_orden'] ?>#editar" class="btn btn-sm btn-info"><i class="bi bi-pencil"></i></a>
                                        <a href="?tab=cancelar&cancelar_id=<?= $r['id_orden'] ?>#cancelar" class="btn btn-sm btn-danger" onclick="return confirm('¿Cancelar reserva?')"><i class="bi bi-x-circle"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center">No se encontraron reservas.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Mostrando 
                    <strong><?= $totalRows ? $offset + 1 : 0 ?></strong> 
                    a 
                    <strong><?= min($offset + $limit, $totalRows) ?></strong> 
                    de 
                    <strong><?= $totalRows ?></strong> 
                    registros
                </div>
                <nav>
                    <ul class="pagination mb-0">
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?search=<?= urlencode($search) ?>&limit=<?= $limit ?>&page=1#gestion">&laquo;</a>
                        </li>
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?search=<?= urlencode($search) ?>&limit=<?= $limit ?>&page=<?= $page-1 ?>#gestion">&lt;</a>
                        </li>
                        <?php
                        $start = max(1, $page - 2);
                        $end = min($totalPages, $page + 2);
                        for ($i = $start; $i <= $end; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="?search=<?= urlencode($search) ?>&limit=<?= $limit ?>&page=<?= $i ?>#gestion"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?search=<?= urlencode($search) ?>&limit=<?= $limit ?>&page=<?= $page+1 ?>#gestion">&gt;</a>
                        </li>
                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?search=<?= urlencode($search) ?>&limit=<?= $limit ?>&page=<?= $totalPages ?>#gestion">&raquo;</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <!-- Nueva Reserva -->
        <div class="tab-pane fade" id="nueva" role="tabpanel">
            <h3>Nueva Reserva</h3>
            <form method="post">
                <div class="mb-3">
                    <label for="cliente" class="form-label">Cliente</label>
                    <select class="form-control" id="cliente" name="cliente" required>
                        <option value="">Seleccione...</option>
                        <?php foreach ($clientes as $c): ?>
                            <option value="<?= $c['id_usuario'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="paquete" class="form-label">Paquete Turístico</label>
                    <select class="form-control" id="paquete" name="paquete" required>
                        <option value="">Seleccione...</option>
                        <?php foreach ($paquetes as $p): ?>
                            <option value="<?= $p['id_paquete'] ?>"><?= htmlspecialchars($p['destino']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="pasajeros" class="form-label">Número de Pasajeros</label>
                    <input type="number" class="form-control" id="pasajeros" name="pasajeros" min="1" required>
                </div>
                <div class="mb-3">
                    <label for="fecha" class="form-label">Fecha de Viaje</label>
                    <input type="date" class="form-control" id="fecha" name="fecha" required>
                </div>
                <button type="submit" name="nueva_reserva" class="btn btn-primary">Guardar Reserva</button>
            </form>
        </div>
        <!-- Editar Reserva -->
        <div class="tab-pane fade" id="editar" role="tabpanel">
            <h3>Editar Reserva</h3>
            <form method="get" class="mb-3">
                <label>Buscar por ID de Reserva:</label>
                <input type="number" name="editar_id" class="form-control" style="width:200px;display:inline-block;">
                <button class="btn btn-info btn-sm" type="submit">Buscar</button>
            </form>
            <?php
            if (isset($_GET['editar_id'])):
                $eid = intval($_GET['editar_id']);
                $res = $conn->query("SELECT * FROM ordenes WHERE id_orden=$eid")->fetch_assoc();
                if ($res):
            ?>
            <form method="post">
                <input type="hidden" name="id_orden" value="<?= $res['id_orden'] ?>">
                <div class="mb-3">
                    <label>Fecha de Viaje</label>
                    <input type="date" class="form-control" name="fecha" value="<?= date('Y-m-d', strtotime($res['fecha_orden'])) ?>">
                </div>
                <div class="mb-3">
                    <label>Estado</label>
                    <select class="form-control" name="estado">
                        <option <?= $res['estado']=='Pendiente'?'selected':'' ?>>Pendiente</option>
                        <option <?= $res['estado']=='Confirmada'?'selected':'' ?>>Confirmada</option>
                        <option <?= $res['estado']=='Cancelada'?'selected':'' ?>>Cancelada</option>
                    </select>
                </div>
                <button type="submit" name="editar_reserva" class="btn btn-success">Guardar Cambios</button>
            </form>
            <?php else: ?>
                <div class="alert alert-warning">Reserva no encontrada.</div>
            <?php endif; endif; ?>
        </div>
        <!-- Cancelar Reserva -->
        <div class="tab-pane fade" id="cancelar" role="tabpanel">
            <h3>Cancelar Reserva</h3>
            <form method="get" class="mb-3">
                <label>Buscar por ID de Reserva:</label>
                <input type="number" name="cancelar_id" class="form-control" style="width:200px;display:inline-block;">
                <button class="btn btn-danger btn-sm" type="submit">Cancelar</button>
            </form>
        </div>
        <!-- Historial de Reservas -->
        <div class="tab-pane fade" id="historial" role="tabpanel">
            <h3>Historial de Reservas</h3>
            <form method="get" class="mb-3">
                <label>Buscar por Cliente:</label>
                <select name="historial_cliente" class="form-control" style="width:250px;display:inline-block;">
                    <option value="">Seleccione...</option>
                    <?php
                    $clientes2 = $conn->query("SELECT id_usuario, nombre FROM usuarios");
                    foreach ($clientes2 as $c):
                    ?>
                        <option value="<?= $c['id_usuario'] ?>" <?= (isset($_GET['historial_cliente']) && $_GET['historial_cliente']==$c['id_usuario'])?'selected':'' ?>>
                            <?= htmlspecialchars($c['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-secondary btn-sm" type="submit" name="tab" value="historial">Ver historial</button>
            </form>
            <?php if (isset($_GET['historial_cliente']) && $historial): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Destino</th>
                                <th>Pasajeros</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($historial as $h): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($h['fecha_orden'])) ?></td>
                                <td><?= htmlspecialchars($h['destino']) ?></td>
                                <td><?= $h['cantidad'] ?></td>
                                <td><?= $h['estado'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Obtiene el valor de tab de la URL
    const params = new URLSearchParams(window.location.search);
    const tab = params.get('tab') || 'gestion';
    const tabTrigger = document.querySelector(`#${tab}-tab`);
    if(tabTrigger){
        new bootstrap.Tab(tabTrigger).show();
    }
});
</script>
</body>
</html>
<?php $conn->close(); ?>