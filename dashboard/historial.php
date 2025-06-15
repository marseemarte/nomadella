<?php
include 'conexion.php';
include 'verificar_admin.php';

$historial = [];
$cliente_nombre = "";

if (isset($_GET['cliente_id'])) {
    $cliente_id = intval($_GET['cliente_id']);

    // Obtener nombre del cliente
    $resCliente = $conn->query("SELECT nombre FROM usuarios WHERE id_usuario = $cliente_id");
    if ($resCliente->num_rows > 0) {
        $cliente_nombre = $resCliente->fetch_assoc()['nombre'];
    }

    $query = $conn->query("
        SELECT o.id_orden, o.fecha_orden, o.estado, pt.destino, oi.cantidad
        FROM ordenes o
        JOIN orden_items oi ON oi.id_orden = o.id_orden
        JOIN paquetes_turisticos pt ON pt.id_paquete = oi.id_producto
        WHERE o.id_usuario = $cliente_id
        ORDER BY o.fecha_orden DESC
    ");
    $historial = $query->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Reservas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #FFF6F8; color: #1A001C; }
        .main-content { margin-left: 260px; padding: 40px 30px 30px 30px; min-height: 100vh; }
        .breadcrumb-item a { text-decoration: none; color: #750D37; }
        .btn-primary { background-color: #3AB789; border-color: #3AB789; font-weight: bold; }
        .btn-secondary { background-color: #5CC7ED; border-color: #5CC7ED; color: #1A001C; font-weight: bold; }
        .card { background: #fff; border: none; border-radius: 10px; box-shadow: 0 0 10px rgba(117,13,55,0.1); padding: 20px; }
        .table thead { background-color: #750D37; color: #FFF6F8; }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
<?php include 'sidebar.php'; ?>

<div class="main-content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="reservas.php">Reservas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Historial de Reservas</li>
        </ol>
    </nav>

    <h2 class="mb-4">Historial de Reservas</h2>

    <div class="card mb-4">
        <form method="get" class="row g-3">
            <div class="col-md-8">
                <label for="cliente_input" class="form-label">Buscar Cliente:</label>
                <input type="text" id="cliente_input" class="form-control" placeholder="Escriba el nombre del cliente...">
                <input type="hidden" name="cliente_id" id="cliente_id">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Ver Historial</button>
            </div>
        </form>
    </div>

    <?php if ($historial): ?>
    <div class="card">
        <h5>Historial de: <span class="fw-bold"><?= htmlspecialchars($cliente_nombre) ?></span></h5>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID Reserva</th>
                    <th>Destino</th>
                    <th>Fecha</th>
                    <th>Pasajeros</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($historial as $h): ?>
                <tr>
                    <td><?= $h['id_orden'] ?></td>
                    <td><?= htmlspecialchars($h['destino']) ?></td>
                    <td><?= date('d/m/Y', strtotime($h['fecha_orden'])) ?></td>
                    <td><?= $h['cantidad'] ?></td>
                    <td><?= $h['estado'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php elseif (isset($_GET['cliente_id'])): ?>
        <div class="alert alert-warning">Este cliente no posee reservas registradas.</div>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    $('#cliente_input').on('input', function() {
        let term = $(this).val();
        if (term.length < 2) return;

        $.get('buscar_clientes.php', {term: term}, function(data) {
            let list = '<ul class="list-group position-absolute w-75 z-3">';
            data.forEach(c => {
                list += `<li class="list-group-item list-group-item-action cliente-item" data-id="${c.id}">${c.label}</li>`;
            });
            list += '</ul>';
            $('#cliente_input').nextAll('ul').remove();
            $('#cliente_input').after(list);
        }, 'json');
    });

    $(document).on('click', '.cliente-item', function() {
        $('#cliente_input').val($(this).text());
        $('#cliente_id').val($(this).data('id'));
        $('.list-group').remove();
    });
});
</script>

</body>
</html>

<?php $conn->close(); ?>
