<?php
include 'conexion.php';
include 'verificar_admin.php';

$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'id';
$order_dir = (isset($_GET['order_dir']) && strtolower($_GET['order_dir']) === 'asc') ? 'ASC' : 'DESC';
$offset = ($page - 1) * $limit;

$validColumns = [
    'id' => 'o.id_orden',
    'cliente' => 'u.nombre',
    'fecha' => 'o.fecha_orden',
    'estado' => 'o.estado'
];
$orderColumn = isset($validColumns[$order_by]) ? $validColumns[$order_by] : 'o.id_orden';

$where = $search ? "WHERE u.nombre LIKE '%$search%' OR pt.destino LIKE '%$search%'" : "";

$totalQuery = $conn->query("SELECT COUNT(*) as total
    FROM ordenes o
    JOIN usuarios u ON u.id_usuario = o.id_usuario
    JOIN orden_items oi ON oi.id_orden = o.id_orden
    JOIN paquetes_turisticos pt ON pt.id_paquete = oi.id_producto
    $where");
$totalRows = $totalQuery->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

$reservas = $conn->query("SELECT o.id_orden, o.fecha_orden, o.estado, u.nombre, u.apellido AS cliente, pt.destino, oi.cantidad
    FROM ordenes o
    JOIN usuarios u ON u.id_usuario = o.id_usuario
    JOIN orden_items oi ON oi.id_orden = o.id_orden
    JOIN paquetes_turisticos pt ON pt.id_paquete = oi.id_producto
    $where
    ORDER BY $orderColumn $order_dir
    LIMIT $limit OFFSET $offset");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Reservas</title>
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

        .breadcrumb-item a {
            text-decoration: none;
            color: #750D37;
        }

        .btn-primary {
            background-color: #3AB789;
            border-color: #3AB789;
            font-weight: bold;
        }

        .btn-secondary {
            background-color: #5CC7ED;
            border-color: #5CC7ED;
            color: #1A001C;
            font-weight: bold;
        }

        .card {
            background: #fff;
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(117, 13, 55, 0.1);
            padding: 20px;
        }

        .accordion-button {
            font-weight: 600;
            color: #1A001C;
            background-color: #F8F9FA;
        }

        .accordion-button:focus {
            box-shadow: none;
        }

        .accordion-item {
            border: 1px solid #DDD;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .table thead {
            background-color: #750D37;
            color: #FFF6F8;
        }
    </style>
</head>

<body>

    <?php include './sidebar.php' ?>

    <div class="main-content">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Gestión de Reservas</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gestionar Reservas</h2>
            <a href="nueva_reserva.php" class="btn btn-primary mb-3">
                <i class="bi bi-plus-circle"></i> Nueva reserva
            </a>
        </div>

        <form class="d-flex mb-4" onsubmit="return false;">
            <input type="text" id="busqueda-reserva" name="search" class="form-control me-2" placeholder="Buscar...">
        </form>

        <div class="accordion" id="reservasAccordion">
            <!-- Aquí se cargan las reservas por AJAX -->
        </div>

        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++):
                    $class = ($i == $page) ? 'active' : '';
                    $params = array_merge($_GET, ['page' => $i]); ?>
                    <li class="page-item <?= $class ?>">
                        <a class="page-link" href="?<?= http_build_query($params) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function cargarReservas(q) {
            $.get('reservas_ajax.php', {
                search: q
            }, function(data) {
                $('#reservasAccordion').html(data);
            });
        }
        $(document).ready(function() {
            cargarReservas('');
            $('#busqueda-reserva').on('input', function() {
                cargarReservas($(this).val());
            });
        });
    </script>

</body>

</html>

<?php $conn->close(); ?>