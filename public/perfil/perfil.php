<?php
include '../conexion.php';
include '../header.php';

if (!isset($_SESSION['usuario_id'])) {
  header('Location: ../login.php');
  exit;
}

$id = $_SESSION['usuario_id'];
$sql = "SELECT nombre, apellido, email FROM usuarios WHERE id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$usuario = $res->fetch_assoc();
?>

<div class="container my-5">
  <div class="card shadow-lg border-0 rounded-4">
    <div class="card-body p-5">
      <h2 class="text-center mb-4" style="color: #a63e62;">Mi perfil</h2>
      <form id="form-perfil">
        <div class="mb-3">
          <label class="form-label fw-semibold text-dark">Nombre:</label>
          <input type="text" name="nombre" class="form-control rounded-3" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold text-dark">Apellido:</label>
          <input type="text" name="apellido" class="form-control rounded-3" value="<?= htmlspecialchars($usuario['apellido']) ?>" required>
        </div>
        <div class="mb-4">
          <label class="form-label fw-semibold text-dark">Email:</label>
          <input type="email" name="email" class="form-control rounded-3" value="<?= htmlspecialchars($usuario['email']) ?>" required>
        </div>
        <div class="d-grid">
          <button type="submit" class="btn text-white fw-semibold" style="background: linear-gradient(to right, #a63e62, #732350);">
            Guardar cambios
          </button>
        </div>
      </form>

      <div id="respuesta" class="mt-3 text-success fw-bold text-center"></div>

      
    </div>
  </div>
</div>

<script>
document.getElementById('form-perfil').addEventListener('submit', async (e) => {
  e.preventDefault();
  const datos = new FormData(e.target);
  const res = await fetch('perfil/actualizar_datos.php', {
    method: 'POST',
    body: datos
  });
  const txt = await res.text();
  document.getElementById('respuesta').innerText = txt;
});

async function cargarReservas() {
  const res = await fetch('ver_reservas.php');
  const data = await res.json();
  const cont = document.getElementById('reservas');
  if (data.length === 0) {
    cont.innerHTML = '<p class="text-muted">No tenés reservas aún.</p>';
    return;
  }

  data.forEach(r => {
    const div = document.createElement('div');
    div.className = "alert alert-light border-start border-4 border-primary";
    div.innerHTML = `<strong>Orden #${r.id_orden}</strong> - ${r.fecha_orden} - Total: $${r.total} USD<br><span class="badge bg-info text-dark">${r.estado}</span>`;
    cont.appendChild(div);
  });
}

cargarReservas();

</script>

<?php include '../footer.php'; ?>
