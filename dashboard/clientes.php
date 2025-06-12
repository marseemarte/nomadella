<?php
// Conexión a la base de datos (ajusta los datos según tu configuración)
$mysqli = new mysqli("localhost", "root", "", "nomadella");
if ($mysqli->connect_errno) {
    die("Error de conexión: " . $mysqli->connect_error);
}

// Parámetros de paginación y búsqueda
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$search = isset($_GET['search']) ? $mysqli->real_escape_string($_GET['search']) : '';
$offset = ($page - 1) * $limit;

// Consulta para contar total de registros
$where = $search ? "WHERE nombre LIKE '%$search%' OR email LIKE '%$search%' OR telefono LIKE '%$search%'" : "";
$totalQuery = $mysqli->query("SELECT COUNT(*) as total FROM usuarios $where");
$totalRows = $totalQuery->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// Consulta para obtener los usuarios
$sql = "SELECT * FROM usuarios $where ORDER BY fecha_registro DESC LIMIT $limit OFFSET $offset";
$result = $mysqli->query($sql);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Usuarios | Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="/nomadella/css/apartados.css">
</head>

<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Usuarios</li>
            </ol>
        </nav>
        <div class="container-fluid mt-4">
            <div class="row">
                <div class="col-12">
                    <h2 class="mb-4">Usuarios</h2>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <form class="d-flex" method="get" action="">
                            <input type="text" id="busqueda-cliente" name="search" class="form-control me-2" placeholder="Buscar cliente..." value="<?= htmlspecialchars($search) ?>">
                            <button class="btn btn-primary" type="submit">Buscar</button>
                        </form>
                        <form method="get" class="d-flex align-items-center">
                            <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                            <label class="me-2">Mostrar</label>
                            <select name="limit" class="form-select me-2" style="width: auto;" onchange="this.form.submit()">
                                <option <?= $limit == 10 ? 'selected' : '' ?>>10</option>
                                <option <?= $limit == 25 ? 'selected' : '' ?>>25</option>
                                <option <?= $limit == 50 ? 'selected' : '' ?>>50</option>
                                <option <?= $limit == 100 ? 'selected' : '' ?>>100</option>
                            </select>
                            <span>registros</span>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Fecha de Registro</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-clientes">
                                <?php if ($result && $result->num_rows > 0): ?>
                                    <?php foreach ($result as $i => $row): ?>
                                        <tr>
                                            <td><?= $offset + $i + 1 ?></td>
                                            <td><?= htmlspecialchars($row['nombre']) ?></td>
                                            <td><?= htmlspecialchars($row['email']) ?></td>
                                            <td><?= htmlspecialchars($row['telefono']) ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($row['fecha_registro'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No se encontraron usuarios.</td>
                                    </tr>
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
                                    <a class="page-link" href="?search=<?= urlencode($search) ?>&limit=<?= $limit ?>&page=1">&laquo;</a>
                                </li>
                                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?search=<?= urlencode($search) ?>&limit=<?= $limit ?>&page=<?= $page - 1 ?>">&lt;</a>
                                </li>
                                <?php
                                $start = max(1, $page - 2);
                                $end = min($totalPages, $page + 2);
                                for ($i = $start; $i <= $end; $i++): ?>
                                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                        <a class="page-link" href="?search=<?= urlencode($search) ?>&limit=<?= $limit ?>&page=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?search=<?= urlencode($search) ?>&limit=<?= $limit ?>&page=<?= $page + 1 ?>">&gt;</a>
                                </li>
                                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?search=<?= urlencode($search) ?>&limit=<?= $limit ?>&page=<?= $totalPages ?>">&raquo;</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $('#busqueda-cliente').on('input', function() {
            var search = $(this).val();
            $.get('clientes_ajax.php', {
                search: search
            }, function(data) {
                $('#tabla-clientes').html(data);
            });
        });
    </script>
</body>

</html>
<?php $mysqli->close(); ?>