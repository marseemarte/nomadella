<?php
include 'conexion.php';
include 'verificar_admin.php';

// proveedores desactivados (rol=3 o el que uses para clientes)
$proveedores = $conn->query("SELECT * FROM proveedores WHERE estado='inactivo'")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>proveedores Desactivados</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="main-content">
    <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="clientes.php">proveedores</a></li>
                <li class="breadcrumb-item active" aria-current="page">proveedores Desactivados</li>
            </ol>
        </nav>
    <div>
        <div class="d-flex justify-content-between align-items-center">  
            <h2 class="mb-4">Proveedores desactivados</h2>     
            <a href="proveedores.php" class="btn btn-secondary mb-3">
                <i class="bi bi-arrow-left"></i> Volver a proveedores
            </a>  
        </div>
        <div>
            <p class="text-muted">
                Aquí se muestran los proveedores que han sido desactivados. Puedes activarlos desde aquí.
            </p>
        </div> 
    </div>

    <div class="tab-content" id="">
        <div class="tab-pane fade show active" id="proveedores" role="tabpanel">
            <table class="table table-bordered">
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
                <?php foreach ($proveedores as $u): ?>
                    <tr>
                        <td><?= $u['id_proveedor'] ?></td>
                        <td><?= htmlspecialchars($u['nombre']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= htmlspecialchars($u['telefono']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($u['fecha_registro'])) ?></td>
                        <td><span class="badge bg-secondary">Inactivo</span></td>
                        <td>
                            <button class="btn btn-success btn-sm btn-reactivar" data-id="<?= $u['id_proveedor'] ?>" data-nombre="<?= htmlspecialchars($u['nombre']) ?>">
                                <i class="bi bi-person-check"></i> Reactivar
                            </button>
                        </td>
                    </tr>
                <?php endforeach; if (empty($proveedores)): ?>
                    <tr><td colspan="4" class="text-center">No hay proveedores desactivados.</td></tr>
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
    $('#textoModalReactivar').html('¿Está seguro que desea reactivar al proveedor de <b>' + nombre + '</b>?');
    let modal = new bootstrap.Modal(document.getElementById('modalReactivar'));
    modal.show();
});
$('#btnConfirmarReactivar').on('click', function() {
    if (usuarioAReactivar) {
        $.post('reactivar_proveedor.php', { id: usuarioAReactivar }, function(resp) {
            location.reload();
        });
    }
});
</script>
</body>
</html>