<?php
// Conexión a la base de datos (ajusta los datos según tu configuración)
$mysqli = new mysqli("localhost", "root", "", "nomadella");
if ($mysqli->connect_errno) {
    die("Error de conexión: " . $mysqli->connect_error);
}
include 'verificar_admin.php';

// Parámetros de paginación y búsqueda
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$search = isset($_GET['search']) ? $mysqli->real_escape_string($_GET['search']) : '';
$rol = isset($_GET['rol']) ? intval($_GET['rol']) : 0;
$offset = ($page - 1) * $limit;

// Ordenar
$order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'desc' : 'asc';
$orderIcon = $order === 'asc' ? 'bi-caret-up-fill' : 'bi-caret-down-fill';

// Consulta para contar total de registros
$where = "WHERE estado='activo'";
if ($search) {
    $where .= " AND (nombre LIKE '%$search%' OR email LIKE '%$search%' OR telefono LIKE '%$search%')";
}
if ($rol) {
    $where .= " AND rol = $rol";
}
$totalQuery = $mysqli->query("SELECT COUNT(*) as total FROM usuarios $where");
$totalRows = $totalQuery->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// Consulta para obtener los usuarios
$sql = "SELECT * FROM usuarios $where ORDER BY id_usuario $order LIMIT $limit OFFSET $offset";
$result = $mysqli->query($sql);


$highlight = isset($_GET['highlight']) ? intval($_GET['highlight']) : 0;
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
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="">Usuarios</h2>
                        <button type="button" class="btn btn-primary" onclick="location.href='registro_empleado.user.php'">
                            <i class="bi bi-plus-circle"></i> Nuevo Usuario
                        </button>
                    </div>
                    <p class="text-muted">
                        Aquí puedes gestionar los usuarios de la plataforma. Puedes buscar, filtrar por rol y paginar los resultados.
                        Utiliza el botón <b>"Nuevo Usuario"</b> para agregar un nuevo usuario a la plataforma.
                    </p>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <form class="d-flex align-items-center" method="get" action="">
                            <input type="text" id="busqueda-cliente" name="search" class="form-control me-2" placeholder="Buscar cliente..." value="<?= htmlspecialchars($search) ?>">
                            <select name="rol" class="form-select me-2" style="width:auto;">
                                <option value="">Todos</option>
                                <option value="1" <?= (isset($_GET['rol']) && $_GET['rol'] == 1) ? 'selected' : '' ?>>Superadmin</option>
                                <option value="2" <?= (isset($_GET['rol']) && $_GET['rol'] == 2) ? 'selected' : '' ?>>Admin</option>
                                <option value="3" <?= (isset($_GET['rol']) && $_GET['rol'] == 3) ? 'selected' : '' ?>>Cliente</option>
                            </select>
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
                                        <a href="?search=<?= urlencode($search) ?>&rol=<?= $rol ?>&limit=<?= $limit ?>&page=<?= $page ?>&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>" class="text-decoration-none text-light">
                                            #
                                            <i class="bi <?= $orderIcon ?>"></i>
                                        </a>
                                    </th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Fecha de Registro
                                        <a href="?search=<?= urlencode($search) ?>&limit=<?= $limit ?>&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>" class="text-decoration-none">
                                            <i class="bi <?= $orderIcon ?>"></i>
                                        </a>
                                    </th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-clientes">
                                <?php if ($result && $result->num_rows > 0): ?>
                                    <?php foreach ($result as $i => $row): ?>
                                        <tr <?= ($row['id_usuario'] == $highlight) ? 'class="table-warning"' : '' ?>>

                                            <td><?= $row['id_usuario'] ?></td>
                                            <td><?= htmlspecialchars($row['nombre']) ?></td>
                                            <td><?= htmlspecialchars($row['email']) ?></td>
                                            <td><?= htmlspecialchars($row['telefono']) ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($row['fecha_registro'])) ?></td>
                                            <td>
                                                <?php if (isset($row['estado']) && $row['estado'] === 'activo'): ?>
                                                    <span class="badge bg-success">Activo</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inactivo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="editar_usuario.php?id=<?= $row['id_usuario'] ?>" class="btn btn-sm btn-primary me-1">
                                                    <i class="bi bi-pencil-square"></i> Editar
                                                </a>
                                                <?php if ($row['estado']): ?>
                                                    <button type="button" class="btn btn-sm btn-danger btn-desactivar" data-id="<?= $row['id_usuario'] ?>" data-nombre="<?= htmlspecialchars($row['nombre']) ?>">
                                                        <i class="bi bi-person-x"></i> Desactivar
                                                    </button>
                                                <?php endif; ?>
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
                    <a href="usuarios_desactivados.php" class="btn btn-outline-secondary mb-3">
                        <i class="bi bi-archive"></i> Ver usuarios desactivados
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Desactivar Usuario -->
    <div class="modal fade" id="modalDesactivarUsuario" tabindex="-1" aria-labelledby="modalDesactivarUsuarioLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
          <div class="modal-header">
            <h5 class="modal-title w-100" id="modalDesactivarUsuarioLabel">Desactivar usuario</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body py-4">
            <p id="textoModalDesactivar"></p>
            <button type="button" class="btn btn-danger px-4" id="btnConfirmarDesactivar">Desactivar</button>
            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
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
        let usuarioAEliminar = null;
        $(document).on('click', '.btn-desactivar', function() {
            usuarioAEliminar = $(this).data('id');
            let nombre = $(this).data('nombre');
            $('#textoModalDesactivar').html(`¿Está seguro que desea desactivar la cuenta de <b>${nombre}</b>?`);
            let modal = new bootstrap.Modal(document.getElementById('modalDesactivarUsuario'));
            modal.show();
        });

        $('#btnConfirmarDesactivar').on('click', function() {
            if (usuarioAEliminar) {
                $.post('desactivar_usuario.php', { id: usuarioAEliminar }, function(resp) {
                    location.reload();
                });
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php

$mysqli->close(); 
?>