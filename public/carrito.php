<?php
include 'header.php';
include 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
  header('Location: login.php');
  exit;
}

$id_usuario = $_SESSION['usuario_id'];
?>
<div class="contenedor-carrito">
  <h2>Mi carrito</h2>
  <div id="lista-carrito"></div>
  <h3>Total: $<span id="total"></span> USD</h3>
  <button onclick="confirmarCompra()">Confirmar compra</button>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
async function cargarCarrito() {
  const res = await fetch('carrito/ver_carrito.php');
  const items = await res.json();

  const contenedor = document.getElementById('lista-carrito');
  contenedor.innerHTML = '';
  let total = 0;

  items.forEach(item => {
    total += parseFloat(item.subtotal);
    contenedor.innerHTML += `
      <div class="item-carrito">
        <h4>${item.nombre}</h4>
        <p>Cantidad: ${item.cantidad}</p>
        <p>Precio: $${item.precio_unitario}</p>
        <p>Subtotal: $${item.subtotal}</p>
        <button onclick="eliminarItem(${item.id_item})">Eliminar</button>
        <hr>
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
  const items = document.querySelectorAll('.item-carrito');

  let y = 30;
  items.forEach(item => {
    doc.text(item.querySelector('h4').textContent, 10, y);
    doc.text(item.querySelectorAll('p')[2].textContent, 80, y);
    y += 10;
  });

  doc.text("Total: $" + document.getElementById('total').innerText, 10, y + 10);
  doc.save("ticket_nomadella.pdf");
}

cargarCarrito();
</script>

<?php include 'footer.php'; ?>
