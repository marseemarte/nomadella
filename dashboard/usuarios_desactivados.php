<?php
include 'conexion.php';
include 'verificar_admin.php';

// Empleados desactivados (rol=2)
$empleados = $conn->query("SELECT * FROM usuarios WHERE estado='inactivo' AND rol=2")->fetch_all(MYSQLI_ASSOC);
// Usuarios desactivados (rol=3 o el que uses para clientes)
$usuarios = $conn->query("SELECT * FROM usuarios WHERE estado='inactivo' AND rol=3")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios Desactivados</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="main-content">
    <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="clientes.php">Usuarios</a></li>
                <li class="breadcrumb-item active" aria-current="page">Usuarios Desactivados</li>
            </ol>
        </nav>
    <h2 class="mb-4">Usuarios desactivados</h2>
    <ul class="nav nav-tabs mb-3" id="tabUsuarios" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="empleados-tab" data-bs-toggle="tab" data-bs-target="#empleados" type="button" role="tab">Empleados</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="usuarios-tab" data-bs-toggle="tab" data-bs-target="#usuarios" type="button" role="tab">Usuarios</button>
        </li>
    </ul>
    <div class="tab-content" id="tabUsuariosContent">
        <div class="tab-pane fade show active" id="empleados" role="tabpanel">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Fecha de Registro</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($empleados as $i => $e): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($e['nombre']) ?></td>
                        <td><?= htmlspecialchars($e['email']) ?></td>
                        <td><?= htmlspecialchars($e['telefono']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($e['fecha_registro'])) ?></td>
                        <td><span class="badge bg-secondary">Inactivo</span></td>
                        <td>
                            <a href="editar_usuario.php?id=<?= $e['id_usuario'] ?>" class="btn btn-sm btn-primary me-1">
                                <i class="bi bi-pencil-square"></i> Editar
                            </a>
                            <button class="btn btn-success btn-sm btn-reactivar" data-id="<?= $e['id_usuario'] ?>" data-nombre="<?= htmlspecialchars($e['nombre']) ?>">
                                <i class="bi bi-person-check"></i> Reactivar
                            </button>
                        </td>
                    </tr>
                <?php endforeach; if (empty($empleados)): ?>
                    <tr><td colspan="7" class="text-center">No hay empleados desactivados.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="tab-pane fade" id="usuarios" role="tabpanel">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nombre</th><th>Email</th><th>Teléfono</th><th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['nombre']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= htmlspecialchars($u['telefono']) ?></td>
                        <td>
                            <button class="btn btn-success btn-sm btn-reactivar" data-id="<?= $u['id_usuario'] ?>">
                                <i class="bi bi-person-check"></i> Reactivar
                            </button>
                        </td>
                    </tr>
                <?php endforeach; if (empty($usuarios)): ?>
                    <tr><td colspan="4" class="text-center">No hay usuarios desactivados.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de confirmación -->
<div class="modal fade" id="modalReactivar" tabindex="-1" aria-labelledby="modalReactivarLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center">
      <div class="modal-header">
        <h5 class="modal-title w-100" id="modalReactivarLabel">Reactivar cuenta</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body py-4">
        <p id="textoModalReactivar"></p>
        <button type="button" class="btn btn-success px-4" id="btnConfirmarReactivar">Reactivar</button>
        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
let usuarioAReactivar = null;
$(document).on('click', '.btn-reactivar', function() {
    usuarioAReactivar = $(this).data('id');
     let nombre = $(this).data('nombre');
    $('#textoModalReactivar').html('¿Está seguro que desea reactivar la cuenta de <b>' + nombre + '</b>?');
    let modal = new bootstrap.Modal(document.getElementById('modalReactivar'));
    modal.show();
});
$('#btnConfirmarReactivar').on('click', function() {
    if (usuarioAReactivar) {
        $.post('reactivar_usuario.php', { id: usuarioAReactivar }, function(resp) {
            location.reload();
        });
    }
});
</script>
</body>
</html>