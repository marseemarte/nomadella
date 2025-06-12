
<?php
include 'header.php';
?>
<style>
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
</style>
  <div class="hero-section">
  <h1>Descubrí tu próximo destino</h1>
  <form class="buscador-form" method="get" action="paquetes.php">
    <input type="text" name="busqueda" placeholder="Busca tu destino soñado...">
    <button type="submit">Buscar</button>
  </form>
</div>

  <!-- Carrusel de Paquetes Destacados -->
  <section class="paquetes-section">
    <h2>Paquetes Turísticos Destacados</h2>
    <div class="glider-contain">
      <button class="glider-prev">&#8592;</button>
      <div class="glider">
        <?php
          include 'conexion.php';
          $sql = "SELECT * FROM paquetes_turisticos WHERE activo = 1 LIMIT 12";
          $result = $conexion->query($sql);
          while ($row = $result->fetch_assoc()):
        ?>
        
          <div class="paquete-card">
            <div class="paquete-body">
              <div class="paquete-title"><?= htmlspecialchars($row['nombre']) ?></div>
              <div class="paquete-detalles">
                <?= htmlspecialchars($row['descripcion']) ?>
              </div>
              <div class="paquete-precio">$<?= number_format($row['precio_base'], 2) ?> USD</div>
              <a href="./paquetes_info.php?id=<?= $row['id_paquete'] ?>"><button class="paquete-btn">Ver detalles</button></a>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
      <button class="glider-next">&#8594;</button>
      <div role="tablist" class="glider-dots"></div>
    </div>
  </section>
  <!-- Filtros avanzados -->
<!-- Filtros avanzados -->
<section class="filtros-section">
  <form class="filtros-form">
    <div>
      <label for="precio">Precio:</label>
      <input type="range" id="precio" name="precio" min="200" max="4000" step="50" value="1500" oninput="precioOutput.value = precio.value">
      <output id="precioOutput">1500</output> USD
    </div>
    <div>
      <label for="duracion">Duración:</label>
      <select id="duracion" name="duracion">
        <option value="">Cualquier duración</option>
        <?php for($i=1;$i<=10;$i++): ?>
          <option value="<?=$i?>"><?=$i?> día<?=($i>1?'s':'')?></option>
        <?php endfor; ?>
      </select>
    </div>
    <div>
      <label for="salida">Salida desde:</label>
      <select id="salida" name="salida">
        <option value="">Cualquier ciudad</option>
        <option>Buenos Aires</option>
        <option>Córdoba</option>
        <option>Mendoza</option>
        <option>Rosario</option>
      </select>
    </div>
    <div>
      <label for="tipo">Tipo de viaje:</label>
      <select id="tipo" name="tipo">
        <option value="">Todos</option>
        <option>Playa</option>
        <option>Ciudad</option>
        <option>Montaña</option>
        <option>Aventura</option>
        <option>Familiar</option>
      </select>
    </div>
    <button type="submit" class="filtros-btn">Filtrar</button>
  </form>
  <p style="font-size:0.95em;color:#b84e6f;margin-top:8px;">
    * Todos los precios están expresados en dólares estadounidenses (USD). Consultá por formas de pago en pesos argentinos.
  </p>
</section>
<section class="opiniones-section">
  <h2>Opiniones de nuestros viajeros</h2>
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


  <?php
    include 'footer.php';
    ?>
  <!-- Carrusel JS -->
  <script src="https://cdn.jsdelivr.net/npm/glider-js@1/glider.min.js"></script>
  <script>
    window.addEventListener('load', function(){
      new Glider(document.querySelector('.glider'), {
        slidesToShow: 1,
        slidesToScroll: 1,
        draggable: true,
        dots: '.glider-dots',
        arrows: {
          prev: '.glider-prev',
          next: '.glider-next'
        },
        responsive: [
          {
            breakpoint: 700,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 1
            }
          },
          {
            breakpoint: 1000,
            settings: {
              slidesToShow: 3,
              slidesToScroll: 1
            }
          }
        ]
      });
    });
  </script>
</body>
</html>
