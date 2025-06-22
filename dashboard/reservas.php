<?php
include 'conexion.php';
include 'verificar_admin.php';

$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$estado = isset($_GET['estado']) ? $conn->real_escape_string($_GET['estado']) : '';
$offset = ($page - 1) * $limit;

$where = "WHERE 1";
if (!empty($search)) {
    $where .= " AND (u.nombre LIKE '%$search%' OR u.apellido LIKE '%$search%' OR o.id_orden LIKE '%$search%')";
}
if (!empty($estado)) {
    $where .= " AND o.estado = '$estado'";
}

$totalQuery = $conn->query("SELECT COUNT(*) as total
    FROM ordenes o
    JOIN usuarios u ON u.id_usuario = o.id_usuario
    $where");
$totalRows = $totalQuery->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);
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

        .table thead {
            background-color: #750D37;
            color: #FFF6F8;
        }

        .pagination .page-link {
            border-radius: 0.5rem;
            margin: 0 0.2rem;
            color: #750D37;
            border-color: #ddd;
        }

        .pagination .page-item.active .page-link {
            background-color: #24c58c;
            border-color: #24c58c;
            color: #fff;
        }

        .pagination .page-item.disabled .page-link {
            color: #ccc;
            pointer-events: none;
        }

        /* Fix modal visibility */
        #detalleModal {
            display: block !important;
            opacity: 1 !important;
            pointer-events: auto !important;
            z-index: 1050 !important;
            position: fixed !important;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) !important;
        }

        #detalleModal.show {
            display: block !important;
            opacity: 1 !important;
        }

        .modal-backdrop {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            background-color: rgba(0, 0, 0, 0.5) !important;
            z-index: 1040 !important;
        }
    </style>
</head>

<body>
    <?php include './sidebar.php'; ?>
    <div class="main-content">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Gestión de Reservas</li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-2">Gestión de Reservas</h2>

            <div>
                <a href="nueva_reserva.php" class="btn btn-primary mb-3">
                    <i class="bi bi-plus-circle"></i> Nueva Reserva
                </a>
            </div>
        </div>
        <p class="text-muted">Aquí puedes ver y gestionar todas las reservas realizadas por los clientes.</p>

        <form class="row g-3 mb-4">
            <div class="col-md-4">
                <input type="text" id="busqueda-reserva" class="form-control" placeholder="Buscar por cliente o ID...">
            </div>
            <div class="col-md-3">
                <select id="filtro-estado" class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="confirmada">Confirmada</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="cancelada">Cancelada</option>
                </select>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-reservas">
                    <!-- AJAX -->
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                Mostrando <strong id="start-record">0</strong> a <strong id="end-record">0</strong> de <strong id="total-records"><?php echo $totalRows; ?></strong> registros
            </div>
            <nav>
                <ul class="pagination mb-0" id="pagination"></ul>
            </nav>
        </div>
    </div>

    
    <!-- Modal de confirmación -->
    <div class="modal fade" id="modalCancelarReserva" tabindex="-1" aria-labelledby="modalConfirmarEliminarLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:2.5rem;"></i>
                    </div>
                    <h5 class="mb-3" id="modalConfirmarEliminarLabel">¿Estás seguro de que deseas cancelar esta reserva?</h5>
                    <p class="mb-4">Se notificará al cliente del cambio de estado.</p>
                    <form method="post" action="cancelar_reserva.php">
                        <input type="hidden" name="id_orden" id="cancelar-id">
                        <button type="submit" class="btn btn-danger px-4 me-2">Sí, cancelar</button>
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">No</button>
                    </form>
            </div>
        </div>
    </div>

    <!-- Modal Detalle Reserva -->
    <div class="modal" id="detalleModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalle de la Reserva</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detalleContenido">
                    Cargando...
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let currentPage = 1;
        let limit = <?= $limit ?>;
        let totalPages = <?= $totalPages ?>;

        function confirmarCancelacion(id) {
            document.getElementById('cancelar-id').value = id;
            const modal = new bootstrap.Modal(document.getElementById('modalCancelarReserva'));
            modal.show();
        }

        function cargarReservas(page = 1) {
            currentPage = page;
            const search = $('#busqueda-reserva').val();
            const estado = $('#filtro-estado').val();

            $.get('reservas_ajax.php', {
                page,
                limit,
                search,
                estado
            }, function(data) {
                $('#tabla-reservas').html(data.html);
                $('#start-record').text(data.start);
                $('#end-record').text(data.end);
                $('#total-records').text(data.total);
                totalPages = data.pages;
                renderPagination();
            }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
                console.error('Error loading reservations:', textStatus, errorThrown);
            });
        }

        function renderPagination() {
            let html = '';

            if (currentPage > 1) {
                html += `<li class="page-item"><a class="page-link" href="#" onclick="cargarReservas(1); return false;">&laquo;</a></li>`;
                html += `<li class="page-item"><a class="page-link" href="#" onclick="cargarReservas(${currentPage - 1}); return false;">&lsaquo;</a></li>`;
            } else {
                html += `<li class="page-item disabled"><span class="page-link">&laquo;</span></li>`;
                html += `<li class="page-item disabled"><span class="page-link">&lsaquo;</span></li>`;
            }

            for (let i = 1; i <= totalPages; i++) {
                html += `<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link" href="#" onclick="cargarReservas(${i}); return false;">${i}</a></li>`;
            }

            if (currentPage < totalPages) {
                html += `<li class="page-item"><a class="page-link" href="#" onclick="cargarReservas(${currentPage + 1}); return false;">&rsaquo;</a></li>`;
                html += `<li class="page-item"><a class="page-link" href="#" onclick="cargarReservas(${totalPages}); return false;">&raquo;</a></li>`;
            } else {
                html += `<li class="page-item disabled"><span class="page-link">&rsaquo;</span></li>`;
                html += `<li class="page-item disabled"><span class="page-link">&raquo;</span></li>`;
            }

            $('#pagination').html(html);
        }

        $(document).ready(function() {
            cargarReservas();
            $('#busqueda-reserva, #filtro-estado').on('input change', function() {
                cargarReservas(1);
            });
        });
        
        var detalleModal = new bootstrap.Modal(document.getElementById('detalleModal'));

        $(document).on('click', '.btn-ver-detalle', function() {
            var id = $(this).attr('data-id');
            console.log('Ver detalles clicked for id:', id);
            $('#detalleContenido').html('Cargando...');
            $.get('detalle_reserva.php', {id: id}, function(data) {
                console.log('AJAX success, data received:', data);
                $('#detalleContenido').html(data);
                detalleModal.show();
                $('#detalleModal').addClass('show').css('display', 'block');
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown);
                $('#detalleContenido').html('<div class="alert alert-danger">Error al cargar el detalle de la reserva.</div>');
            });
        });
    </script>

</body>

</html>