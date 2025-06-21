<footer class="bg-light py-4 mt-5 border-top">
  <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center text-center text-md-start">
    <div class="mb-2 mb-md-0">
      <span class="text-muted">&copy; 2025 Nomadella. Todos los derechos reservados.</span>
    </div>
    <div>
      <a href="terminos.php" class="text-muted text-decoration-none me-3">Términos y Condiciones</a>
      <a href="privacidad.php" class="text-muted text-decoration-none me-3">Política de Privacidad</a>
      <a href="faq.php" class="text-muted text-decoration-none">Preguntas Frecuentes</a>
    </div>
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
  } catch(e) {
    console.error('Error al actualizar el carrito:', e);
  }
}

actualizarBadgeCarrito();
</script>
