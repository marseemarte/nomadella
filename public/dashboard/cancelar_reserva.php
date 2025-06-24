<?php
include 'conexion.php';
include 'verificar_admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_orden'])) {
    $id_orden = intval($_POST['id_orden']);

    // Cancelar la reserva
    $conn->query("UPDATE ordenes SET estado='Cancelada' WHERE id_orden = $id_orden");

    // Obtener id_usuario
    $res = $conn->query("SELECT id_usuario FROM ordenes WHERE id_orden = $id_orden");
    $row = $res->fetch_assoc();
    $id_usuario = $row['id_usuario'] ?? 0;

    // Notificación
    $mensaje_notif = "Su reserva #$id_orden ha sido cancelada por el administrador.";
    $mensaje_notif_sql = $conn->real_escape_string($mensaje_notif);
    $conn->query("INSERT INTO notificaciones (id_usuario, mensaje, tipo, leido, fecha) VALUES ($id_usuario, '$mensaje_notif_sql', 'cancelacion', 0, NOW())");

    // Bitácora
    if (isset($_SESSION['id_usuario'])) {
        registrar_bitacora(
            $pdo,
            $_SESSION['id_usuario'],
            'Cancelar reserva',
            "Reserva #$id_orden cancelada."
        );
    }

    header("Location: reservas.php?cancelada=1");
    exit;
}
?>

<!-- Modal de confirmación -->
<div class="modal fade" id="modalCancelarReserva" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Cancelar Reserva</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        ¿Estás seguro de que deseas cancelar esta reserva?
        <p>Se notificará al cliente del cambio de estado.</p>
      </div>
      <div class="modal-footer">
        <form method="post" id="form-cancelar">
          <input type="hidden" name="id_orden" id="cancelar-id">
          <button type="submit" class="btn btn-danger">Sí, cancelar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
function confirmarCancelacion(id) {
    document.getElementById('cancelar-id').value = id;
    const modal = new bootstrap.Modal(document.getElementById('modalCancelarReserva'));
    modal.show();
}
</script>
