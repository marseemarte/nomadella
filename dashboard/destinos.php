<?php
// filepath: c:\xampp\htdocs\nomadella\dashboard\nuevo_destino.php
include 'conexion.php';
include 'verificar_admin.php';

// Parámetros de paginación y búsqueda
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$where = "WHERE 1=1";
$offset = ($page - 1) * $limit;

// Consulta para contar total de registros
if ($search) {
    $where .= " AND (destino LIKE '%$search%')";
} else {
    $search = '';
}
$consulta = "SELECT COUNT(*) FROM destinos";
$order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC';
$orderIcon = $order === 'DESC' ? 'bi-arrow-down-short' : 'bi-arrow-up-short';
$resultCount = $conn->query($consulta);
$totalRows = $resultCount->fetch_row()[0];
$totalPages = ceil($totalRows / $limit);

// Consulta para obtener los destinos
$sql = "SELECT * FROM destinos $where ORDER BY id_destino $order LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);


$highlight = isset($_GET['highlight']) ? intval($_GET['highlight']) : 0;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Nuevo Destino</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/nomadella/css/apartados.css">
    <style>
        .main-content {
            margin-left: 260px;
            padding: 40px 30px 30px 30px;
            min-height: 100vh;
            background: #FFF6F8;
        }

        .card-destino {
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
                <li class="breadcrumb-item active" aria-current="page">Destino</li>
            </ol>
        </nav>
        <div class="container-fluid mt-4">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="">Destinos</h2>
                        <a href="nuevo_destino.php" class="btn btn-primary mb-3">
                            <i class="bi bi-plus-circle"></i> Nuevo Destino
                        </a>
                    </div>
                    <p class="text-muted">
                        Aquí puedes gestionar los destinos de la agencia. Puedes buscar, filtrar y ordenar los destinos según tus necesidades.
                    </p>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <form class="d-flex align-items-center" method="get" action="">
                            <input type="text" id="busqueda-destino" name="search" class="form-control me-2" placeholder="Buscar destino..." value="<?= htmlspecialchars($search) ?>">
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
                                    <th>
                                        <a href="?search=<?= urlencode($search) ?>&limit=<?= $limit ?>&page=<?= $page ?>&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>" class="text-decoration-none text-light">
                                            #
                                            <i class="bi <?= $orderIcon ?>"></i>
                                        </a>
                                    </th>
                                    <th>Destino</th>
                                    <th>Fecha de Registro
                                        <a href="?search=<?= urlencode($search) ?>&limit=<?= $limit ?>&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>" class="text-decoration-none">
                                            <i class="bi <?= $orderIcon ?>"></i>
                                        </a>
                                    </th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-clientes">
                                <?php if ($result && $result->num_rows > 0): ?>
                                    <?php foreach ($result as $i => $row): ?>
                                        <tr <?= ($row['id_destino'] == $highlight) ? 'class="table-warning"' : '' ?>>

                                            <td><?= $row['id_destino'] ?></td>
                                            <td><?= htmlspecialchars($row['destino']) ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($row['fecha_registro'])) ?></td>
                                            <td>
                                                <a href="editar_destino.php?id=<?= $row['id_destino'] ?>" class="btn btn-sm btn-primary me-1">
                                                    <i class="bi bi-pencil-square"></i> Editar
                                                </a>
                                                <a href="eliminar_destino.php?id=<?= $row['id_destino'] ?>" class="btn btn-sm btn-danger me-1" style="font-weight: bold;">
                                                    <i class="bi bi-x-circle"></i> Eliminar
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No se encontraron usuarios.</td>
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
    <!-- Modal Alta Proveedor -->
    <?php include 'modal_proveedor.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/js_componentes_desino.js"></script>
</body>

</html>
<?php
$conn->close();
?>