<?php
include 'header.php';
?>
<div class="hero-section">
  <h1>Descubrí tu próximo destino</h1>
  <form class="buscador-form" method="get" action="paquetes.php">
    <input type="text" name="busqueda" placeholder="Busca tu destino soñado...">
    <button type="submit">Buscar</button>
  </form>
</div>

<section class="paquetes-section">
  <h2>Paquetes Turísticos Destacados</h2>
  <div class="paquetes-grid">
    <?php
      include 'conexion.php';
      $sql = "SELECT * FROM paquetes_turisticos WHERE activo = 1 ORDER BY RAND() LIMIT 6";
      $result = $conexion->query($sql);
      while ($row = $result->fetch_assoc()):
    ?>
      <div class="paquete-card">
        <?php if (!empty($row['imagen_destacada'])): ?>
          <img class="paquete-img" src="<?= htmlspecialchars($row['imagen_destacada']) ?>" alt="Imagen del paquete">
        <?php endif; ?>
        <div class="paquete-body">
          <div class="paquete-title"><?= htmlspecialchars($row['nombre']) ?></div>
          <div class="paquete-detalles"><?= htmlspecialchars($row['descripcion']) ?></div>
          <div class="paquete-precio">$<?= number_format($row['precio_base'], 2) ?> USD</div>
          <a href="./paquetes_info.php?id=<?= $row['id_paquete'] ?>" class="paquete-btn">Ver detalles</a>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</section>

<section class="opiniones-section">
  <h2>Lo que dicen nuestros viajeros</h2>
  <div class="opiniones-grid">
    <div class="opinion-card">
      <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Cliente" class="opinion-avatar">
      <div class="opinion-body">
        <div class="opinion-nombre">María G.</div>
        <div class="opinion-estrellas">★★★★★</div>
        <div class="opinion-texto">“Estuve en Cancún y todo fue como lo prometieron. ¡Volvería a reservar con Nomadella!”</div>
      </div>
    </div>
    <div class="opinion-card">
      <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Cliente" class="opinion-avatar">
      <div class="opinion-body">
        <div class="opinion-nombre">Carlos P.</div>
        <div class="opinion-estrellas">★★★★☆</div>
        <div class="opinion-texto">“La atención fue excelente y el hotel superó mis expectativas. ¡Gracias!”</div>
      </div>
    </div>
    <div class="opinion-card">
      <img src="https://randomuser.me/api/portraits/women/65.jpg" alt="Cliente" class="opinion-avatar">
      <div class="opinion-body">
        <div class="opinion-nombre">Lucía R.</div>
        <div class="opinion-estrellas">★★★★★</div>
        <div class="opinion-texto">“Viajamos en familia y los niños la pasaron increíble. Todo muy organizado.”</div>
      </div>
    </div>
  </div>
</section>

<?php include 'footer.php'; ?>

<style>
body {
  background: #f8f8f4;
  font-family: 'Montserrat', Arial, sans-serif;
  margin: 0;
}
.hero-section {
  background: linear-gradient(90deg, #b84e6f 60%, #741d41 100%);
  color: #fff;
  text-align: center;
  padding: 48px 0 36px 0;
}
.hero-section h1 {
  font-size: 2.3em;
  margin-bottom: 18px;
  font-weight: 700;
  letter-spacing: 1px;
}
.buscador-form {
  display: flex;
  justify-content: center;
  gap: 0;
  max-width: 480px;
  margin: 0 auto;
}
.buscador-form input[type="text"] {
  flex: 1;
  padding: 14px 16px;
  border-radius: 8px 0 0 8px;
  border: none;
  font-size: 1.1em;
  outline: none;
}
.buscador-form button {
  background: #fff;
  color: #b84e6f;
  border: none;
  padding: 14px 32px;
  border-radius: 0 8px 8px 0;
  font-size: 1.1em;
  font-weight: 700;
  cursor: pointer;
  transition: background 0.2s, color 0.2s;
}
.buscador-form button:hover {
  background: #b84e6f;
  color: #fff;
}

.paquetes-section {
  max-width: 1200px;
  margin: 48px auto 0 auto;
  text-align: center;
}
.paquetes-section h2 {
  color: #b84e6f;
  margin-bottom: 32px;
  font-size: 2em;
}
.paquetes-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 32px;
  justify-content: center;
}
.paquete-card {
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 2px 10px #0001;
  padding: 24px 24px 32px 24px;
  min-width: 320px;
  max-width: 340px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  align-items: center;
  transition: box-shadow 0.2s;
}
.paquete-card:hover {
  box-shadow: 0 4px 24px #b84e6f22;
}
.paquete-img {
  width: 100%;
  max-width: 280px;
  height: 160px;
  object-fit: cover;
  border-radius: 12px;
  margin-bottom: 18px;
  background: #eee;
}
.paquete-title {
  font-size: 1.15em;
  font-weight: 700;
  color: #b84e6f;
  margin-bottom: 8px;
  text-align: center;
}
.paquete-detalles {
  color: #444;
  font-size: 1em;
  margin-bottom: 10px;
  min-height: 48px;
}
.paquete-precio {
  font-size: 1.15em;
  color: #fff;
  background: linear-gradient(90deg, #b84e6f 60%, #741d41 100%);
  padding: 12px 0;
  border-radius: 10px;
  text-align: center;
  font-family: 'Montserrat', Arial, sans-serif;
  font-weight: 700;
  margin: 16px 0 10px 0;
  letter-spacing: 1px;
  width: 100%;
  box-shadow: 0 2px 8px rgba(116,29,65,0.08);
}
.paquete-btn {
  background: #b84e6f;
  color: #fff;
  padding: 12px 0;
  border: none;
  border-radius: 8px;
  font-size: 1.08em;
  cursor: pointer;
  text-align: center;
  text-decoration: none;
  display: block;
  font-weight: 700;
  width: 100%;
  margin-top: 8px;
  transition: background 0.2s;
}
.paquete-btn:hover {
  background: #741d41;
}

.opiniones-section {
  max-width: 1100px;
  margin: 64px auto 0 auto;
  text-align: center;
}
.opiniones-section h2 {
  color: #b84e6f;
  margin-bottom: 32px;
  font-size: 2em;
}
.opiniones-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 32px;
  justify-content: center;
}
.opinion-card {
  background: #fff;
  border-radius: 14px;
  box-shadow: 0 2px 10px #0001;
  padding: 24px 24px 24px 24px;
  min-width: 260px;
  max-width: 320px;
  display: flex;
  align-items: flex-start;
  gap: 18px;
}
.opinion-avatar {
  width: 54px;
  height: 54px;
  border-radius: 50%;
  object-fit: cover;
  margin-top: 6px;
}
.opinion-body {
  text-align: left;
}
.opinion-nombre {
  font-weight: 700;
  color: #b84e6f;
  margin-bottom: 2px;
}
.opinion-estrellas {
  color: #ffd700;
  font-size: 1.1em;
  margin-bottom: 4px;
}
.opinion-texto {
  color: #444;
  font-size: 1em;
}
@media (max-width: 900px) {
  .paquetes-section, .opiniones-section { max-width: 98vw; }
  .paquetes-grid, .opiniones-grid { gap: 18px; }
  .paquete-card, .opinion-card { min-width: 220px; max-width: 98vw; }
  .paquete-img { max-width: 98vw; }
}
@media (max-width: 600px) {
  .hero-section { padding: 28px 0 18px 0; }
  .paquetes-section h2, .opiniones-section h2 { font-size: 1.2em; }
  .paquete-card, .opinion-card { padding: 12px 4vw; }
  .paquete-img { height: 120px; }
}
</style>
</body>
</html>
