<?php
include 'header.php';
include 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
  header('Location: login.php');
  exit;
}

$id_usuario = $_SESSION['usuario_id'];
?>

<div class="container my-5">
  <div class="card shadow-sm">
    <div class="card-header bg-gradient text-white" style="background:linear-gradient(90deg, #741d41 60%, #b84e6f 100%)">
      <h3 class="mb-0 text-center">🛒 Mi Carrito</h3>
    </div>
    <div class="card-body">
      <div id="lista-carrito"></div>
      <hr>
      <h4 class="text-end">Total: $<span id="total">0.00</span> USD</h4>
      <div class="d-grid gap-2 mt-4">
        <button class="btn btn-primary" onclick="abrirModalPago()">
          <i class="bi bi-bag-check"></i> Confirmar compra
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal de Pago -->
<div class="modal fade" id="modalPago" tabindex="-1" aria-labelledby="modalPagoLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" id="formPago" onsubmit="return false;">
      <div class="modal-header">
        <h5 class="modal-title" id="modalPagoLabel">Datos de Pago</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="nombreTitular" class="form-label">Nombre del titular</label>
          <input type="text" class="form-control" id="nombreTitular" required>
        </div>
        <div class="mb-3">
          <label for="numeroTarjeta" class="form-label">Número de tarjeta</label>
          <input type="text" class="form-control" id="numeroTarjeta" maxlength="19" required>
        </div>
        <div class="row">
          <div class="col-6 mb-3">
            <label for="vencimiento" class="form-label">Vencimiento</label>
            <input type="text" class="form-control" id="vencimiento" placeholder="MM/AA" maxlength="5" required>
          </div>
          <div class="col-6 mb-3">
            <label for="cvv" class="form-label">CVV</label>
            <input type="text" class="form-control" id="cvv" maxlength="4" required>
          </div>
        </div>
        <div class="mb-3">
          <label for="emailPago" class="form-label">Email para recibir el ticket</label>
          <input type="email" class="form-control" id="emailPago" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary" id="btnConfirmarPago">Confirmar y pagar</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
async function cargarCarrito() {
  const res = await fetch('carrito/ver_carrito.php');
  const items = await res.json();

  const contenedor = document.getElementById('lista-carrito');
  contenedor.innerHTML = '';
  let total = 0;

  if(items.length === 0){
    contenedor.innerHTML = `
      <div class="alert alert-info text-center" role="alert">
        Tu carrito está vacío.
      </div>
    `;
    document.getElementById('total').innerText = '0.00';
    return;
  }

  items.forEach(item => {
    total += parseFloat(item.subtotal);
    contenedor.innerHTML += `
      <div class="card mb-3 shadow-sm">
        <div class="card-body">
          <h5 class="card-title text-primary">${item.nombre}</h5>
          <p class="card-text mb-1"><strong>Cantidad:</strong> ${item.cantidad}</p>
          <p class="card-text mb-1"><strong>Precio unitario:</strong> $${item.precio_unitario} USD</p>
          <p class="card-text"><strong>Subtotal:</strong> $${item.subtotal} USD</p>
          <button class="btn btn-outline-danger btn-sm" onclick="eliminarItem(${item.id_item})">
            <i class="bi bi-trash"></i> Eliminar
          </button>
        </div>
      </div>
    `;
  });

  document.getElementById('total').innerText = total.toFixed(2);
}

async function eliminarItem(id) {
  const res = await fetch('carrito/eliminar_item.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'id_item=' + id
  });
  const texto = await res.text();
  if (texto === 'ok') {
    cargarCarrito();
    actualizarBadgeCarrito(); // <--- Agrega esta línea
  } else {
    alert('Error al eliminar');
  }
}

async function confirmarCompra() {
  const res = await fetch('order/crear_orden.php');
  const texto = await res.text();
  if (texto === 'Orden creada correctamente') {
    alert('Compra realizada. Se descargará el ticket.');
    generarPDF();
    cargarCarrito();
  } else {
    alert('Error al confirmar: ' + texto);
  }
}

function generarPDF() {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();
  const fecha = new Date().toLocaleString();

  doc.text("Ticket de compra - Nomadella", 10, 10);
  doc.text("Fecha: " + fecha, 10, 20);
  const items = document.querySelectorAll('.card');

  let y = 30;
  items.forEach(item => {
    const title = item.querySelector('.card-title')?.textContent;
    const subtotal = item.querySelectorAll('.card-text')[2]?.textContent;
    if(title && subtotal){
      doc.text(title, 10, y);
      doc.text(subtotal, 80, y);
      y += 10;
    }
  });

  doc.text("Total: $" + document.getElementById('total').innerText, 10, y + 10);
  doc.save("ticket_nomadella.pdf");
}

function abrirModalPago() {
  // Limpia el formulario
  document.getElementById('formPago').reset();
  const modal = new bootstrap.Modal(document.getElementById('modalPago'));
  modal.show();
}

document.getElementById('formPago').addEventListener('submit', async function() {
  // Validación básica (puedes mejorarla)
  const nombre = document.getElementById('nombreTitular').value.trim();
  const tarjeta = document.getElementById('numeroTarjeta').value.trim();
  const venc = document.getElementById('vencimiento').value.trim();
  const cvv = document.getElementById('cvv').value.trim();
  const email = document.getElementById('emailPago').value.trim();

  if (!nombre || !tarjeta || !venc || !cvv || !email) {
    alert('Completa todos los campos.');
    return false;
  }

  // Deshabilita el botón para evitar doble envío
  document.getElementById('btnConfirmarPago').disabled = true;

  // Llama al backend para crear la orden y enviar el mail
  const res = await fetch('order/crear_orden.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: `email=${encodeURIComponent(email)}`
  });
  const texto = await res.text();

  document.getElementById('btnConfirmarPago').disabled = false;

  if (texto === 'Orden creada correctamente') {
    bootstrap.Modal.getInstance(document.getElementById('modalPago')).hide();
    alert('Compra realizada. Recibirás el ticket por email.');
    cargarCarrito();
    actualizarBadgeCarrito();
  } else {
    alert('Error al confirmar: ' + texto);
  }
});

cargarCarrito();
</script>

<?php include 'footer.php'; ?>
