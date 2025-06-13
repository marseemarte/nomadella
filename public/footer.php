<footer class="bg-light py-3 mt-5 border-top">
  <div class="container d-flex justify-content-between align-items-center">
    <span class="text-muted">&copy; Nomadella 2025</span>
    <span class="text-muted">Términos | Política | FAQ</span>
  </div>
</footer>
<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
async function actualizarBadgeCarrito() {
  try {
    const res = await fetch('carrito/contar_items.php');
    const cantidad = parseInt(await res.text());
    const badge = document.getElementById('carrito-badge');
    if (badge) {
      if (cantidad > 0) {
        badge.textContent = cantidad;
        badge.style.display = 'inline-block';
      } else {
        badge.style.display = 'none';
      }
    }
  } catch(e) {}
}

// Llama al cargar la página
actualizarBadgeCarrito();

// Si usas SPA o AJAX para agregar al carrito, llama a actualizarBadgeCarrito() después de agregar
</script>
