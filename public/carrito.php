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
    <form id="formPago" method="post" autocomplete="off">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalPagoLabel">Datos de Pago</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="nombreCompleto" class="form-label">Nombre completo</label>
            <input type="text" class="form-control" id="nombreCompleto" name="nombreCompleto" required pattern="^[A-Za-z\s]{3,100}$" title="Ingrese un nombre válido (solo letras)">
          </div>
          <div class="mb-3">
            <label for="dniCuit" class="form-label">DNI o CUIT</label>
            <input type="text" class="form-control" id="dniCuit" name="dniCuit" inputmode="numeric" required pattern="^\d{7,8}$|^\d{11}$" maxlength="11" minlength="7" title="Ingrese un DNI válido (7-8 dígitos) o CUIT válido (11 dígitos)">
          </div>
          <div class="mb-3">
            <label for="medioPago" class="form-label">Medio de pago</label>
            <select class="form-select" id="medioPago" name="medioPago" required>
              <option value="">Seleccionar...</option>
              <option value="Tarjeta de Crédito">Tarjeta de Crédito</option>
              <option value="Tarjeta de Débito">Tarjeta de Débito</option>
              <option value="Transferencia Bancaria">Transferencia Bancaria</option>
              <option value="Mercado Pago">Mercado Pago</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="numeroTarjeta" class="form-label">Número de tarjeta</label>
            <input type="text" class="form-control" id="numeroTarjeta" name="numeroTarjeta" inputmode="numeric" pattern="^\d{13,19}$" maxlength="19" required title="Debe ingresar entre 13 y 19 dígitos">
          </div>
          <div class="row">
            <div class="col-6 mb-3">
              <label for="vencimiento" class="form-label">Vencimiento</label>
              <input type="text" class="form-control" id="vencimiento" name="vencimiento" placeholder="MM/AA">
            </div>
            <div class="col-6 mb-3">
              <label for="codigoSeguridad" class="form-label">Código de seguridad</label>
              <input type="text" class="form-control" id="codigoSeguridad" name="codigoSeguridad" maxlength="4">
            </div>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email de contacto</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>
          <div class="mb-3">
            <label for="direccionFacturacion" class="form-label">Dirección de facturación</label>
            <input type="text" class="form-control" id="direccionFacturacion" name="direccionFacturacion" maxlength="200">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary" id="btnConfirmarPago">Confirmar pago</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div id="resultado" class="my-4"></div>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const dniCuitInput = document.getElementById('dniCuit');
  dniCuitInput.addEventListener('input', () => {
    dniCuitInput.value = dniCuitInput.value.replace(/\D/g, '');
  });

  const medioPago = document.getElementById('medioPago');
  const tarjetaInput = document.getElementById('numeroTarjeta');
  const vencimientoInput = document.getElementById('vencimiento');
  const cvvInput = document.getElementById('codigoSeguridad');

  function actualizarRestricciones() {
    const valor = medioPago.value;
    if (valor === 'Tarjeta de Crédito' || valor === 'Tarjeta de Débito') {
      tarjetaInput.type = 'text';
      tarjetaInput.placeholder = 'Ej: 1234567812345678';
      tarjetaInput.maxLength = 19;
      tarjetaInput.required = true;
      tarjetaInput.pattern = '\\d{13,19}';
      vencimientoInput.parentElement.style.display = '';
      vencimientoInput.required = true;
      cvvInput.parentElement.style.display = '';
      cvvInput.required = true;
      cvvInput.maxLength = 4;
      cvvInput.pattern = '\\d{3,4}';
    } else {
      tarjetaInput.type = 'text';
      tarjetaInput.placeholder = 'CBU o alias';
      tarjetaInput.maxLength = 22;
      tarjetaInput.required = true;
      tarjetaInput.pattern = '.{4,22}';
      vencimientoInput.parentElement.style.display = 'none';
      vencimientoInput.required = false;
      cvvInput.parentElement.style.display = 'none';
      cvvInput.required = false;
    }
  }

  medioPago.addEventListener('change', actualizarRestricciones);
  actualizarRestricciones();
});

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
      </div>`;
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
      </div>`;
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
    actualizarBadgeCarrito();
  } else {
    alert('Error al eliminar');
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
  document.getElementById('formPago').reset();
  const modal = new bootstrap.Modal(document.getElementById('modalPago'));
  modal.show();
}

document.getElementById('formPago').addEventListener('submit', async function(e) {
  e.preventDefault();
  const form = e.target;
  const datos = new FormData(form);
  document.getElementById('btnConfirmarPago').disabled = true;
  const res = await fetch('order/crear_orden.php', {
    method: 'POST',
    body: datos
  });
  const data = await res.json();
  document.getElementById('btnConfirmarPago').disabled = false;
  if (data.success) {
    bootstrap.Modal.getInstance(document.getElementById('modalPago')).hide();
    document.getElementById('lista-carrito').innerHTML = data.mensaje;
    document.getElementById('total').innerText = '0.00';
    document.querySelector('h4.text-end').style.display = 'none';
    const botonConfirmarCompra = document.querySelector('button[onclick="abrirModalPago()"]');
    if (botonConfirmarCompra) botonConfirmarCompra.style.display = 'none';
    generarPDF();
    actualizarBadgeCarrito?.();
  } else {
    alert('Error al confirmar: ' + (data.mensaje || 'Error desconocido'));
  }
});

cargarCarrito();
</script>

<?php include 'footer.php'; ?>