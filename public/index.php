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
  /* Flechas Swiper */
.swiper-button-next,
.swiper-button-prev {
    color: #741d41; /* color vino oscuro */
}

/* Paginación (bullets) Swiper */
.swiper-pagination-bullet {
    background: #b84e6f; /* rosa fuerte */
    opacity: 0.5; /* para los no activos */
}

.swiper-pagination-bullet-active {
    background: #741d41; /* activo en vino oscuro */
    opacity: 1;
}
.swiper-pagination-bullet {
    width: 12px;
    height: 12px;
    transition: transform 0.3s ease;
}

.swiper-pagination-bullet-active {
    transform: scale(1.2);
}
</style>

<div class="hero-section">
  <h1>Descubrí tu próximo destino</h1>
  <form class="buscador-form" method="get" action="paquetes.php">
    <input type="text" name="busqueda" placeholder="Busca tu destino soñado..." class="form-control border-0 shadow-none">
    <button type="submit">Buscar</button>
  </form>
</div>

<!-- Carrusel de Paquetes Destacados -->
<section class="container my-5">
  <h2 class="text-center mb-4" style="color:#741d41;">Paquetes Turísticos Destacados</h2>
  
  <div class="swiper mySwiper">
    <div class="swiper-wrapper">
      <?php
        include 'conexion.php';
        $sql = "SELECT * FROM paquetes_turisticos WHERE activo = 1 LIMIT 9";
        $result = $conexion->query($sql);
        while ($row = $result->fetch_assoc()):
      ?>
      <div class="swiper-slide">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title" style="color:#b84e6f;"><?= htmlspecialchars($row['nombre']) ?></h5>
            <p class="card-text"><?= htmlspecialchars($row['descripcion']) ?></p>
            <div class="fw-bold mb-2" style="color:#741d41;">$<?= number_format($row['precio_base'], 2) ?> USD</div>
            <a href="./paquetes_info.php?id=<?= $row['id_paquete'] ?>" class="btn btn-primary w-100" style="background:linear-gradient(90deg,#741d41 60%,#b84e6f 100%);border:none;">Ver detalles</a>
          </div>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
    
    <!-- Flechas -->
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>

    <!-- Paginación (opcional) -->
     <br>
     <br>
    <div class="swiper-pagination"></div>
  </div>
</section>


<!-- Filtros avanzados -->
<section class="container filtros-section my-5">
  <form class="row g-3 filtros-form align-items-end">
    <div class="col-12 col-md-3">
      <label for="precio" class="form-label">Precio:</label>
      <input type="range" id="precio" name="precio" min="200" max="4000" step="50" value="1500" class="form-range" oninput="precioOutput.value = precio.value">
      <output id="precioOutput" class="ms-2">1500</output> USD
    </div>
    <div class="col-12 col-md-3">
      <label for="duracion" class="form-label">Duración:</label>
      <select id="duracion" name="duracion" class="form-select">
        <option value="">Cualquier duración</option>
        <?php for($i=1;$i<=10;$i++): ?>
          <option value="<?=$i?>"><?=$i?> día<?=($i>1?'s':'')?></option>
        <?php endfor; ?>
      </select>
    </div>
    <div class="col-12 col-md-3">
      <label for="salida" class="form-label">Salida desde:</label>
      <select id="salida" name="salida" class="form-select">
        <option value="">Cualquier ciudad</option>
        <option>Buenos Aires</option>
        <option>Córdoba</option>
        <option>Mendoza</option>
        <option>Rosario</option>
      </select>
    </div>
    <div class="col-12 col-md-3">
      <label for="tipo" class="form-label">Tipo de viaje:</label>
      <select id="tipo" name="tipo" class="form-select">
        <option value="">Todos</option>
        <option>Playa</option>
        <option>Ciudad</option>
        <option>Montaña</option>
        <option>Aventura</option>
        <option>Familiar</option>
      </select>
    </div>
    <div class="col-12 text-end">
      <button type="submit" class="btn filtros-btn">Filtrar</button>
    </div>
  </form>
  <p class="mt-2" style="font-size:0.95em;color:#b84e6f;">
    * Todos los precios están expresados en dólares estadounidenses (USD). Consultá por formas de pago en pesos argentinos.
  </p>
</section>

<!-- Opiniones de clientes -->
<section class="container opiniones-section">
  <h2 class="text-center mb-4">Opiniones de nuestros viajeros</h2>
  <div class="row justify-content-center opiniones-grid">
    <div class="col-12 col-md-4 mb-3">
      <div class="opinion-card d-flex align-items-start">
        <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Cliente" class="opinion-avatar me-3">
        <div class="opinion-body">
          <div class="opinion-nombre">María G.</div>
          <div class="opinion-estrellas">★★★★★</div>
          <div class="opinion-texto">“Estuve en Cancún y todo fue como lo prometieron. ¡Volvería a reservar con Nomadella!”</div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-4 mb-3">
      <div class="opinion-card d-flex align-items-start">
        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Cliente" class="opinion-avatar me-3">
        <div class="opinion-body">
          <div class="opinion-nombre">Carlos P.</div>
          <div class="opinion-estrellas">★★★★☆</div>
          <div class="opinion-texto">“La atención fue excelente y el hotel superó mis expectativas. ¡Gracias!”</div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-4 mb-3">
      <div class="opinion-card d-flex align-items-start">
        <img src="https://randomuser.me/api/portraits/women/65.jpg" alt="Cliente" class="opinion-avatar me-3">
        <div class="opinion-body">
          <div class="opinion-nombre">Lucía R.</div>
          <div class="opinion-estrellas">★★★★★</div>
          <div class="opinion-texto">“Viajamos en familia y los niños la pasaron increíble. Todo muy organizado.”</div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include 'footer.php'; ?>
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
  <script>
  var swiper = new Swiper(".mySwiper", {
    slidesPerView: 1,
    spaceBetween: 30,
    loop: true,
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    breakpoints: { // Responsive
      768: {
        slidesPerView: 2,
      },
      1024: {
        slidesPerView: 3,
      }
    }
  });
</script>
</body>
</html>
