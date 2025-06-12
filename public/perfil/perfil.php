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

<div class="perfil-container">
  <h2>Mi perfil</h2>
  <form id="form-perfil">
    <label>Nombre:</label>
    <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
    <label>Apellido:</label>
    <input type="text" name="apellido" value="<?= htmlspecialchars($usuario['apellido']) ?>" required>
    <label>Email:</label>
    <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>
    <button type="submit">Guardar cambios</button>
  </form>
  <div id="respuesta"></div>

  <hr>
  <h3>Mis reservas</h3>
  <div id="reservas"></div>
</div>

<script>
document.getElementById('form-perfil').addEventListener('submit', async (e) => {
  e.preventDefault();
  const datos = new FormData(e.target);
  const res = await fetch('actualizar_datos.php', {
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
    cont.innerHTML = '<p>No tenés reservas aún.</p>';
    return;
  }

  data.forEach(r => {
    const div = document.createElement('div');
    div.innerHTML = `<strong>Orden #${r.id_orden}</strong> - ${r.fecha_orden} - Total: $${r.total} USD - Estado: ${r.estado}<hr>`;
    cont.appendChild(div);
  });
}

cargarReservas();
</script>

<?php include '../footer.php'; ?>
